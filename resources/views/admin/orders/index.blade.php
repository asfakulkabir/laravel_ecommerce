@extends('layouts.admin')

@section('title', 'Manage Orders - Outfitt')
@section('page_title', 'Orders Management')

@section('content')
<div class="space-y-6">

    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-grow">
            <div class="relative w-64">
                <input type="text" name="search" placeholder="Search ID, name, phone..." value="{{ request('search') }}"
                       class="w-full bg-slate-900/80 border border-slate-700/50 rounded-lg px-4 py-2 pl-9 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all placeholder:text-slate-300">
                <svg class="w-4 h-4 text-slate-300 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <select name="status" class="bg-slate-900/80 border border-slate-700/50 rounded-lg px-4 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option value="">-- All Statuses --</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>🔷 Processing</option>
                <option value="shipped"    {{ request('status') === 'shipped'    ? 'selected' : '' }}>🚚 Shipped</option>
                <option value="delivered"  {{ request('status') === 'delivered'  ? 'selected' : '' }}>✅ Delivered</option>
                <option value="cancelled"  {{ request('status') === 'cancelled'  ? 'selected' : '' }}>❌ Cancelled</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold transition">
                Filter
            </button>
            @if(request()->filled('search') || request()->filled('status'))
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-pink-400 hover:underline">Clear Filters</a>
            @endif
        </form>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('admin.orders.export-csv') }}"
               class="px-5 py-2.5 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 border border-blue-500/30 rounded-lg text-sm font-bold shadow transition-all whitespace-nowrap">
                ⬇ Export CSV
            </a>
            <label class="px-5 py-2.5 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 rounded-lg text-sm font-bold shadow transition-all cursor-pointer whitespace-nowrap">
                ⬆ Import CSV
                <input type="file" accept=".csv,.txt" class="hidden" id="csvFileInput">
            </label>
        </div>
    </div>

    <!-- Hidden Import Form -->
    <form id="importForm" action="{{ route('admin.orders.import-csv') }}" method="POST" enctype="multipart/form-data" class="hidden">
        @csrf
        <input type="file" name="csv_file" id="csvFileInputHidden" accept=".csv,.txt">
    </form>

    <!-- Table Card -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800/50 rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-800/30 border-b border-slate-800/50 text-sm font-bold uppercase text-slate-300">
                        <th class="px-8 py-4">Order ID</th>
                        <th class="px-8 py-4">Customer</th>
                        <th class="px-8 py-4">Date</th>
                        <th class="px-8 py-4">Delivery Zone</th>
                        <th class="px-8 py-4">Total</th>
                        <th class="px-8 py-4">Status</th>
                        <th class="px-8 py-4">Steadfast</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                @forelse($orders as $order)
                    @php
                        $deliveryCharge = $order->deliveryCharge;
                        $chargeAmount   = $order->actualShippingCharge();
                        $zoneName       = $deliveryCharge ? $deliveryCharge->zone   : 'N/A';
                    @endphp
                    <tbody x-data="{ open: false }" class="divide-y divide-slate-800/30 text-slate-400">
                        <tr class="hover:bg-slate-800/30 transition-all duration-150">
                            <td class="px-8 py-4 font-bold text-white">#{{ $order->id }}</td>
                            <td class="px-8 py-4 cursor-pointer" @click="open = !open">
                                <div class="font-semibold text-slate-200 hover:text-purple-400 transition-colors">
                                    {{ $order->customer_name }}
                                    <svg class="w-3.5 h-3.5 inline-block ml-1 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                                <div class="text-sm text-slate-300 font-mono">{{ $order->customer_phone }}</div>
                            </td>
                            <td class="px-8 py-4 text-sm">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                            <td class="px-8 py-4 text-sm font-semibold text-purple-400">
                                {{ $zoneName }}
                            </td>
                            <td class="px-8 py-4 font-bold text-white">৳{{ number_format($order->total_amount, 0) }}</td>
                            <td class="px-8 py-4">
                                @php
                                    $statusClasses = [
                                        'processing' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'shipped'    => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                        'delivered'  => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                        'cancelled'  => 'bg-pink-500/10 text-pink-400 border-pink-500/20',
                                    ];
                                    $class = $statusClasses[$order->status] ?? 'bg-slate-800/50 text-slate-300 border-slate-700/50';
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-sm font-semibold border {{ $class }} uppercase tracking-wider">
                                    {{ \App\Models\Order::STATUSES[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-8 py-4 text-center">
                                @if($order->is_sent_to_steadfast)
                                    <span class="text-emerald-400 text-sm font-bold">✅ Sent</span>
                                    @if($order->steadfast_consignment_id)
                                        <div class="text-sm text-slate-300 font-mono">{{ $order->steadfast_consignment_id }}</div>
                                    @endif
                                @else
                                    <span class="text-slate-600 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-8 py-4 text-right">
                                <button @click="open = !open"
                                        class="px-3 py-1.5 bg-purple-500/10 hover:bg-purple-500/20 text-purple-400 border border-purple-500/20 rounded-lg text-sm font-bold transition">
                                    <span x-show="!open">Expand</span>
                                    <span x-show="open">Collapse</span>
                                </button>
                            </td>
                        </tr>
                        <tr x-show="open" x-cloak>
                            <td colspan="8" class="p-0 bg-slate-900/70">
                                <div class="p-6 space-y-6">

                                    <!-- Customer & Payment Info -->
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span class="text-xs font-bold uppercase text-slate-400 block">Customer</span>
                                            <span class="font-semibold text-slate-200">{{ $order->customer_name }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold uppercase text-slate-400 block">Phone</span>
                                            <span class="font-semibold text-slate-200 font-mono">{{ $order->customer_phone }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold uppercase text-slate-400 block">Payment</span>
                                            <span class="font-semibold text-slate-200">{{ $order->payment_method }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold uppercase text-slate-400 block">Delivery Zone</span>
                                            <span class="font-semibold text-slate-200">{{ $zoneName }}</span>
                                        </div>
                                        <div class="col-span-2 md:col-span-4">
                                            <span class="text-xs font-bold uppercase text-slate-400 block">Address</span>
                                            <span class="font-semibold text-slate-200">{{ $order->customer_address }}</span>
                                        </div>
                                        @if($order->bkash_trx_id)
                                        <div>
                                            <span class="text-xs font-bold uppercase text-slate-400 block">bKash TrxID</span>
                                            <span class="font-semibold text-slate-200 font-mono">{{ $order->bkash_trx_id }}</span>
                                        </div>
                                        @endif
                                        <div>
                                            <span class="text-xs font-bold uppercase text-slate-400 block">Order Date</span>
                                            <span class="font-semibold text-slate-200">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                                        </div>
                                    </div>

                                    <!-- Actions: Status Update + Send to Steadfast -->
                                    <div class="border-t border-slate-800/50 pt-4">
                                        <div class="flex items-end gap-3">
                                            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="flex-1">
                                                @csrf
                                                <label class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Update Status</label>
                                                <div class="flex gap-2">
                                                    <select name="status"
                                                            class="flex-1 bg-slate-800 border border-slate-700/50 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-purple-500">
                                                        @foreach(\App\Models\Order::STATUSES as $val => $label)
                                                            <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit"
                                                            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 text-white rounded-lg text-sm font-bold uppercase tracking-wider transition-all whitespace-nowrap">
                                                        Update
                                                    </button>
                                                </div>
                                            </form>
                                            @if($order->is_sent_to_steadfast)
                                                <div class="flex-shrink-0">
                                                    <label class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">&nbsp;</label>
                                                    <span class="inline-block px-4 py-2 bg-emerald-600/20 text-emerald-400 border border-emerald-500/30 rounded-lg text-sm font-bold uppercase tracking-wider whitespace-nowrap">
                                                        ✅ Sent
                                                    </span>
                                                </div>
                                            @else
                                            <form action="{{ route('admin.orders.send-to-steadfast', $order->id) }}" method="POST" class="flex-shrink-0">
                                                @csrf
                                                <label class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">&nbsp;</label>
                                                <button type="submit"
                                                        class="px-4 py-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 rounded-lg text-sm font-bold uppercase tracking-wider transition-all whitespace-nowrap">
                                                    Send to Steadfast
                                                </button>
                                            </form>
                                            @endif
                                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="flex-shrink-0" onsubmit="return confirm('Are you sure you want to delete Order #{{ $order->id }}? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <label class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">&nbsp;</label>
                                                <button type="submit"
                                                        class="px-4 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-400 border border-red-500/30 rounded-lg text-sm font-bold uppercase tracking-wider transition-all whitespace-nowrap">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Items Table (at bottom) -->
                                    <div class="border-t border-slate-800/50 pt-4">
                                        <h4 class="text-sm font-bold uppercase tracking-wider text-slate-300 mb-3">Order Items</h4>
                                        <table class="w-full text-left border-collapse text-sm">
                                            <thead>
                                                <tr class="bg-slate-800/30 border-b border-slate-800/50 text-xs font-bold uppercase text-slate-400">
                                                    <th style="width:44px"></th>
                                                    <th class="px-4 py-2">Product</th>
                                                    <th class="px-4 py-2 text-center">Price</th>
                                                    <th class="px-4 py-2 text-center">Qty</th>
                                                    <th class="px-4 py-2 text-right">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-800/30 text-slate-400">
                                                @foreach($order->items as $item)
                                                    @php
                                                        $is_arr = is_array($item);
                                                        $iname  = $is_arr ? ($item['name'] ?? '—') : $item->product_name;
                                                        $idet   = $is_arr ? ($item['variation_details'] ?? '') : ($item->variation_details ?? '');
                                                        $iqty   = $is_arr ? ($item['quantity'] ?? 1) : $item->quantity;
                                                        $iprice = $is_arr ? ($item['price'] ?? 0) : $item->price;
                                                        $iimage = $is_arr ? ($item['image'] ?? null) : null;
                                                        if (!$iimage && !$is_arr && $item->product && $item->product->images->isNotEmpty()) {
                                                            $iimage = $item->product->images->where('is_featured', true)->first()?->image ?? $item->product->images->first()?->image;
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td class="pl-4 py-2" style="width:50px">
                                                            @if($iimage)
                                                                <img src="{{ Storage::url($iimage) }}" alt="{{ $iname }}" width="50" height="50" style="border-radius:4px;object-fit:cover;display:block;">
                                                            @else
                                                                <span style="display:inline-block;width:50px;height:50px;background:#1e293b;border-radius:4px;"></span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2">
                                                            <span class="font-semibold text-slate-200">{{ $iname }}</span>
                                                            @if($idet)
                                                                <span class="text-xs text-purple-400 font-semibold block">Option: {{ $idet }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2 text-center">৳{{ number_format($iprice, 0) }}</td>
                                                        <td class="px-4 py-2 text-center">{{ $iqty }}</td>
                                                        <td class="px-4 py-2 text-right font-bold text-white">৳{{ number_format($iprice * $iqty, 0) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="flex justify-end mt-2 text-sm">
                                            <span class="text-slate-400">Delivery ({{ $zoneName }}):</span>
                                            <span class="font-semibold ml-4 {{ $chargeAmount <= 0 ? 'text-emerald-400' : 'text-slate-200' }}">
                                                {{ $chargeAmount <= 0 ? 'ফ্রি ডেলিভারি 🎉' : '৳' . number_format($chargeAmount, 0) }}
                                            </span>
                                        </div>
                                        <div class="flex justify-end mt-1 text-base font-bold text-white">
                                            <span>Grand Total:</span>
                                            <span class="text-purple-400 ml-4">৳{{ number_format($order->total_amount, 0) }}</span>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="8" class="px-8 py-12 text-center text-slate-300">No orders found.</td>
                        </tr>
                    </tbody>
                @endforelse
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="px-8 py-4 border-t border-slate-800/50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<style>
    [x-cloak] { display: none !important; }
</style>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var csvInput = document.getElementById('csvFileInput');
        if (csvInput) {
            csvInput.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    var hiddenInput = document.getElementById('csvFileInputHidden');
                    var dataTransfer = new DataTransfer();
                    dataTransfer.items.add(this.files[0]);
                    hiddenInput.files = dataTransfer.files;
                    document.getElementById('importForm').submit();
                }
            });
        }
    });
</script>
@endsection
