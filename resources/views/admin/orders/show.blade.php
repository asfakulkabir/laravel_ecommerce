@extends('layouts.admin')

@section('title', "Order Details #{$order->id} - Outfitt")
@section('page_title', "Order Details #{$order->id}")

@section('content')
<div class="space-y-8 max-w-6xl">

    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold uppercase tracking-wider text-purple-400 hover:text-purple-300">← Back to Orders</a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-lg text-sm font-semibold">✅ {{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left 2 Columns: Order Items + Customer Info -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Items Card -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800/50 rounded-2xl shadow-lg overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-800/50 bg-slate-800/50">
                    <h3 class="font-bold text-white">Order Items</h3>
                </div>

                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-800/30 border-b border-slate-800/50 text-sm font-bold uppercase text-slate-300">
                            <th class="px-8 py-4">Product</th>
                            <th class="px-8 py-4 text-center">Price</th>
                            <th class="px-8 py-4 text-center">Qty</th>
                            <th class="px-8 py-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/30 text-slate-400">
                        @foreach($order->items as $item)
                            @php
                                $isArray = is_array($item);
                                $name    = $isArray ? ($item['name']              ?? '—') : $item->product_name;
                                $varDet  = $isArray ? ($item['variation_details'] ?? '')  : $item->variation_details;
                                $qty     = $isArray ? ($item['quantity']          ?? 1)   : $item->quantity;
                                $price   = $isArray ? ($item['price']             ?? 0)   : $item->price;
                            @endphp
                            <tr>
                                <td class="px-8 py-4">
                                    <div class="font-semibold text-slate-200">{{ $name }}</div>
                                    @if($varDet)
                                        <div class="text-sm text-purple-400 font-semibold mt-1">Option: {{ $varDet }}</div>
                                    @endif
                                </td>
                                <td class="px-8 py-4 text-center">৳{{ number_format($price, 0) }}</td>
                                <td class="px-8 py-4 text-center font-semibold text-slate-200">{{ $qty }}</td>
                                <td class="px-8 py-4 text-right font-bold text-white">৳{{ number_format($price * $qty, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Total Summary -->
                @php
                    $deliveryCharge = $order->deliveryCharge;
                    $chargeAmount   = $order->actualShippingCharge();
                    $zoneName       = $deliveryCharge ? $deliveryCharge->zone   : 'N/A';
                    $subtotalCalc   = $order->total_amount - $chargeAmount;
                    $isFreeShipping = $chargeAmount <= 0;
                @endphp
                <div class="p-8 bg-slate-800/50 border-t border-slate-800/50 space-y-3 text-sm flex flex-col items-end">
                    <div class="flex justify-between w-72">
                        <span class="text-slate-300">Subtotal:</span>
                        <span class="font-semibold text-slate-200">৳{{ number_format($subtotalCalc, 0) }}</span>
                    </div>
                    <div class="flex justify-between w-72">
                        <span class="text-slate-300">Delivery ({{ $zoneName }}):</span>
                        <span class="font-semibold {{ $isFreeShipping ? 'text-emerald-400' : 'text-slate-200' }}">
                            {{ $isFreeShipping ? 'ফ্রি ডেলিভারি 🎉' : '৳' . number_format($chargeAmount, 0) }}
                        </span>
                    </div>
                    <div class="flex justify-between w-72 border-t border-slate-700/50 pt-3 text-lg font-bold text-white">
                        <span>Grand Total:</span>
                        <span class="text-purple-400">৳{{ number_format($order->total_amount, 0) }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer & Delivery Details -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800/50 p-8 rounded-2xl shadow-lg space-y-4">
                <h3 class="font-bold text-white border-b border-slate-800/50 pb-2">Customer & Delivery Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-sm font-bold text-slate-300 uppercase">Customer Name</p>
                        <p class="font-semibold text-slate-200 mt-1">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-300 uppercase">Mobile Number</p>
                        <p class="font-semibold text-slate-200 mt-1 font-mono">{{ $order->customer_phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-300 uppercase">Payment Method</p>
                        <p class="font-semibold text-slate-200 mt-1">{{ $order->payment_method }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-300 uppercase">Delivery Zone</p>
                        <p class="font-semibold text-slate-200 mt-1">{{ $zoneName }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm font-bold text-slate-300 uppercase">Delivery Address</p>
                        <p class="font-semibold text-slate-200 mt-1 whitespace-pre-line bg-slate-800/50 p-4 rounded-lg border border-slate-800/30">{{ $order->customer_address }}</p>
                    </div>
                </div>
            </div>

            <!-- Steadfast Info (if applicable) -->
            @if($order->is_sent_to_steadfast)
            <div class="bg-emerald-500/10 border border-emerald-500/20 p-6 rounded-2xl space-y-2 text-sm">
                <h3 class="font-bold text-emerald-400">✅ Sent to Steadfast</h3>
                @if($order->steadfast_consignment_id)
                    <p class="text-slate-400">Consignment ID: <span class="font-mono font-bold text-emerald-400">{{ $order->steadfast_consignment_id }}</span></p>
                @endif
                @if($order->is_notification_sent)
                    <p class="text-slate-300 text-sm">📱 Customer notification sent</p>
                @endif
            </div>
            @endif

        </div>

        <!-- Right Column: Status & Meta Controls -->
        <div class="space-y-6">
            <!-- Order Meta -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800/50 p-6 rounded-2xl shadow-lg space-y-4 text-sm">
                <h3 class="font-bold text-slate-200 text-sm uppercase tracking-wider">Order Info</h3>
                <div class="space-y-3 text-slate-400">
                    <div class="flex justify-between">
                        <span class="text-slate-300">Order Date</span>
                        <span class="font-semibold">{{ $order->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-300">Order Time</span>
                        <span class="font-semibold">{{ $order->created_at->format('h:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-300">Steadfast</span>
                        <span class="font-semibold {{ $order->is_sent_to_steadfast ? 'text-emerald-400' : 'text-slate-300' }}">
                            {{ $order->is_sent_to_steadfast ? 'Sent ✅' : 'Pending' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-300">Notification</span>
                        <span class="font-semibold {{ $order->is_notification_sent ? 'text-emerald-400' : 'text-slate-300' }}">
                            {{ $order->is_notification_sent ? 'Sent ✅' : 'Not Sent' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Send to Steadfast -->
            @unless($order->is_sent_to_steadfast)
            <div class="bg-slate-900 text-white p-6 rounded-2xl border border-slate-800 space-y-6 shadow-xl">
                <div>
                    <h3 class="font-bold text-sm uppercase tracking-wider text-emerald-400">Steadfast Courier</h3>
                    <p class="text-sm text-slate-400 mt-1">Send this order to Steadfast for shipping</p>
                </div>
                <form action="{{ route('admin.orders.send-to-steadfast', $order->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full py-2.5 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 rounded-lg text-sm font-bold uppercase tracking-wider transition-all">
                        Send to Steadfast
                    </button>
                </form>
            </div>
            @endunless

            <!-- Status Update -->
            <div class="bg-slate-900 text-white p-6 rounded-2xl border border-slate-800 space-y-6 shadow-xl">
                <div>
                    <h3 class="font-bold text-sm uppercase tracking-wider text-pink-400">Order Management</h3>
                    <p class="text-sm text-slate-400 mt-1">Change order fulfillment status</p>
                </div>

                <!-- Status Badge -->
                <div class="py-2 px-4 bg-slate-950 border border-slate-800 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-slate-400">Current Status:</span>
                    @php
                        $statusColors = [
                            'processing' => 'text-blue-400',
                            'shipped'    => 'text-purple-400',
                            'delivered'  => 'text-green-400',
                            'cancelled'  => 'text-red-400',
                        ];
                        $color = $statusColors[$order->status] ?? 'text-white';
                    @endphp
                    <span class="text-sm font-bold {{ $color }}">
                        {{ \App\Models\Order::STATUSES[$order->status] ?? ucfirst($order->status) }}
                    </span>
                </div>

                <!-- Update Status Form -->
                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="space-y-4 pt-4 border-t border-slate-800">
                    @csrf
                    <div>
                        <label for="status" class="block text-sm font-bold text-slate-400 uppercase mb-2">Change Status To:</label>
                        <select id="status" name="status"
                                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-1 focus:ring-pink-500 transition-all">
                            @foreach(\App\Models\Order::STATUSES as $val => $label)
                                <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 text-white rounded-lg text-sm font-bold uppercase tracking-wider transition-all">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Delete Order -->
            <div class="bg-slate-900 border border-red-800/40 p-6 rounded-2xl space-y-4">
                <div>
                    <h3 class="font-bold text-sm uppercase tracking-wider text-red-400">Danger Zone</h3>
                    <p class="text-sm text-slate-400 mt-1">Permanently delete this order.</p>
                </div>
                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete Order #{{ $order->id }}? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full py-2.5 bg-red-600/20 hover:bg-red-600/30 text-red-400 border border-red-500/30 rounded-lg text-sm font-bold uppercase tracking-wider transition-all">
                        Delete Order
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[action*="send-to-steadfast"]').forEach(function (form) {
            form.addEventListener('submit', function () {
                var btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Sending...';
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            });
        });
    });
</script>
@endsection
