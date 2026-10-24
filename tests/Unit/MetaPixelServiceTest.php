<?php

namespace Tests\Unit;

use App\Models\Setting;
use App\Services\MetaPixelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MetaPixelServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_conversion_api_event_includes_deduplication_and_browser_attribution_data(): void
    {
        Setting::setValue('meta_pixel_id', '123456789');
        Setting::setValue('meta_conversion_api_token', 'test-token');
        Setting::setValue('meta_test_event_code', 'TEST123');
        Http::fake();

        request()->cookies->set('_fbp', 'fb.1.123456789.1234567890');
        request()->cookies->set('_fbc', 'fb.1.123456789.1234567890');
        request()->headers->set('User-Agent', 'Pixel test browser');

        app(MetaPixelService::class)->sendEvent(
            'Purchase',
            ['value' => 125.5, 'currency' => 'BDT'],
            [],
            'purchase-event-123'
        );

        Http::assertSent(function (ClientRequest $request): bool {
            $event = $request['data'][0];

            return str_contains($request->url(), '/v26.0/123456789/events')
                && $request['test_event_code'] === 'TEST123'
                && $event['event_name'] === 'Purchase'
                && $event['event_id'] === 'purchase-event-123'
                && $event['custom_data']['value'] === 125.5
                && $event['user_data']['fbp'] === 'fb.1.123456789.1234567890'
                && $event['user_data']['fbc'] === 'fb.1.123456789.1234567890'
                && $event['user_data']['client_user_agent'] === 'Pixel test browser';
        });
    }

    public function test_conversion_api_is_not_called_with_an_invalid_pixel_id(): void
    {
        Setting::setValue('meta_pixel_id', 'invalid-pixel-id');
        Setting::setValue('meta_conversion_api_token', 'test-token');
        Http::fake();

        app(MetaPixelService::class)->sendEvent('Purchase', ['value' => 100]);

        Http::assertNothingSent();
    }
}
