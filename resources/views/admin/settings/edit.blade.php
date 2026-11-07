@extends('layouts.admin')

@section('title', 'Settings - Outfitt')
@section('page_title', 'Settings')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-lg text-base font-semibold">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Meta Pixel Settings -->
        <div class="bg-gradient-to-br from-slate-900 to-indigo-950 border border-indigo-800/40 p-8 rounded-2xl shadow-lg">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-indigo-800/30">
                <span class="w-10 h-10 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
                <div>
                    <h3 class="font-bold text-white text-xl">Meta Pixel Settings</h3>
                    <p class="text-base text-indigo-300/70">Configure Meta Pixel and Conversion API tracking.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="meta_pixel_id" class="block text-base font-bold uppercase tracking-wider text-indigo-300 mb-2">
                        Pixel ID <span class="text-pink-500">*</span>
                    </label>
                    <input type="text" id="meta_pixel_id" name="meta_pixel_id"
                           value="{{ old('meta_pixel_id', $metaPixelId) }}"
                           class="w-full bg-slate-800/60 border border-indigo-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                           placeholder="123456789012345">
                    @error('meta_pixel_id')
                        <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="meta_conversion_api_token" class="block text-base font-bold uppercase tracking-wider text-indigo-300 mb-2">
                        Conversion API Token
                    </label>
                    <input type="text" id="meta_conversion_api_token" name="meta_conversion_api_token"
                           value="{{ old('meta_conversion_api_token', $metaConversionApiToken) }}"
                           class="w-full bg-slate-800/60 border border-indigo-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                           placeholder="EAA...">
                    @error('meta_conversion_api_token')
                        <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="meta_test_event_code" class="block text-base font-bold uppercase tracking-wider text-indigo-300 mb-2">
                        Test Event Code
                    </label>
                    <input type="text" id="meta_test_event_code" name="meta_test_event_code"
                           value="{{ old('meta_test_event_code', $metaTestEventCode) }}"
                           class="w-full bg-slate-800/60 border border-indigo-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                           placeholder="TEST12345">
                    <p class="text-base text-indigo-300/50 mt-1">Only for testing — leave empty in production.</p>
                    @error('meta_test_event_code')
                        <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Email Notification Settings -->
        <div class="bg-gradient-to-br from-slate-900 to-blue-950 border border-blue-800/40 p-8 rounded-2xl shadow-lg">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-blue-800/30">
                <span class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <div>
                    <h3 class="font-bold text-white text-xl">Email Notification Settings</h3>
                    <p class="text-base text-blue-300/70">Configure where new order notifications are sent.</p>
                </div>
            </div>
            <div>
                <label for="order_notification_emails" class="block text-base font-bold uppercase tracking-wider text-blue-300 mb-2">
                    New Order Notification Emails <span class="text-pink-500">*</span>
                </label>
                <input type="text" id="order_notification_emails" name="order_notification_emails"
                       value="{{ old('order_notification_emails', $notificationEmails) }}"
                       class="w-full bg-slate-800/60 border border-blue-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                       placeholder="admin@example.com, manager@example.com">
                <p class="text-base text-blue-300/50 mt-1">Enter email addresses separated by commas. Notifications will be sent to all listed addresses when a new order is placed.</p>
                @error('order_notification_emails')
                    <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- WhatsApp Settings -->
        <div class="bg-gradient-to-br from-slate-900 to-emerald-950 border border-emerald-800/40 p-8 rounded-2xl shadow-lg">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-emerald-800/30">
                <span class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </span>
                <div>
                    <h3 class="font-bold text-white text-xl">WhatsApp Settings</h3>
                    <p class="text-base text-emerald-300/70">Configure WhatsApp number for the contact button on product pages.</p>
                </div>
            </div>
            <div>
                <label for="whatsapp_number" class="block text-base font-bold uppercase tracking-wider text-emerald-300 mb-2">
                    WhatsApp Number
                </label>
                <input type="text" id="whatsapp_number" name="whatsapp_number"
                       value="{{ old('whatsapp_number', $whatsappNumber) }}"
                       class="w-full bg-slate-800/60 border border-emerald-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                       placeholder="+8801XXXXXXXXX">
                <p class="text-base text-emerald-300/50 mt-1">This number will appear on the WhatsApp contact button in product pages.</p>
                @error('whatsapp_number')
                    <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Steadfast Courier Settings -->
        <div class="bg-gradient-to-br from-slate-900 to-cyan-950 border border-cyan-800/40 p-8 rounded-2xl shadow-lg">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-cyan-800/30">
                <span class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center text-cyan-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </span>
                <div>
                    <h3 class="font-bold text-white text-xl">Steadfast Courier Settings</h3>
                    <p class="text-base text-cyan-300/70">Configure Steadfast Courier API credentials for order shipping.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="steadfast_api_key" class="block text-base font-bold uppercase tracking-wider text-cyan-300 mb-2">
                        API Key <span class="text-pink-500">*</span>
                    </label>
                    <input type="text" id="steadfast_api_key" name="steadfast_api_key"
                           value="{{ old('steadfast_api_key', $steadfastApiKey) }}"
                           class="w-full bg-slate-800/60 border border-cyan-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                           placeholder="Enter your Steadfast API Key">
                    @error('steadfast_api_key')
                        <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="steadfast_secret_key" class="block text-base font-bold uppercase tracking-wider text-cyan-300 mb-2">
                        Secret Key <span class="text-pink-500">*</span>
                    </label>
                    <input type="text" id="steadfast_secret_key" name="steadfast_secret_key"
                           value="{{ old('steadfast_secret_key', $steadfastSecretKey) }}"
                           class="w-full bg-slate-800/60 border border-cyan-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                           placeholder="Enter your Steadfast Secret Key">
                    @error('steadfast_secret_key')
                        <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Free Shipping Settings -->
        <div class="bg-gradient-to-br from-slate-900 to-emerald-950 border border-emerald-800/40 p-8 rounded-2xl shadow-lg">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-emerald-800/30">
                <span class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </span>
                <div>
                    <h3 class="font-bold text-white text-xl">Free Shipping Settings</h3>
                    <p class="text-base text-emerald-300/70">Set the minimum order amount that unlocks free delivery (ফ্রি ডেলিভারি) site-wide.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="free_shipping_threshold" class="block text-base font-bold uppercase tracking-wider text-emerald-300 mb-2">
                        Free Shipping Above (৳)
                    </label>
                    <input type="number" step="0.01" min="0" id="free_shipping_threshold" name="free_shipping_threshold"
                           value="{{ old('free_shipping_threshold', $freeShippingThreshold) }}"
                           class="w-full bg-slate-800/60 border border-emerald-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                           placeholder="e.g. 1000">
                    <p class="text-base text-emerald-300/50 mt-1">Orders of this amount or more get free delivery. Leave empty to disable free shipping.</p>
                    @error('free_shipping_threshold')
                        <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Social Media Links -->
        <div class="bg-gradient-to-br from-slate-900 to-rose-950 border border-rose-800/40 p-8 rounded-2xl shadow-lg">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-rose-800/30">
                <span class="w-10 h-10 rounded-lg bg-rose-500/20 flex items-center justify-center text-rose-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </span>
                <div>
                    <h3 class="font-bold text-white text-xl">Social Media Links</h3>
                    <p class="text-base text-rose-300/70">Social media URLs displayed in the footer. Leave blank to hide an icon.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="facebook_url" class="block text-base font-bold uppercase tracking-wider text-rose-300 mb-2">Facebook URL</label>
                    <input type="url" id="facebook_url" name="facebook_url"
                           value="{{ old('facebook_url', $facebookUrl) }}"
                           class="w-full bg-slate-800/60 border border-rose-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                           placeholder="https://facebook.com/yourpage">
                    @error('facebook_url')
                        <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="instagram_url" class="block text-base font-bold uppercase tracking-wider text-rose-300 mb-2">Instagram URL</label>
                    <input type="url" id="instagram_url" name="instagram_url"
                           value="{{ old('instagram_url', $instagramUrl) }}"
                           class="w-full bg-slate-800/60 border border-rose-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                           placeholder="https://instagram.com/yourpage">
                    @error('instagram_url')
                        <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="youtube_url" class="block text-base font-bold uppercase tracking-wider text-rose-300 mb-2">YouTube URL</label>
                    <input type="url" id="youtube_url" name="youtube_url"
                           value="{{ old('youtube_url', $youtubeUrl) }}"
                           class="w-full bg-slate-800/60 border border-rose-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                           placeholder="https://youtube.com/@channel">
                    @error('youtube_url')
                        <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="twitter_url" class="block text-base font-bold uppercase tracking-wider text-rose-300 mb-2">Twitter / X URL</label>
                    <input type="url" id="twitter_url" name="twitter_url"
                           value="{{ old('twitter_url', $twitterUrl) }}"
                           class="w-full bg-slate-800/60 border border-rose-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                           placeholder="https://x.com/yourhandle">
                    @error('twitter_url')
                        <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- General Site Settings -->
        <div class="bg-gradient-to-br from-slate-900 to-fuchsia-950 border border-fuchsia-800/40 p-8 rounded-2xl shadow-lg">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-fuchsia-800/30">
                <span class="w-10 h-10 rounded-lg bg-fuchsia-500/20 flex items-center justify-center text-fuchsia-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
                <div>
                    <h3 class="font-bold text-white text-xl">General Site Settings</h3>
                    <p class="text-base text-fuchsia-300/70">Configure the site name, logo, and description shown across the website.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="site_name" class="block text-base font-bold uppercase tracking-wider text-fuchsia-300 mb-2">
                        Site Name
                    </label>
                    <input type="text" id="site_name" name="site_name"
                           value="{{ old('site_name', $siteName) }}"
                           class="w-full bg-slate-800/60 border border-fuchsia-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                           placeholder="Outfitt">
                    <p class="text-base text-fuchsia-300/50 mt-1">Used in the header, footer, page titles, and copyright notice.</p>
                    @error('site_name')
                        <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="site_logo" class="block text-base font-bold uppercase tracking-wider text-fuchsia-300 mb-2">
                        Site Logo
                    </label>
                    <div class="flex items-start gap-4">
                        <img id="logo-preview"
                             src="{{ $siteLogo ? \Illuminate\Support\Facades\Storage::url($siteLogo) : '/logo.webp' }}"
                             alt="Site Logo Preview"
                             class="w-24 h-16 object-contain rounded-lg bg-white border border-fuchsia-700/40 p-1">
                        <div class="flex-1">
                            <input type="file" id="site_logo" name="site_logo" accept="image/*"
                                   onchange="document.getElementById('logo-preview').src = URL.createObjectURL(this.files[0])"
                                   class="w-full bg-slate-800/60 border border-fuchsia-700/40 rounded-lg px-4 py-3 text-base text-slate-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-fuchsia-500/20 file:text-fuchsia-300 file:font-bold file:cursor-pointer hover:file:bg-fuchsia-500/30">
                            <p class="text-base text-fuchsia-300/50 mt-1">Recommended: transparent PNG or WebP, max 2MB.</p>
                        </div>
                    </div>
                    @error('site_logo')
                        <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="primary_color" class="block text-base font-bold uppercase tracking-wider text-fuchsia-300 mb-2">
                        Primary Color
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color" id="primary_color_picker" name="primary_color"
                               value="{{ old('primary_color', $primaryColor) }}"
                               class="w-16 h-12 rounded-lg bg-slate-800/60 border border-fuchsia-700/40 p-1 cursor-pointer"
                               oninput="document.getElementById('primary_color_hex').value = this.value">
                        <input type="text" id="primary_color_hex"
                               value="{{ old('primary_color', $primaryColor) }}"
                               oninput="if(/^#[0-9a-fA-F]{6}$/.test(this.value)) document.getElementById('primary_color_picker').value = this.value"
                               class="flex-1 bg-slate-800/60 border border-fuchsia-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                               placeholder="#7c3aed">
                    </div>
                    <p class="text-base text-fuchsia-300/50 mt-1">Replaces the primary color (purple-700) across the website. No rebuild needed.</p>
                    @error('primary_color')
                        <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="mt-6">
                <label for="site_description" class="block text-base font-bold uppercase tracking-wider text-fuchsia-300 mb-2">
                    Site Description
                </label>
                <textarea id="site_description" name="site_description" rows="3"
                          class="w-full bg-slate-800/60 border border-fuchsia-700/40 rounded-lg px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent transition-all text-slate-200 placeholder-slate-600"
                          placeholder="Describe your store in a sentence or two...">{{ old('site_description', $siteDescription) }}</textarea>
                <p class="text-base text-fuchsia-300/50 mt-1">Shown in the website footer and as the SEO meta description.</p>
                @error('site_description')
                    <span class="text-base text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="flex items-center space-x-4 pt-2">
            <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 text-white font-bold rounded-lg text-base transition-all shadow-md">
                Save Settings
            </button>
            <a href="{{ route('admin.dashboard') }}"
               class="px-8 py-3 bg-slate-800/50 hover:bg-slate-700/50 text-slate-300 font-bold rounded-lg text-base transition-all">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
