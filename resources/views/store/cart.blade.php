@extends('layouts.store')

@section('title', 'Shopping Cart - ' . site_name())

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 mb-8">Your Shopping Cart</h1>

    @if(count($cart) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4" id="cart-items-container">
                @foreach($cart as $key => $item)
                    <div class="flex items-start bg-white border border-gray-100 rounded-2xl p-4 gap-5 shadow-sm" id="cart-item-{{ md5($key) }}" data-cart-key="{{ $key }}">
                        
                        <!-- Product Image -->
                        <div class="w-24 h-24 bg-gray-50 border border-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                            @if($item['image'])
                                <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-purple-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Item Details -->
                        <div class="flex-grow space-y-1">
                            <h3 class="font-bold text-gray-900 text-sm">{{ $item['name'] }}</h3>
                            @if($item['variation_details'])
                                <p class="text-[11px] font-semibold text-purple-600">{{ $item['variation_details'] }}</p>
                            @endif
                            <p class="text-xs text-gray-400 font-semibold">৳{{ number_format($item['price'], 2) }} each</p>
                        </div>

                        <!-- Quantity Controls -->
                        <div class="flex flex-col items-center space-y-2">
                            <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
                                <button type="button" class="cart-qty-btn px-2.5 py-1.5 text-gray-600 hover:bg-gray-150 focus:outline-none text-sm"
                                        data-key="{{ $key }}" data-action="decrease" data-item-id="{{ md5($key) }}">-</button>
                                <span class="px-3 py-1.5 text-sm font-bold text-gray-900 qty-display" id="qty-{{ md5($key) }}">{{ $item['quantity'] }}</span>
                                <button type="button" class="cart-qty-btn px-2.5 py-1.5 text-gray-600 hover:bg-gray-150 focus:outline-none text-sm"
                                        data-key="{{ $key }}" data-action="increase" data-item-id="{{ md5($key) }}">+</button>
                            </div>
                            
                            <!-- Line Total -->
                            <span class="font-bold text-gray-900 text-sm" id="item-total-{{ md5($key) }}">৳{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            
                            <!-- Remove -->
                            <form action="{{ route('cart.remove') }}" method="POST" class="cart-remove-form" data-key="{{ $key }}" data-item-id="{{ md5($key) }}">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key }}">
                                <button type="submit" class="text-[11px] font-bold text-pink-600 hover:text-pink-700 uppercase tracking-wider">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Summary Sidebar -->
            <aside class="space-y-6">
                <div class="bg-white border border-gray-100 p-6 rounded-2xl shadow-sm space-y-4">
                    <h3 class="font-bold text-gray-900 text-lg">Order Summary</h3>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal ({{ count($cart) }} items)</span>
                            <span class="font-semibold text-gray-900" id="cart-subtotal">
                                ৳{{ number_format(array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart)), 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-gray-400 text-xs">
                            <span>Shipping</span>
                            <span>Calculated at checkout</span>
                        </div>
                        @if(!empty($freeShippingMin))
                            @php
                                $cartSubtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
                                $remainingForFree = max(0, (float) $freeShippingMin - $cartSubtotal);
                            @endphp
                            @if($remainingForFree > 0)
                                <div class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                                    🎁 আরও <b>৳{{ number_format($remainingForFree, 0) }}</b> অর্ডার করলেই পাবেন <b class="text-emerald-700">ফ্রি ডেলিভারি</b>
                                </div>
                            @else
                                <div class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">
                                    🎉 অভিনন্দন! আপনার অর্ডারে পাচ্ছেন <b>ফ্রি ডেলিভারি</b>
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="border-t border-gray-100 pt-4 flex justify-between font-bold text-gray-950">
                        <span>Estimated Total</span>
                        <span id="cart-grand-total">
                            ৳{{ number_format(array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart)), 2) }}
                        </span>
                    </div>

                    <a href="{{ route('checkout') }}" class="block w-full text-center py-3 bg-primary hover:bg-primary-dark text-white rounded-lg font-bold tracking-wide transition shadow-sm">
                        Proceed to Checkout
                    </a>
                    
                    <a href="{{ route('shop') }}" class="block w-full text-center py-2 text-xs font-bold text-purple-600 hover:text-primary uppercase tracking-wider">
                        ← Continue Shopping
                    </a>
                </div>
            </aside>
        </div>
    @else
        <!-- Empty Cart State -->
        <div class="flex flex-col items-center py-24 space-y-6 text-center">
            <div class="w-24 h-24 bg-purple-50 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Your cart is empty</h2>
                <p class="text-gray-400 text-sm mt-1">Looks like you haven't added anything to your cart yet.</p>
            </div>
            <a href="{{ route('shop') }}" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-500 hover:from-primary hover:to-pink-600 text-white rounded-full font-bold shadow-lg transition">Start Shopping</a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Cart Badge update
    function updateCartBadge(count) {
        const badge = document.getElementById('cart-badge');
        if (badge) {
            badge.textContent = count;
            if (count > 0) {
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    }

    // Quantity button listeners
    document.querySelectorAll('.cart-qty-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const key = this.dataset.key;
            const action = this.dataset.action;
            const itemId = this.dataset.itemId;
            const qtySpan = document.getElementById(`qty-${itemId}`);
            let currentQty = parseInt(qtySpan.textContent);
            let newQty = action === 'increase' ? currentQty + 1 : Math.max(1, currentQty - 1);

            const response = await fetch('{{ route("cart.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ key, quantity: newQty }),
            });

            const data = await response.json();

            if (data.success) {
                qtySpan.textContent = newQty;
                document.getElementById(`item-total-${itemId}`).textContent = `৳${data.item_total}`;
                document.getElementById('cart-subtotal').textContent = `৳${data.cart_subtotal}`;
                document.getElementById('cart-grand-total').textContent = `৳${data.cart_subtotal}`;
                updateCartBadge(data.cart_count);
            } else {
                alert(data.message || 'Cannot update quantity.');
            }
        });
    });

    // Remove item listeners
    document.querySelectorAll('.cart-remove-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const key = this.dataset.key;
            const itemId = this.dataset.itemId;

            const formData = new FormData(this);
            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            const data = await response.json();
            if (data.success) {
                // Remove item from UI
                const itemEl = document.getElementById(`cart-item-${itemId}`);
                if (itemEl) {
                    itemEl.remove();
                }
                document.getElementById('cart-subtotal').textContent = `৳${data.cart_subtotal}`;
                document.getElementById('cart-grand-total').textContent = `৳${data.cart_subtotal}`;
                updateCartBadge(data.cart_count);

                if (data.cart_count === 0) {
                    location.reload();
                }
            }
        });
    });

    
</script>
@endsection
