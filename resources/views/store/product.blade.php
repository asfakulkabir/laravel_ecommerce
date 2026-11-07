@extends('layouts.store')

@section('title', "{$product->name} - " . site_name())

@section('pixel_events')
@if(isset($pixelEventId, $pixelEventData))
<script>
fbq('track', 'ViewContent', @json($pixelEventData), { eventID: @json($pixelEventId) });
</script>
@endif
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-3 md:py-10" x-data="productDetail({{ json_encode($variationsJson) }})">
    
    <!-- Breadcrumbs (desktop only) -->
    <nav class="hidden md:flex text-xs font-bold text-gray-400 mb-6 space-x-2">
        <a href="{{ route('home') }}" class="hover:text-purple-600 transition">Home</a>
        <span>/</span>
        <a href="{{ route('shop') }}" class="hover:text-purple-600 transition">Shop</a>
        @if($product->categories->count() > 0)
            @php $mainCat = $product->categories->first(); @endphp
            <span>/</span>
            <a href="{{ route('shop', ['category' => $mainCat->slug]) }}" class="hover:text-purple-600 transition">{{ $mainCat->name }}</a>
        @endif
        <span>/</span>
        <span class="text-gray-600">{{ $product->name }}</span>
    </nav>

    <!-- Product Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-10 items-start">
        
        <!-- Left: Image Gallery -->
        @php
            $imagesList = $product->images;
            $featuredImg = $imagesList->where('is_featured', true)->first() ?? $imagesList->first();
        @endphp
        <div class="space-y-2 md:space-y-4">
            <div class="bg-gray-100 aspect-square w-full rounded-xl md:rounded-2xl overflow-hidden border border-gray-200 relative">
                <img :src="activeImage" src="{{ $featuredImg ? Storage::url($featuredImg->image) : '/placeholder.png' }}" 
                     alt="{{ $product->name }}" class="w-full h-full object-cover object-center transition duration-300">
            </div>

            @if($imagesList->count() > 1)
                <div class="flex space-x-2 md:space-x-3 overflow-x-auto pb-1 md:pb-2">
                    @foreach($imagesList as $img)
                        <button type="button" 
                                @click="activeImage = '{{ Storage::url($img->image) }}'; resetAutoSlide()"
                                class="h-16 w-16 md:h-20 md:w-20 bg-white border rounded-lg md:rounded-xl overflow-hidden flex-shrink-0 hover:border-purple-500 focus:outline-none focus:border-purple-500 transition-all"
                                :class="activeImage === '{{ Storage::url($img->image) }}' ? 'border-purple-600 ring-2 ring-purple-100' : 'border-gray-200'">
                            <img src="{{ Storage::url($img->image) }}" class="h-full w-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right: Info & Form -->
        <div>
            
            <!-- Title + Badge -->
            <div>
                @if($product->is_featured)
                    <span class="text-[10px] font-extrabold bg-purple-50 text-primary border border-purple-100 px-2.5 py-0.5 rounded-full">Featured</span>
                @endif
                <h1 class="text-xl md:text-3xl font-extrabold text-gray-900 {{ $product->is_featured ? 'mt-1' : '' }}">{{ $product->name }}</h1>
            </div>

            <!-- Price -->
            <div class="flex items-baseline gap-3 pt-1 md:pt-2">
                @if($product->sale_price !== null)
                    <span class="font-bold text-primary text-xl md:text-2xl" x-text="formattedPrice">৳{{ number_format($product->sale_price, 2) }}</span>
                    <span class="text-sm text-gray-400 line-through">৳{{ number_format($product->regular_price, 2) }}</span>
                @else
                    <span class="font-bold text-primary text-xl md:text-2xl" x-text="formattedPrice">৳{{ number_format($product->regular_price, 2) }}</span>
                @endif
            </div>

            <!-- Short Description -->
            @if($product->short_description)
                <div class="text-base text-gray-600 leading-relaxed [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_a]:text-purple-600 [&_a]:underline p-2 bg-gray-100 border border-slate-500 rounded-md">{!! $product->short_description !!}</div>
            @endif

            <!-- Form -->
            <form action="{{ route('cart.add') }}" method="POST" class="space-y-2 md:space-y-3 border-t border-gray-200">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                @if($product->product_type === 'variable')
                    <input type="hidden" name="variation_id" x-model="selectedVariationId">

                    <div class="space-y-3 md:space-y-4 bg-purple-50/30 p-3 md:p-4 rounded-xl border border-purple-100/50">
                        @php
                            $sizes = $product->variations->pluck('size')->filter()->unique()->values()->toArray();
                            $colors = $product->variations->pluck('color')->filter()->unique()->values()->toArray();
                        @endphp

                        @if(count($colors) > 0)
                            <div>
                                <label class="block text-xs text-gray-500 font-bold mb-2">রং</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($colors as $color)
                                        <button type="button"
                                                @click="selectedColor = '{{ $color }}'; matchVariation()"
                                                class="w-9 h-9 md:w-10 md:h-10 rounded-full border-2 transition-all flex items-center justify-center font-bold text-xs hover:scale-105"
                                                :class="selectedColor === '{{ $color }}' ? 'border-purple-600 ring-2 ring-purple-200 shadow' : 'border-gray-300 hover:border-purple-400'"
                                                style="background-color: {{ strtolower($color) }}; color: {{ in_array(strtolower($color), ['white', 'yellow', 'lime']) ? '#333' : 'white' }};"
                                                title="{{ $color }}">
                                            <span x-show="selectedColor === '{{ $color }}'" class="text-sm">✓</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(count($sizes) > 0)
                            <div>
                                <label class="block text-xs text-gray-500 font-bold mb-2">সাইজ</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($sizes as $size)
                                        <button type="button"
                                                @click="selectedSize = '{{ $size }}'; matchVariation()"
                                                class="px-4 py-2 rounded-lg border-2 font-bold text-sm transition-all active:scale-95 min-w-[3rem] text-center"
                                                :class="selectedSize === '{{ $size }}' ? 'bg-purple-600 border-purple-600 text-white shadow' : 'bg-white border-gray-300 text-gray-700 hover:border-purple-400 hover:bg-gray-50'">
                                            {{ $size }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Quantity + Buttons -->
                <div class="flex items-center gap-2">
                    <label for="quantity" class="sr-only">Quantity</label>
                    <div class="flex items-center bg-gray-50 border border-gray-500 rounded-lg overflow-hidden flex-shrink-0">
                        <button type="button" @click="if(qty > 1) qty--" class="px-2.5 py-3 text-gray-600 hover:bg-gray-200 focus:outline-none text-base font-bold">−</button>
                        <input type="number" id="quantity" name="quantity" x-model="qty" readonly class="w-14 text-center bg-transparent border-0 text-sm focus:ring-0 p-0 font-bold text-gray-900">
                        <button type="button" @click="qty++" class="px-2.5 py-3 text-gray-600 hover:bg-gray-200 focus:outline-none text-base font-bold">+</button>
                    </div>
                    @if($product->product_type === 'simple' && $product->stock_quantity <= 0)
                        <button type="button" disabled class="flex-1 py-2.5 bg-gray-200 text-gray-400 rounded font-bold cursor-not-allowed text-lg">❌ স্টক নেই</button>
                    @elseif($product->product_type === 'simple')
                        <button type="submit" class="flex-1 py-2.5 bg-sky-100 hover:bg-sky-200 text-sky-800 rounded font-bold transition-all duration-200 shadow text-lg hover:shadow-md active:scale-95 border-2 border-sky-400">
                            🛒 কার্টে যোগ করুন
                        </button>
                    @else
                        <button type="submit"
                                x-bind:disabled="!selectedVariationId || variationStock <= 0"
                                class="flex-1 py-2.5 rounded font-bold transition-all duration-200 shadow text-lg hover:shadow-md active:scale-95 border-2"
                                :class="selectedVariationId && variationStock > 0 ? 'bg-sky-100 hover:bg-sky-200 text-sky-800 border-sky-400 cursor-pointer' : 'bg-gray-200 text-gray-400 border-gray-300 cursor-not-allowed'"
                                x-text="buttonText">
                            🛒 কার্টে যোগ করুন
                        </button>
                    @endif
                </div>

                @if($product->product_type === 'simple')
                    @if($product->stock_quantity > 0)
                        <button type="submit" name="buy_now" value="1" class="w-full py-4 md:py-5 relative overflow-hidden bg-sky-800 hover:bg-blue-800 text-white rounded font-bold transition-all duration-300 shadow-lg hover:shadow-xl text-lg animate-buy-now hover:scale-105 active:scale-95">
                            <span class="relative z-10">🔥 এখনই অর্ডার করুন</span>
                            <span class="shine"></span>
                        </button>
                    @endif
                @else
                    <button type="submit" name="buy_now" value="1"
                            x-show="selectedVariationId && variationStock > 0"
                            class="w-full py-4 md:py-5 relative overflow-hidden bg-sky-800 hover:bg-blue-800 text-white rounded font-bold transition-all duration-300 shadow-lg hover:shadow-xl text-lg animate-buy-now hover:scale-105 active:scale-95">
                        <span class="relative z-10">🔥 এখনই অর্ডার করুন</span>
                        <span class="shine"></span>
                    </button>
                @endif
            </form>

            <!-- Stock -->
            <div class="pt-3 md:pt-4">
                @if($product->product_type === 'simple')
                    @if($product->stock_quantity > 0)
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 bg-green-50 text-green-700 rounded-full border border-green-200">✅ স্টকে আছে</span>
                    @else
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 bg-red-50 text-red-700 rounded-full border border-red-200">❌ স্টক আউট</span>
                    @endif
                @else
                    <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full border"
                          :class="variationStock > 0 ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'"
                          x-text="stockStatusText">
                    </span>
                @endif
            </div>

            <!-- WhatsApp Contact -->
            @php
                $whatsappNumber = App\Models\Setting::getValue('whatsapp_number', '');
            @endphp
            @if($whatsappNumber)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsappNumber) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="mt-3 md:mt-4 flex items-center justify-center gap-2 w-full py-5 md:py-6 bg-lime-100 hover:bg-green-300 text-green-700 hover:text-black rounded-lg font-bold transition-all duration-200 shadow hover:shadow-md active:scale-95 text-lg md:text-xl border-green-500 border-2">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    <span>WhatsApp-এ যোগাযোগ করুন</span>
                </a>
            @endif

            <!-- Trust Features -->
            <div class="grid grid-cols-2 gap-2 md:gap-3 mt-3 md:mt-4">
                <div class="flex items-start gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs md:text-sm font-bold text-gray-900 leading-tight">দ্রুত ডেলিভারি</p>
                        <p class="text-[11px] md:text-xs text-gray-500 mt-0.5">২-৩ দিনের মধ্যে নিরাপদ ডেলিভারি</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                    <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs md:text-sm font-bold text-gray-900 leading-tight">ক্যাশ অন ডেলিভারি</p>
                        <p class="text-[11px] md:text-xs text-gray-500 mt-0.5">পণ্য হাতে পেয়ে পেমেন্ট করুন</p>
                    </div>
                </div>
            </div>

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

        </div>
    </div>

    <!-- Full Description -->
    @if($product->description)
        <div class="mt-6 md:mt-12 border-t border-gray-200 pt-5 md:pt-8">
            <h3 class="text-lg md:text-2xl font-extrabold text-gray-900 mb-3 md:mb-5">📄 বিস্তারিত</h3>
            <div class="text-base text-gray-600 leading-relaxed [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_a]:text-purple-600 [&_a]:underline max-w-none">{!! $product->description !!}</div>
        </div>
    @endif

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <section class="mt-8 md:mt-16 border-t border-gray-200 pt-6 md:pt-12 space-y-4 md:space-y-6">
            <h2 class="text-lg md:text-2xl font-extrabold text-gray-900 text-center">আরও দেখুন</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 md:gap-6">
                @foreach($relatedProducts as $rel)
                    @include('store.partials.product-card', ['product' => $rel])
                @endforeach
            </div>
        </section>
    @endif

