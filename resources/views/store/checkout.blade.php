@extends('layouts.store')

@section('title', 'চেকআউট - ' . site_name())

@section('pixel_events')
@if(isset($pixelEventId, $pixelEventData))
<script>
fbq('track', 'InitiateCheckout', @json($pixelEventData), { eventID: @json($pixelEventId) });
</script>
@endif
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4"
     x-data="checkoutPage(@json($deliveryZones), {{ $qualifiesForFreeShipping ? 'true' : 'false' }}, {{ $freeShippingThreshold ?? 0 }})">

    <h1 class="text-xl font-extrabold text-gray-900 mb-3">🔒 চেকআউট</h1>

    @if(session('error'))
        <div class="mb-3 bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-sm font-semibold">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('checkout.place') }}" method="POST" id="checkout-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <!-- Left: Customer Form -->
            <div class="lg:col-span-2 space-y-3">

                <!-- Customer Information -->
                <div class="bg-white border border-gray-200 rounded-xl p-4 space-y-3 shadow-sm">
                    <h2 class="font-bold text-gray-900 text-base border-b border-gray-200 pb-2">📋 আপনার তথ্য</h2>

                    <div class="space-y-3">

                        <!-- Full Name -->
                        <div>
                            <label for="customer_name" class="block text-sm font-bold text-gray-600 mb-1">
                                আপনার নাম <span class="text-pink-500">*</span>
                            </label>
                            <input type="text" id="customer_name" name="customer_name"
                                   value="{{ old('customer_name') }}" required
                                   class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                                   placeholder="আপনার পুরো নাম লিখুন">
                            @error('customer_name')
                                <span class="text-sm text-red-500 mt-0.5 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="customer_phone" class="block text-sm font-bold text-gray-600 mb-1">
                                মোবাইল নম্বর <span class="text-pink-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-base font-semibold select-none">🇧🇩</span>
                                <input type="tel" id="customer_phone" name="customer_phone"
                                       value="{{ old('customer_phone') }}" required
                                       pattern="^(\+?88)?01[3-9]\d{8}$"
                                       class="w-full bg-white border border-gray-300 rounded-lg pl-9 pr-3 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                                       placeholder="01XXXXXXXXX">
                            </div>
                            @error('customer_phone')
                                <span class="text-sm text-red-500 mt-0.5 block">{{ $message }}</span>
                            @enderror
                            <p class="text-xs text-gray-400 mt-0.5">ফরম্যাট: 01XXXXXXXXX (বাংলাদেশি নম্বর)</p>
                        </div>

                        <!-- Address (reduced height) -->
                        <div>
                            <label for="customer_address" class="block text-sm font-bold text-gray-600 mb-1">
                                ঠিকানা <span class="text-pink-500">*</span>
                            </label>
                            <textarea id="customer_address" name="customer_address" rows="2" required
                                      class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2.5 text-base focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                                      placeholder="বাসা / রাস্তা / এলাকা / শহর / জেলা">{{ old('customer_address') }}</textarea>
                            @error('customer_address')
                                <span class="text-sm text-red-500 mt-0.5 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Delivery Zone -->
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-2">
                                ডেলিভারি জোন <span class="text-pink-500">*</span>
                            </label>

                            @if($qualifiesForFreeShipping)
                                <!-- Free Delivery — only option, auto-selected -->
                                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                    <input type="hidden" name="delivery_zone" value="">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="delivery_zone" value="free" checked
                                               class="w-4 h-4 text-emerald-600 focus:ring-emerald-500"
                                               disabled>
                                        <div>
                                            <div class="font-bold text-gray-900 text-sm">ফ্রি ডেলিভারি 🎉</div>
                                            <div class="text-xs text-emerald-600">
                                                ৳{{ number_format($freeShippingThreshold, 0) }}+ অর্ডারে বিনামূল্যে ডেলিভারি
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Paid shipping zones -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($deliveryZones as $zone)
                                        <label class="relative flex items-start p-3 cursor-pointer rounded-lg border-2 transition-all"
                                               :class="selectedZone === '{{ $zone->zone }}' ? 'border-purple-500 bg-purple-50' : 'border-gray-300 bg-white hover:border-purple-400 hover:bg-gray-50'">
                                            <div class="flex items-center h-5">
                                                <input type="radio" name="delivery_zone" value="{{ $zone->zone }}"
                                                       @change="selectZone('{{ $zone->zone }}')"
                                                       x-model="selectedZone"
                                                       class="h-4 w-4 border-gray-400 text-purple-600 focus:ring-purple-500"
                                                       {{ old('delivery_zone') === $zone->zone ? 'checked' : '' }}>
                                            </div>
                                            <div class="ml-2 flex-1">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-bold text-gray-900 text-sm">{{ $zone->zone }}</span>
                                                    <span class="font-bold text-primary text-sm">৳{{ number_format($zone->charge, 0) }}</span>
                                                </div>
                                                @if($zone->estimated_days)
                                                    <p class="text-xs text-gray-400">⏱ {{ $zone->estimated_days }}-এর মধ্যে ডেলিভারি</p>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                @if($freeShippingThreshold > 0)
                                    <div class="mt-2 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-1.5">
                                        🎁 ৳{{ number_format($freeShippingThreshold, 0) }}+ অর্ডারে পাবেন <b>ফ্রি ডেলিভারি</b>
                                    </div>
                                @endif
                            @endif

                            @error('delivery_zone')
                                <span class="text-sm text-red-500 mt-0.5 block">{{ $message }}</span>
                            @enderror

                            <div x-show="selectedZoneDays && !qualifiesForFreeShipping" x-cloak
                                 class="mt-1.5 text-sm font-semibold text-primary bg-purple-50 border border-purple-200 px-3 py-1.5 rounded-md">
                                🚚 আনুমানিক ডেলিভারি: <span x-text="selectedZoneDays" class="font-bold"></span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <h2 class="font-bold text-gray-900 text-base mb-2">💳 পেমেন্ট মেথড</h2>
                    <div class="flex items-center gap-2 bg-purple-50 border border-purple-200 rounded-lg px-4 py-3">
                        <div class="w-9 h-9 rounded-full bg-purple-100 flex items-center justify-center text-lg">💵</div>
                        <div>
                            <p class="font-bold text-gray-900 text-sm">ক্যাশ অন ডেলিভারি</p>
                            <p class="text-gray-500 text-xs">অর্ডার আপনার দরজায় পৌঁছালে পেমেন্ট করুন</p>
                        </div>
                        <div class="ml-auto w-4 h-4 rounded-full bg-purple-600 border-2 border-white ring-2 ring-purple-400"></div>
                    </div>
                </div>

            </div>

            <!-- Right: Order Summary -->
            <aside class="space-y-3">
                <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm space-y-3 sticky top-20">
                    <h3 class="font-bold text-gray-900 text-base">🛒 অর্ডার সারাংশ</h3>

                    <!-- Cart Items -->
                    <div class="space-y-2">
                        @php $subtotal = 0; @endphp
                        @foreach($cart as $key => $item)
                            @php $subtotal += $item['price'] * $item['quantity']; @endphp
                            <div class="flex justify-between items-start text-sm">
                                <div class="flex-1 pr-2">
                                    <span class="font-semibold text-gray-900">{{ $item['name'] }}</span>
                                    @if($item['variation_details'])
                                        <span class="text-xs text-purple-500 font-semibold block">{{ $item['variation_details'] }}</span>
                                    @endif
                                    <span class="text-gray-400 text-xs">× {{ $item['quantity'] }}</span>
                                </div>
                                <span class="font-semibold text-gray-900 whitespace-nowrap">৳{{ number_format($item['price'] * $item['quantity'], 0) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-200 pt-3 space-y-1.5 text-sm">
                        <div class="flex justify-between text-gray-500">
                            <span>সাবটোটাল</span>
                            <span class="font-semibold text-gray-900">৳{{ number_format($subtotal, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>ডেলিভারি চার্জ</span>
                            <span class="font-semibold"
                                  :class="isFreeShipping ? 'text-emerald-600' : 'text-gray-900'"
                                  x-text="formattedShippingCharge">জোন নির্বাচন করুন</span>
                        </div>
                        <div class="text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-md px-2 py-1.5"
                             x-show="!qualifiesForFreeShipping && freeShippingRemaining > 0" x-cloak>
                            🎁 আরও <span x-text="'৳' + Math.round(freeShippingRemaining)"></span> অর্ডার করলেই পাবেন <b>ফ্রি ডেলিভারি</b>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-3 flex justify-between font-bold text-gray-900">
                        <span>সর্বমোট</span>
                        <span class="text-purple-600" x-text="formattedGrandTotal">৳{{ number_format($subtotal, 0) }}</span>
                    </div>

                    <button type="submit"
                            class="block w-full text-center py-4 bg-primary hover:bg-primary text-white rounded-lg font-bold transition shadow-md active:scale-95 text-lg">
                        ✅ অর্ডার কনফার্ম করুন
                    </button>

                    <a href="{{ route('cart') }}"
                       class="block w-full text-center py-1.5 text-xs font-bold text-gray-400 hover:text-gray-700 transition">
                        ← কার্ট এডিট করুন
                    </a>
                </div>
            </aside>
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
    function checkoutPage(zones, qualifiesForFreeShipping, freeShippingThreshold) {
        const subtotal = {{ $subtotal ?? 0 }};

        const zoneMap = {};
        zones.forEach(z => {
            zoneMap[z.zone] = { charge: parseFloat(z.charge), days: z.estimated_days || '' };
        });

        return {
            subtotal: subtotal,
            qualifiesForFreeShipping: qualifiesForFreeShipping,
            freeShippingThreshold: parseFloat(freeShippingThreshold) || 0,
            selectedZoneDays: '',
            selectedZone: qualifiesForFreeShipping ? 'free' : '{{ old('delivery_zone') }}',

            init() {
                if (!qualifiesForFreeShipping && this.selectedZone && zoneMap[this.selectedZone]) {
                    this.selectZone(this.selectedZone);
                }
            },

            get selectedZoneData() {
                return (this.selectedZone && zoneMap[this.selectedZone]) ? zoneMap[this.selectedZone] : null;
            },

            get isFreeShipping() {
                return this.qualifiesForFreeShipping;
            },

            get shippingCharge() {
                if (this.qualifiesForFreeShipping) return 0;
                const zone = this.selectedZoneData;
                return zone ? zone.charge : 0;
            },

            get freeShippingRemaining() {
                if (this.qualifiesForFreeShipping) return 0;
                return Math.max(0, this.freeShippingThreshold - this.subtotal);
            },

            get formattedShippingCharge() {
                if (this.qualifiesForFreeShipping) return 'ফ্রি ডেলিভারি 🎉';
                if (!this.selectedZoneData) return 'জোন নির্বাচন করুন';
                return '৳' + Math.round(this.shippingCharge);
            },

            get formattedGrandTotal() {
                return '৳' + Math.round(this.subtotal + this.shippingCharge);
            },

            selectZone(zoneName) {
                if (zoneName && zoneMap[zoneName]) {
                    this.selectedZone = zoneName;
                    this.selectedZoneDays = zoneMap[zoneName].days || '';
                } else {
                    this.selectedZone = '';
                    this.selectedZoneDays = '';
                }
            }
        }
    }
</script>
@endsection
