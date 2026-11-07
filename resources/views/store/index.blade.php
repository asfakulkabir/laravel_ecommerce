@extends('layouts.store')

@section('title', site_name() . ' - Premium E-commerce Store')

@section('content')
<div class="pb-16">
    
    <!-- Hero Slider -->
    @if($sliders->count() > 0)
        <section class="relative w-full overflow-hidden bg-gray-900" x-data="heroSlider({{ $sliders->count() }})" x-init="init()">
            <div class="relative w-full h-[200px] sm:h-[280px] md:h-[55vh] lg:h-[85vh] xl:max-h-[600px]">
                @foreach($sliders as $i => $slider)
                    <div x-show="activeSlide === {{ $i }}" x-transition:enter="transition-opacity duration-700" x-cloak
                         class="absolute inset-0 w-full">
                        @if($slider->link)
                            <a href="{{ $slider->link }}">
                        @endif
                        <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title ?? 'Slider ' . ($i + 1) }}"
                             class="w-full h-full object-cover">
                        @if($slider->link)
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($sliders->count() > 1)
                <button @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white rounded-full p-2 transition z-10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white rounded-full p-2 transition z-10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2 z-10">
                    @foreach($sliders as $i => $slider)
                        <button @click="activeSlide = {{ $i }}; resetTimer()"
                                class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                                :class="activeSlide === {{ $i }} ? 'bg-white w-6' : 'bg-white/40 hover:bg-white/60'">
                        </button>
                    @endforeach
                </div>
            @endif
        </section>
    @else
        <!-- Fallback solid background when no sliders -->
        <section class="relative bg-gradient-to-tr from-indigo-950 via-purple-900 to-pink-700 h-[200px] sm:h-[280px] md:h-[55vh] lg:h-[85vh]"></section>
    @endif

    <!-- Featured Categories -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 md:py-6 lg:py-8">
        <div class="flex flex-wrap gap-1 lg:gap-4 justify-center items-center">
            @foreach($featuredCategories as $cat)
                <a href="{{ route('shop', ['category' => $cat->slug]) }}" class="group flex flex-col  border bg-white border-gray-200 rounded-md overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 w-1/4 md:w-1/6 lg:w-24">
                    <div class="relative aspect-square bg-gray-100 overflow-hidden w-full">
                        @if($cat->image)
                            <img src="{{ asset('storage/' . $cat->image) }}" alt="{{ $cat->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-100 to-pink-100">
                                <span class="text-4xl font-extrabold text-purple-300/60">{{ substr($cat->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="py-2 text-center">
                        <h4 class="text-gray-900 text-xs sm:text-sm truncate">{{ $cat->name }}</h4>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Featured Products -->
    @if($featuredProducts->count() > 0)
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-4 md:pb-6 2xl:pb-8">
            <div class="flex justify-between items-end">
                <div class="space-y-1">
                    <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-gray-900">Featured Additions</h2>
                    <p class="text-sm text-gray-500 hidden md:block">Handpicked premium pieces from the current season.</p>
                </div>
                <a href="{{ route('shop') }}" class="text-sm font-bold text-purple-600 hover:text-primary">See All Products →</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                @foreach($featuredProducts as $product)
                    @include('store.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

@if($freeShippingThreshold > 0)
    <div class="max-w-2xl mx-auto mb-8 sm:mb-10 px-1">
        <div class="relative overflow-hidden rounded-2xl border border-emerald-100 bg-white shadow-sm">

            <div class="flex items-center gap-4 p-5 sm:p-6">

                <!-- Delivery Icon -->
                <div class="flex-shrink-0 flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-emerald-50 text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-8 h-8"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 7h11v10H3zM14 10h4l3 3v4h-7z"/>
                        <circle cx="7.5" cy="18" r="1.5"/>
                        <circle cx="17.5" cy="18" r="1.5"/>
                        <path stroke-linecap="round" d="M14 13h7"/>
                    </svg>
                </div>

                <!-- Text -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900">
                            ফ্রি ডেলিভারি!
                        </h3>

                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700">
                            SPECIAL OFFER
                        </span>
                    </div>

                    <p class="text-sm text-gray-500 leading-relaxed">
                        ৳{{ number_format($freeShippingThreshold, 0) }} বা তার বেশি অর্ডারে
                        <span class="font-semibold text-emerald-700">
                            সারা দেশে ডেলিভারি চার্জ সম্পূর্ণ ফ্রি।
                        </span>
                    </p>
                </div>

                <!-- Free Badge -->
                <div class="hidden sm:flex flex-shrink-0 flex-col items-center justify-center w-16 h-16 rounded-full border-2 border-dashed border-emerald-200 text-emerald-600">
                    <span class="text-xl font-extrabold leading-none">৳0</span>
                    <span class="text-[9px] font-bold mt-1">DELIVERY</span>
                </div>

            </div>

            <!-- Bottom Accent -->
            <div class="h-1 w-full bg-gradient-to-r from-emerald-300 via-emerald-500 to-teal-500"></div>

        </div>
    </div>
@endif

    <!-- Latest Arrivals -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="text-center space-y-2">
            <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-gray-900">Latest Arrivals</h2>
            <p class="text-sm text-gray-500">Be the first to secure our fresh collections.</p>
        </div>



        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
            @foreach($latestProducts as $product)
                @include('store.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </section>

</div>
@endsection

@section('scripts')
@if($sliders->count() > 0)
<script>
    function heroSlider(count) {
        return {
            activeSlide: 0,
            timer: null,
            init() {
                if (count > 1) {
                    this.timer = setInterval(() => {
                        this.activeSlide = (this.activeSlide + 1) % count;
                    }, 5000);
                }
            },
            prev() {
                this.activeSlide = (this.activeSlide - 1 + count) % count;
                this.resetTimer();
            },
            next() {
                this.activeSlide = (this.activeSlide + 1) % count;
                this.resetTimer();
            },
            resetTimer() {
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = setInterval(() => {
                        this.activeSlide = (this.activeSlide + 1) % count;
                    }, 5000);
                }
            }
        }
    }
</script>
@endif
@endsection
