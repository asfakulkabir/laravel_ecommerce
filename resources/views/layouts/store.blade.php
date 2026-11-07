<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', site_name() . ' - Premium E-commerce Store')</title>
    @if(site_description())
        <meta name="description" content="{{ site_description() }}">
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $pixelId = \App\Models\Setting::getValue('meta_pixel_id', '');
        if (!ctype_digit((string) $pixelId)) {
            $pixelId = '';
        }
    @endphp
    @if($pixelId)
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window,document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', @json($pixelId));
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1"
    /></noscript>
    @endif
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        [x-cloak] {
            display: none !important;
        }
    </style>

    <style>
        :root {
            --color-primary: {{ primary_color() }};
            --color-primary-dark: {{ primary_color_dark() }};
        }
        .text-primary { color: var(--color-primary); }
        .hover\:text-primary:hover { color: var(--color-primary-dark); }
        .bg-primary { background-color: var(--color-primary); }
        .hover\:bg-primary:hover { background-color: var(--color-primary-dark); }
        .border-primary { border-color: var(--color-primary); }
        .hover\:border-primary:hover { border-color: var(--color-primary-dark); }
        .ring-primary { --tw-ring-color: var(--color-primary); }
        .from-primary { --tw-gradient-from: var(--color-primary) var(--tw-gradient-from-position); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
        .to-primary { --tw-gradient-to: var(--color-primary) var(--tw-gradient-to-position); }
        .hover\:from-primary:hover { --tw-gradient-from: var(--color-primary-dark) var(--tw-gradient-from-position); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
        .hover\:to-primary:hover { --tw-gradient-to: var(--color-primary-dark) var(--tw-gradient-to-position); }

        .bg-purple-50 { background-color: color-mix(in srgb, var(--color-primary) 6%, white) !important; }
        .bg-purple-100 { background-color: color-mix(in srgb, var(--color-primary) 12%, white) !important; }
        .bg-purple-200 { background-color: color-mix(in srgb, var(--color-primary) 25%, white) !important; }
        .bg-purple-500 { background-color: color-mix(in srgb, var(--color-primary) 80%, white) !important; }
        .bg-purple-600 { background-color: var(--color-primary) !important; }
        .bg-purple-900 { background-color: color-mix(in srgb, var(--color-primary) 55%, black) !important; }

        .text-purple-50 { color: color-mix(in srgb, var(--color-primary) 6%, white) !important; }
        .text-purple-200 { color: color-mix(in srgb, var(--color-primary) 25%, white) !important; }
        .text-purple-300 { color: color-mix(in srgb, var(--color-primary) 40%, white) !important; }
        .text-purple-400 { color: color-mix(in srgb, var(--color-primary) 60%, white) !important; }
        .text-purple-500 { color: color-mix(in srgb, var(--color-primary) 80%, white) !important; }
        .text-purple-600 { color: var(--color-primary) !important; }

        .border-purple-100 { border-color: color-mix(in srgb, var(--color-primary) 12%, white) !important; }
        .border-purple-200 { border-color: color-mix(in srgb, var(--color-primary) 25%, white) !important; }
        .border-purple-400 { border-color: color-mix(in srgb, var(--color-primary) 60%, white) !important; }
        .border-purple-500 { border-color: color-mix(in srgb, var(--color-primary) 80%, white) !important; }
        .border-purple-600 { border-color: var(--color-primary) !important; }

        .ring-purple-100 { --tw-ring-color: color-mix(in srgb, var(--color-primary) 12%, white) !important; }
        .ring-purple-200 { --tw-ring-color: color-mix(in srgb, var(--color-primary) 25%, white) !important; }
        .ring-purple-400 { --tw-ring-color: color-mix(in srgb, var(--color-primary) 60%, white) !important; }
        .ring-purple-500 { --tw-ring-color: color-mix(in srgb, var(--color-primary) 80%, white) !important; }

        .from-purple-100 { --tw-gradient-from: color-mix(in srgb, var(--color-primary) 12%, white) var(--tw-gradient-from-position); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
        .from-purple-600 { --tw-gradient-from: var(--color-primary) var(--tw-gradient-from-position); --tw-gradient-to: rgb(147 51 234 / 0) var(--tw-gradient-to-position); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
        .from-purple-900 { --tw-gradient-from: color-mix(in srgb, var(--color-primary) 55%, black) var(--tw-gradient-from-position); --tw-gradient-to: rgb(88 28 135 / 0) var(--tw-gradient-to-position); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
        .via-purple-900 { --tw-gradient-to: rgb(88 28 135 / 0) var(--tw-gradient-to-position); --tw-gradient-stops: var(--tw-gradient-from), color-mix(in srgb, var(--color-primary) 55%, black) var(--tw-gradient-via-position), var(--tw-gradient-to); }
        .to-purple-600 { --tw-gradient-to: var(--color-primary) var(--tw-gradient-to-position); }

        .bg-purple-50\/50 { background-color: color-mix(in srgb, color-mix(in srgb, var(--color-primary) 6%, white) 50%, transparent) !important; }
        .bg-purple-50\/30 { background-color: color-mix(in srgb, color-mix(in srgb, var(--color-primary) 6%, white) 30%, transparent) !important; }
        .bg-purple-50\/20 { background-color: color-mix(in srgb, color-mix(in srgb, var(--color-primary) 6%, white) 20%, transparent) !important; }
        .text-purple-300\/60 { color: color-mix(in srgb, color-mix(in srgb, var(--color-primary) 40%, white) 60%, transparent) !important; }
        .text-purple-200\/70 { color: color-mix(in srgb, color-mix(in srgb, var(--color-primary) 25%, white) 70%, transparent) !important; }
        .border-purple-100\/60 { border-color: color-mix(in srgb, color-mix(in srgb, var(--color-primary) 12%, white) 60%, transparent) !important; }
        .border-purple-100\/50 { border-color: color-mix(in srgb, color-mix(in srgb, var(--color-primary) 12%, white) 50%, transparent) !important; }
        .border-purple-500\/20 { border-color: color-mix(in srgb, color-mix(in srgb, var(--color-primary) 80%, white) 20%, transparent) !important; }

        .hover\:bg-purple-50:hover { background-color: color-mix(in srgb, var(--color-primary) 10%, white) !important; }
        .hover\:bg-purple-100:hover { background-color: color-mix(in srgb, var(--color-primary) 18%, white) !important; }
        .hover\:text-purple-300:hover { color: color-mix(in srgb, var(--color-primary) 45%, white) !important; }
        .hover\:text-purple-400:hover { color: color-mix(in srgb, var(--color-primary) 65%, white) !important; }
        .hover\:text-purple-600:hover { color: var(--color-primary) !important; }
        .hover\:border-purple-400:hover { border-color: color-mix(in srgb, var(--color-primary) 60%, white) !important; }
        .hover\:border-purple-500:hover { border-color: var(--color-primary) !important; }

        .focus\:border-purple-500:focus { border-color: color-mix(in srgb, var(--color-primary) 80%, white) !important; }
        .focus\:ring-purple-500:focus { --tw-ring-color: color-mix(in srgb, var(--color-primary) 80%, white) !important; }

        .group-hover\:text-purple-600:is(:where(.group):hover *) { color: var(--color-primary) !important; }
        .group-hover\:text-purple-50:is(:where(.group):hover *) { color: color-mix(in srgb, var(--color-primary) 6%, white) !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-rose-50 via-gray-50 to-pink-100 text-gray-800 flex flex-col min-h-screen">

    <!-- Free Shipping Top Banner -->
    @php
        $freeShippingMin = (float) \App\Models\Setting::getValue('free_shipping_threshold', 0);
    @endphp
    @if($freeShippingMin > 0)
        <div class="bg-emerald-600 border-b border-emerald-400/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 flex items-center justify-center gap-3 text-white font-semibold text-sm md:text-base">
                <span>🆓</span>
                <span>৳{{ number_format($freeShippingMin, 0) }}+ অর্ডারে সারা দেশে <span class="uppercase tracking-wide">ফ্রি ডেলিভারি</span></span>
            </div>
        </div>
    @endif

    <!-- Header / Navbar -->
    <header x-data="{ menuOpen: false, openCat: null }" class="bg-white/95 backdrop-blur-md sticky top-0 z-40 border-b border-purple-500 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img class="h-8 lg:h-10 w-auto rounded" src="{{ site_logo() }}" alt="{{ site_name() }}">
                    </a>
                </div>

                @php
                    $parentCats = \App\Models\Category::whereNull('parent_id')->with('children')->orderBy('name')->get();
                @endphp

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold tracking-wider uppercase">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-purple-600 transition-colors py-2 {{ request()->routeIs('home') ? 'border-b-2 border-pink-500 text-purple-600' : '' }}">Home</a>
                    <a href="{{ route('shop') }}" class="text-gray-600 hover:text-purple-600 transition-colors py-2 {{ request()->routeIs('shop') ? 'border-b-2 border-pink-500 text-purple-600' : '' }}">Shop</a>
                    
                    @foreach($parentCats as $cat)
                        <div class="relative group">
                            <a href="{{ route('shop', ['category' => $cat->slug]) }}" class="text-gray-600 hover:text-purple-600 transition-colors py-2 inline-block {{ request()->input('category') === $cat->slug ? 'border-b-2 border-pink-500 text-purple-600' : '' }}">{{ $cat->name }}</a>
                            @if($cat->children->count() > 0)
                                <div class="absolute top-full left-0 mt-0 bg-white rounded-lg shadow-lg border border-gray-100 min-w-[200px] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 py-2">
                                    @foreach($cat->children as $child)
                                        <a href="{{ route('shop', ['category' => $child->slug]) }}" class="block px-4 py-2 text-sm text-gray-600 hover:text-purple-600 hover:bg-purple-50 transition-colors whitespace-nowrap">{{ $child->name }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </nav>

                <!-- Desktop Action Controls -->
                <div class="flex items-center space-x-6">
                    <!-- Desktop Search Form -->
                    <form action="{{ route('shop') }}" method="GET" class="hidden lg:flex relative items-center">
                        <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}"
                               class="bg-purple-50 text-gray-800 placeholder-purple-300 text-sm rounded-full py-2 px-5 pl-10 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:bg-white border border-purple-500 w-64 transition-all">
                        <svg class="w-5 h-5 text-purple-400 absolute left-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </form>

                    <!-- Cart Icon with badge -->
                    <a href="#" @click.prevent="$dispatch('open-cart')" class="relative p-2 text-gray-600 hover:text-purple-600 transition-colors" id="cart-nav-btn">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        @php
                            $cartCount = count(session('cart', []));
                        @endphp
                        <span id="cart-badge" class="absolute -top-1 -right-1 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-full text-xs font-bold w-5 h-5 flex items-center justify-center border-2 border-white shadow {{ $cartCount == 0 ? 'hidden' : '' }}">
                            {{ $cartCount }}
                        </span>
                    </a>

                    <!-- Hamburger Button (Mobile) -->
                    <button @click="menuOpen = !menuOpen" class="md:hidden p-2 text-gray-600 hover:text-purple-600 transition-colors">
                        <svg x-show="!menuOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="menuOpen" x-cloak class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="menuOpen" x-cloak @click.away="menuOpen = false" class="md:hidden bg-white border-t border-purple-100 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 space-y-4">
                <!-- Search inside hamburger -->
                <form action="{{ route('shop') }}" method="GET">
                    <div class="relative">
                        <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}"
                               class="w-full bg-purple-50 text-gray-800 placeholder-purple-300 text-sm rounded-full py-2.5 px-5 pl-10 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:bg-white border border-purple-500 transition-all">
                        <svg class="w-5 h-5 text-purple-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </form>

                <!-- Mobile Nav Links -->
                <div class="flex flex-col space-y-2 text-sm font-semibold tracking-wider uppercase">
                    <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-gray-600 hover:text-purple-600 hover:bg-purple-50 transition-colors {{ request()->routeIs('home') ? 'text-purple-600 bg-purple-50' : '' }}">Home</a>
                    <a href="{{ route('shop') }}" class="px-3 py-2 rounded-lg text-gray-600 hover:text-purple-600 hover:bg-purple-50 transition-colors {{ request()->routeIs('shop') ? 'text-purple-600 bg-purple-50' : '' }}">Shop</a>
                    @foreach($parentCats as $cat)
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('shop', ['category' => $cat->slug]) }}" class="flex-1 px-3 py-2 rounded-lg text-gray-600 hover:text-purple-600 hover:bg-purple-50 transition-colors {{ request()->input('category') === $cat->slug ? 'text-purple-600 bg-purple-50' : '' }}">{{ $cat->name }}</a>
                                @if($cat->children->count() > 0)
                                    <button @click="openCat = openCat === {{ $cat->id }} ? null : {{ $cat->id }}" class="ml-1 p-2 text-gray-400 hover:text-purple-600 transition-colors" aria-label="Toggle {{ $cat->name }} subcategories">
                                        <svg :class="openCat === {{ $cat->id }} ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            @if($cat->children->count() > 0)
                                <div x-show="openCat === {{ $cat->id }}" class="pl-6 space-y-1">
                                    @foreach($cat->children as $child)
                                        <a href="{{ route('shop', ['category' => $child->slug]) }}" class="block px-3 py-1.5 rounded-lg text-sm text-gray-500 hover:text-purple-600 hover:bg-purple-50 transition-colors">{{ $child->name }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </header>

    <!-- Notification Toasts / Banners -->
    @if(session('success'))
        <div class="bg-gradient-to-r from-purple-600 to-pink-500 text-white text-center py-3 px-4 font-medium relative shadow-md">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-500 text-white text-center py-3 px-4 font-medium relative shadow-md">
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Content Grid -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                <!-- Brand Info -->
                <div class="space-y-4">
                    <img src="{{ site_logo() }}" alt="{{ site_name() }}" class="h-10 opacity-90 rounded bg-white p-1">
                    <p class="text-sm">{{ site_description() ?: 'Premium clothing and accessories curated for modern lifestyles. Wear your style with confidence.' }}</p>
                </div>

                <!-- Quick links -->
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-white mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('shop') }}" class="hover:text-purple-400 transition-all">Shop All</a></li>
                        <li><a href="{{ route('cart') }}" class="hover:text-purple-400 transition-all">My Cart</a></li>
                        <li><a href="{{ route('admin.login') }}" class="hover:text-purple-400 transition-all">Admin Dashboard</a></li>
                    </ul>
                </div>

                <!-- Categories List -->
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-white mb-4">Shop Categories</h3>
                    <ul class="space-y-2 text-sm">
                        @foreach($parentCats as $cat)
                            <li><a href="{{ route('shop', ['category' => $cat->slug]) }}" class="hover:text-purple-400 transition-all">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Newsletter / Contact -->
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-white mb-4">Newsletter</h3>
                    <p class="text-sm mb-4">Subscribe to receive updates, access to exclusive deals, and more.</p>
                    <form class="flex">
                        <input type="email" placeholder="Your email" class="w-full bg-gray-800 text-gray-200 placeholder-gray-500 rounded-l-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500 border border-gray-700 text-sm">
                        <button type="submit" class="bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-r-md px-4 py-2 font-medium hover:from-primary hover:to-pink-600 transition text-sm">Join</button>
                    </form>
                </div>
            </div>
            @php
                $fbUrl = App\Models\Setting::getValue('facebook_url', '');
                $igUrl = App\Models\Setting::getValue('instagram_url', '');
                $ytUrl = App\Models\Setting::getValue('youtube_url', '');
                $twUrl = App\Models\Setting::getValue('twitter_url', '');
            @endphp
            <div class="mt-12 border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-xs">
                <p class="text-gray-400">
                    &copy; {{ date('Y') }} {{ site_name() }}. Developed by 
                    <a href="https://beginnershut.com/" target="_blank" rel="noopener noreferrer" class="text-purple-400 hover:text-purple-300 font-semibold">Beginners Hut</a>.
                </p>
                <div class="flex items-center space-x-4 mt-4 md:mt-0">
                    @if($fbUrl)
                        <a href="{{ $fbUrl }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-400 transition-all" title="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    @endif
                    @if($igUrl)
                        <a href="{{ $igUrl }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-400 transition-all" title="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                    @endif
                    @if($ytUrl)
                        <a href="{{ $ytUrl }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-400 transition-all" title="YouTube">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    @endif
                    @if($twUrl)
                        <a href="{{ $twUrl }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-purple-400 transition-all" title="Twitter / X">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    <!-- Side Cart Drawer -->
    <div x-data="sideCart()" 
         @open-cart.window="open()" 
         class="relative z-50" 
         x-show="isOpen" 
         x-cloak
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300"
             x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="close()"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    
                    <!-- Slide-over panel -->
                    <div class="pointer-events-auto w-screen max-w-md"
                         x-show="isOpen"
                         @click.away="close()"
                         x-transition:enter="transform transition ease-in-out duration-300"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-300"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full">
                        
                        <div class="flex h-full flex-col bg-white shadow-2xl border-l border-purple-100">
                            
                            <!-- Header -->
                            <div class="flex items-center justify-between border-b border-gray-150 px-6 py-5 bg-purple-50/20">
                                <h2 class="text-base font-bold text-gray-900 flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    <span>Shopping Cart</span>
                                </h2>
                                <button type="button" @click="close()" class="text-gray-400 hover:text-purple-600 transition">
                                    <span class="sr-only">Close panel</span>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Content / Item List -->
                            <div class="flex-1 overflow-y-auto px-6 py-6 space-y-4">
                                <div class="space-y-4" x-show="count > 0">
                                    <template x-for="item in items" :key="item.key">
                                        <div class="flex items-start bg-white border border-gray-100 rounded-xl p-3 gap-3 shadow-sm">
                                            <!-- Image -->
                                            <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                                <img x-show="item.image" :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                                                <div x-show="!item.image" class="w-full h-full flex items-center justify-center text-purple-300 bg-purple-50">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            </div>

                                            <!-- Details -->
                                            <div class="flex-grow space-y-1">
                                                <h4 class="font-bold text-gray-900 text-xs truncate max-w-[180px]" x-text="item.name"></h4>
                                                <p class="text-[10px] font-semibold text-purple-600" x-show="item.variation_details" x-text="item.variation_details"></p>
                                                <div class="flex items-center justify-between mt-2">
                                                    <!-- Quantity controls -->
                                                    <div class="flex items-center border border-gray-200 rounded overflow-hidden bg-gray-50">
                                                        <button type="button" @click="updateQty(item.key, item.quantity - 1)" class="px-1.5 py-0.5 text-gray-500 hover:bg-gray-100 text-xs">-</button>
                                                        <span class="px-2 text-xs font-bold text-gray-950" x-text="item.quantity"></span>
                                                        <button type="button" @click="updateQty(item.key, item.quantity + 1)" class="px-1.5 py-0.5 text-gray-500 hover:bg-gray-100 text-xs">+</button>
                                                    </div>
                                                    <!-- Price -->
                                                    <span class="text-xs font-bold text-gray-950" x-text="money(item.price * item.quantity)"></span>
                                                </div>
                                            </div>

                                            <!-- Remove button -->
                                            <button type="button" @click="removeItem(item.key)" class="text-pink-600 hover:text-pink-700 transition flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <div class="flex flex-col items-center justify-center py-20 space-y-4 text-center" x-show="count === 0">
                                    <div class="w-16 h-16 bg-purple-50 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-400">Your cart is empty</div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div x-show="count > 0" class="border-t border-gray-150 px-6 py-6 bg-gray-50 space-y-4">
                                <div class="flex justify-between items-baseline font-bold text-gray-900">
                                    <span>Subtotal</span>
                                    <span class="text-purple-600 text-lg" x-text="money(subtotal)"></span>
                                </div>
                                <p class="text-[10px] text-gray-400">Shipping and taxes calculated at checkout.</p>

                                <div class="space-y-2">
                                    <a href="{{ route('checkout') }}" class="block w-full text-center py-3 bg-primary hover:bg-primary-dark text-white rounded-lg font-bold tracking-wide transition shadow-sm">
                                        Checkout Now
                                    </a>
                                    <a href="{{ route('cart') }}" class="block w-full text-center py-2 text-xs font-bold text-purple-600 hover:text-primary uppercase tracking-wider">
                                        View Full Cart
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        @php
            $sideCartPrepared = [];
            foreach (session('cart', []) as $cartKey => $cartItem) {
                $sideCartPrepared[] = [
                    'key' => $cartKey,
                    'name' => $cartItem['name'],
                    'image' => $cartItem['image'] ? Storage::url($cartItem['image']) : null,
                    'variation_details' => $cartItem['variation_details'] ?? null,
                    'price' => (float) $cartItem['price'],
                    'quantity' => (int) $cartItem['quantity'],
                ];
            }
        @endphp

        document.addEventListener('alpine:init', () => {
            Alpine.data('sideCart', () => ({
                isOpen: false,
                items: @json($sideCartPrepared),

                open() {
                    this.isOpen = true;
                },
                close() {
                    this.isOpen = false;
                },
                get count() {
                    return this.items.length;
                },
                get subtotal() {
                    return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },
                money(n) {
                    return '\u09F3' + Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
                refreshBadge() {
                    const badge = document.getElementById('cart-badge');
                    if (badge) {
                        badge.textContent = this.count;
                        badge.classList.toggle('hidden', this.count === 0);
                    }
                },
                async updateQty(key, qty) {
                    if (qty < 1) return;
                    const item = this.items.find(i => i.key === key);
                    if (!item) return;
                    const previous = item.quantity;
                    item.quantity = qty;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    try {
                        const response = await fetch('{{ route("cart.update") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({ key, quantity: qty }),
                        });
                        const data = await response.json();
                        if (!data.success) {
                            item.quantity = previous;
                            this.refreshBadge();
                            alert(data.message || 'Cannot update quantity.');
                        }
                    } catch (e) {
                        item.quantity = previous;
                        this.refreshBadge();
                        alert('Cannot update quantity.');
                    }
                },
                async removeItem(key) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    try {
                        const response = await fetch('{{ route("cart.remove") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({ key }),
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.items = this.items.filter(i => i.key !== key);
                            this.refreshBadge();
                        } else {
                            alert(data.message || 'Cannot remove item.');
                        }
                    } catch (e) {
                        alert('Cannot remove item.');
                    }
                }
            }));
        });

        // Ensure cart opens on page load if needed
        document.addEventListener('DOMContentLoaded', () => {
            // Add click handler to cart button
            const cartBtn = document.getElementById('cart-nav-btn');
            if (cartBtn) {
                cartBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log('Cart button clicked');
                    window.dispatchEvent(new Event('open-cart'));
                });
            }

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('open_cart') === '1') {
                setTimeout(() => {
                    window.dispatchEvent(new Event('open-cart'));
                }, 500);
            }
        });
    </script>

    @if($pixelId)
    {{-- Flash-based pixel events (AddToCart, Purchase, etc.) --}}
    @if(session()->has('pixel_events'))
        @foreach(session('pixel_events') as $event)
        <script>
            fbq('track', @json($event['event']), @json($event['data'] ?? []), {
                eventID: @json($event['event_id'] ?? null)
            });
        </script>
        @endforeach
        @php session()->forget('pixel_events') @endphp
    @endif
    {{-- Page-specific pixel events (ViewContent, Search, InitiateCheckout, etc.) --}}
    @yield('pixel_events')
    @endif

    @yield('scripts')
</body>
</html>
