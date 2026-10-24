<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaPixelService
{
    private const GRAPH_API_VERSION = 'v26.0';

    protected string $pixelId;
    protected string $accessToken;
    protected ?string $testEventCode;

    public function __construct()
    {
        $this->pixelId = Setting::getValue('meta_pixel_id', '');
        $this->accessToken = Setting::getValue('meta_conversion_api_token', '');
        $this->testEventCode = Setting::getValue('meta_test_event_code', '');
    }

    public function isEnabled(): bool
    {
        return ctype_digit($this->pixelId);
    }

    public function isConversionApiEnabled(): bool
    {
        return $this->isEnabled() && $this->accessToken !== '';
    }

    public function sendEvent(
        string $eventName,
        array $customData = [],
        array $userData = [],
        ?string $eventId = null
    ): void
    {
        if (!$this->isConversionApiEnabled()) {
            return;
        }

        $request = request();
        $userData = array_merge(array_filter([
            'client_ip_address' => $request->ip(),
            'client_user_agent' => $request->userAgent(),
            'fbp' => $request->cookie('_fbp'),
            'fbc' => $request->cookie('_fbc'),
        ], fn ($value) => is_string($value) && $value !== ''), $userData);

        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => time(),
                    'event_source_url' => $request->url(),
                    'action_source' => 'website',
                    'user_data' => $userData,
                    'custom_data' => $customData,
                ],
            ],
            'access_token' => $this->accessToken,
        ];

        if ($eventId) {
            $payload['data'][0]['event_id'] = $eventId;
        }

        if ($this->testEventCode) {
            $payload['test_event_code'] = $this->testEventCode;
        }

        try {
            $response = Http::timeout(3)
                ->post("https://graph.facebook.com/" . self::GRAPH_API_VERSION . "/{$this->pixelId}/events", $payload);

            if (!$response->successful()) {
                Log::warning('Meta Conversion API rejected an event.', [
                    'event_name' => $eventName,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
            }
        } catch (ConnectionException $exception) {
            Log::warning('Meta Conversion API connection failed.', [
                'event_name' => $eventName,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