</div>
@endsection

@section('scripts')
<style>
    .shine {
        position: absolute;
        top: 0;
        left: -75%;
        width: 50%;
        height: 100%;
        background: linear-gradient(100deg, transparent 20%, rgba(255,255,255,0.5) 50%, transparent 80%);
        transform: skewX(-20deg);
        animation: shine-sweep 2.5s ease-in-out infinite;
    }

    @keyframes shine-sweep {
        0% { left: -75%; }
        50% { left: 125%; }
        100% { left: -75%; }
    }

    @keyframes buy-now-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.03); }
    }

    .animate-buy-now {
        animation: buy-now-pulse 2s ease-in-out infinite;
    }

    .animate-buy-now:hover {
        animation: none;
    }
</style>
<script>
    function productDetail(variations) {
        return {
            variations: variations || [],
            activeImage: '{{ $featuredImg ? Storage::url($featuredImg->image) : "/placeholder.png" }}',
            selectedSize: '',
            selectedColor: '',
            qty: 1,
            selectedVariationId: '',
            variationStock: 0,
            variationPrice: 0,
            
            formattedPrice: '৳{{ number_format($product->getDisplayPrice(), 2) }}',
            stockStatusText: 'অপশন নির্বাচন করুন',
            buttonText: 'অপশন নির্বাচন করুন',

            imageUrls: {!! json_encode($imagesList->pluck('image')->map(fn($i) => Storage::url($i))->values()) !!},
            autoSlideInterval: null,
            slideIndex: 0,

            init() {
                this.startAutoSlide();
            },

            startAutoSlide() {
                if (this.autoSlideInterval) clearInterval(this.autoSlideInterval);
                if (this.imageUrls.length < 2) return;
                this.autoSlideInterval = setInterval(() => {
                    this.slideIndex = (this.slideIndex + 1) % this.imageUrls.length;
                    this.activeImage = this.imageUrls[this.slideIndex];
                }, 4000);
            },

            resetAutoSlide() {
                this.slideIndex = this.imageUrls.indexOf(this.activeImage);
                if (this.slideIndex === -1) this.slideIndex = 0;
                this.startAutoSlide();
            },

            matchVariation() {
                if (!this.selectedSize && !this.selectedColor) {
                    this.selectedVariationId = '';
                    this.variationStock = 0;
                    this.formattedPrice = '৳{{ number_format($product->getDisplayPrice(), 2) }}';
                    this.stockStatusText = 'অপশন নির্বাচন করুন';
                    this.buttonText = 'অপশন নির্বাচন করুন';
                    return;
                }

                var basePrice = {{ $product->getDisplayPrice() ?: 0 }};
                let match = this.variations.find(v => {
                    let sizeMatch = !v.size || v.size === this.selectedSize;
                    let colorMatch = !v.color || v.color === this.selectedColor;
                    return sizeMatch && colorMatch;
                });

                if (match) {
                    this.selectedVariationId = match.id;
                    this.variationStock = match.stock;
                    this.variationPrice = match.price ? parseFloat(match.price) : basePrice;
                    this.formattedPrice = '৳' + this.variationPrice.toFixed(2);
                    
                    if (match.stock > 0) {
                        this.stockStatusText = '✅ স্টকে আছে';
                        this.buttonText = '🛒 কার্টে যোগ করুন';
                    } else {
                        this.stockStatusText = '❌ স্টক আউট';
                        this.buttonText = '❌ স্টক নেই';
                    }
                } else {
                    this.selectedVariationId = '';
                    this.variationStock = 0;
                    this.stockStatusText = 'এই কম্বিনেশন নেই';
                    this.formattedPrice = 'N/A';
                    this.buttonText = 'কম্বিনেশন নেই';
                }
            }
        }
    }
</script>
@endsection
