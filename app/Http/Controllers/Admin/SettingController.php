<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        $siteName = Setting::getValue('site_name', 'Outfitt');
        $siteDescription = Setting::getValue('site_description', '');
        $siteLogo = Setting::getValue('site_logo', '');
        $primaryColor = Setting::getValue('primary_color', '#7c3aed');
        $notificationEmails = Setting::getValue('order_notification_emails', '');
        $metaPixelId = Setting::getValue('meta_pixel_id', '');
        $metaConversionApiToken = Setting::getValue('meta_conversion_api_token', '');
        $metaTestEventCode = Setting::getValue('meta_test_event_code', '');
        $whatsappNumber = Setting::getValue('whatsapp_number', '');
        $facebookUrl = Setting::getValue('facebook_url', '');
        $instagramUrl = Setting::getValue('instagram_url', '');
        $youtubeUrl = Setting::getValue('youtube_url', '');
        $twitterUrl = Setting::getValue('twitter_url', '');
        $steadfastApiKey = Setting::getValue('steadfast_api_key', '');
        $steadfastSecretKey = Setting::getValue('steadfast_secret_key', '');
        $freeShippingThreshold = Setting::getValue('free_shipping_threshold', '');
        return view('admin.settings.edit', compact(
            'siteName',
            'siteDescription',
            'siteLogo',
            'primaryColor',
            'notificationEmails',
            'metaPixelId',
            'metaConversionApiToken',
            'metaTestEventCode',
            'whatsappNumber',
            'facebookUrl',
            'instagramUrl',
            'youtubeUrl',
            'twitterUrl',
            'steadfastApiKey',
            'steadfastSecretKey',
            'freeShippingThreshold',
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'primary_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'order_notification_emails' => 'nullable|string|max:1000',
            'meta_pixel_id' => ['nullable', 'regex:/^[0-9]+$/', 'max:30'],
            'meta_conversion_api_token' => 'nullable|string|max:255',
            'meta_test_event_code' => 'nullable|string|max:100',
            'whatsapp_number' => 'nullable|string|max:30',
            'facebook_url' => 'nullable|url|max:500',
            'instagram_url' => 'nullable|url|max:500',
            'youtube_url' => 'nullable|url|max:500',
            'twitter_url' => 'nullable|url|max:500',
            'steadfast_api_key' => 'nullable|string|max:255',
            'steadfast_secret_key' => 'nullable|string|max:255',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
        ]);

        Setting::setValue('site_name', $request->input('site_name', 'Outfitt'));
        Setting::setValue('site_description', $request->input('site_description', ''));
        Setting::setValue('primary_color', $request->input('primary_color', '#7c3aed'));

        if ($request->hasFile('site_logo')) {
            $oldLogo = Setting::getValue('site_logo', '');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            $siteLogo = $request->file('site_logo')->store('settings', 'public');
            Setting::setValue('site_logo', $siteLogo);
        }

        Setting::setValue('order_notification_emails', $request->input('order_notification_emails', ''));
        Setting::setValue('meta_pixel_id', $request->input('meta_pixel_id', ''));
        Setting::setValue('meta_conversion_api_token', $request->input('meta_conversion_api_token', ''));
        Setting::setValue('meta_test_event_code', $request->input('meta_test_event_code', ''));
        Setting::setValue('whatsapp_number', $request->input('whatsapp_number', ''));
        Setting::setValue('facebook_url', $request->input('facebook_url', ''));
        Setting::setValue('instagram_url', $request->input('instagram_url', ''));
        Setting::setValue('youtube_url', $request->input('youtube_url', ''));
        Setting::setValue('twitter_url', $request->input('twitter_url', ''));
        Setting::setValue('steadfast_api_key', $request->input('steadfast_api_key', ''));
        Setting::setValue('steadfast_secret_key', $request->input('steadfast_secret_key', ''));
        Setting::setValue('free_shipping_threshold', $request->input('free_shipping_threshold', ''));

        return redirect()->route('admin.settings.edit')->with('success', 'Settings updated successfully.');
    }
}
