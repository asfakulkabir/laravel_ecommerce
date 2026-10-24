@extends('layouts.store')

@section('title', "Order Placed Successfully! #{{ $order->id }} - " . site_name())

@section('pixel_events')
@if($pixelPurchase)
<script>
fbq('track', 'Purchase', @json($pixelPurchase['data']), {
    eventID: @json($pixelPurchase['event_id'])
});
</script>
@endif
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-10">

    <!-- Success Banner -->
    <div class="text-center space-y-5 pb-10">
        <div class="w-20 h-20 bg-gradient-to-r from-emerald-600 to-green-800 rounded-full flex items-center justify-center mx-auto shadow-lg shadow-emerald-900/20">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="space-y-1">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Order Placed Successfully!</h1>
            <p class="text-sm text-gray-500">Thank you, <strong>{{ $order->customer_name }}</strong>! Your order has been received and will be processed shortly.</p>
        </div>
        <div class="inline-flex items-center space-x-3 bg-purple-50 border border-purple-100 px-6 py-3 rounded-full">
            <span class="text-sm text-gray-500">Order ID:</span>
            <span class="text-lg font-extrabold text-primary">#{{ $order->id }}</span>
        </div>
    </div>

    <!-- Invoice Card -->
    <div class="bg-white border border-gray-100 rounded-2xl shadow-md overflow-hidden">

        <!-- Invoice Header -->
        <div class="bg-gradient-to-r from-emerald-950 to-green-900 px-8 py-6 flex justify-between items-center text-white">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ site_name() }}</h1>
            </div>
            <div class="text-right text-xs space-y-0.5">
                <p class="font-extrabold uppercase tracking-widest text-green-300">Order Receipt</p>
                <p class="text-green-200/70">{{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>

        <!-- Order Items -->
        <div class="p-8">
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Items Ordered</h2>
            <div class="space-y-3 mb-6 pb-6 border-b border-gray-100">
                @foreach($order->items as $item)
                    <div class="flex justify-between items-start text-sm">
                        <div>
                            @if(isset($item['name']))
                                {{-- Items from items_json --}}
                                <span class="font-semibold text-gray-900">{{ $item['name'] }}</span>
                                @if(!empty($item['variation_details']))
                                    <span class="text-[11px] text-purple-500 font-semibold block">{{ $item['variation_details'] }}</span>
                                @endif
                            @else
                                {{-- Items from OrderItem relation --}}
                                <span class="font-semibold text-gray-900">{{ $item->product_name }}</span>
                                @if($item->variation_details)
                                    <span class="text-[11px] text-purple-500 font-semibold block">{{ $item->variation_details }}</span>
                                @endif
                            @endif
                        </div>
                        <div class="text-right ml-4">
                            @php
                                $qty   = is_array($item) ? ($item['quantity'] ?? 1) : $item->quantity;
                                $price = is_array($item) ? ($item['price'] ?? 0)    : $item->price;
                            @endphp
                            <span class="text-gray-400 text-xs">× {{ $qty }}</span>
                            <span class="font-bold text-gray-950 block">৳{{ number_format($price * $qty, 0) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Billing Summary -->
            <div class="space-y-2 text-sm">
                @php
                    $deliveryCharge = $order->deliveryCharge;
                    $chargeAmount   = $deliveryCharge ? $deliveryCharge->charge : 0;
                    $zoneName       = $deliveryCharge ? $deliveryCharge->zone   : 'N/A';
                @endphp
                <div class="flex justify-between text-gray-450">
                    <span>Subtotal</span>
                    <span>৳{{ number_format($order->total_amount - $chargeAmount, 0) }}</span>
                </div>
                <div class="flex justify-between text-gray-450">
                    <span>Delivery ({{ $zoneName }})</span>
                    <span>৳{{ number_format($chargeAmount, 0) }}</span>
                </div>
                <div class="flex justify-between font-extrabold text-base text-gray-950 border-t border-gray-100 pt-3 mt-2">
                    <span>Total Amount (COD)</span>
                    <span class="text-primary">৳{{ number_format($order->total_amount, 0) }}</span>
                </div>
            </div>
        </div>

        <!-- Customer & Delivery Details -->
        <div class="px-8 pb-8 border-t border-gray-100 pt-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
            <div class="space-y-3">
                <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400">Customer Details</h2>
                <p class="font-semibold text-gray-900">{{ $order->customer_name }}</p>
                <p class="text-gray-600 font-mono text-xs">{{ $order->customer_phone }}</p>
            </div>
            <div class="space-y-3">
                <h2 class="text-xs font-bold uppercase tracking-wider text-gray-400">Delivery Address</h2>
                <p class="text-gray-600 whitespace-pre-line leading-relaxed">{{ $order->customer_address }}</p>
            </div>
        </div>

        <!-- Status Footer -->
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center text-xs">
            <div>
                <span class="text-gray-400 uppercase font-bold tracking-wider mr-2">Status:</span>
                @php
                    $statusColors = [
                        'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'shipped'    => 'bg-purple-50 text-primary border-purple-200',
                        'delivered'  => 'bg-green-50 text-green-700 border-green-200',
                        'cancelled'  => 'bg-red-50 text-red-700 border-red-200',
                    ];
                    $statusClass = $statusColors[$order->status] ?? 'bg-amber-50 text-amber-700 border-amber-200';
                @endphp
                <span class="px-2.5 py-1 rounded-full text-xs font-bold border uppercase tracking-wider {{ $statusClass }}">
                    {{ \App\Models\Order::STATUSES[$order->status] ?? ucfirst($order->status) }}
                </span>
            </div>
            <span class="text-gray-400">Payment: {{ $order->payment_method }}</span>
        </div>
    </div>

    <!-- CTA Buttons -->
    <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-6 pt-4">
        <a href="{{ route('home') }}" class="px-8 py-3 bg-white border-2 border-purple-200 hover:border-purple-400 text-purple-600 font-bold rounded-full text-center transition">Back to Home</a>
        <a href="{{ route('shop') }}" class="px-8 py-3 bg-gradient-to-r from-emerald-700 to-green-900 hover:from-emerald-600 hover:to-green-800 text-white font-bold rounded-full shadow-lg shadow-emerald-900/15 text-center transition">Continue Shopping</a>
    </div>
</div>
@endsection
