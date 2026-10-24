@extends('layouts.store')

@section('title', 'Shop Catalog - ' . site_name())

@section('pixel_events')
@if($pixelSearchEventId && $pixelSearchEventData)
<script>
fbq('track', 'Search', @json($pixelSearchEventData), { eventID: @json($pixelSearchEventId) });
</script>
@endif
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 md:py-12">
    
    <div x-data="{ filterOpen: false }" class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar Filter Mappings -->
        <aside class="space-y-6" :class="filterOpen ? 'block' : 'hidden lg:block'" x-cloak>
            
            <!-- Filter Form Wrapper -->
            <form action="{{ route('shop') }}" method="GET" class="space-y-6">
                <!-- Search term (hidden unless changed inside sidebar) -->
                @if(request()->filled('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request()->filled('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <!-- Categories Tree -->
                <div class="bg-white border border-gray-150 p-6 rounded-2xl shadow-sm">
                    <h3 class="font-bold text-gray-900 text-sm mb-4 uppercase tracking-wider">Categories</h3>
                    <div class="space-y-3">
                        <a href="{{ route('shop', request()->except(['category', 'page'])) }}" 
                           class="block text-xs font-bold uppercase tracking-wider text-gray-500 hover:text-purple-600 {{ !request()->filled('category') ? 'text-purple-600 font-extrabold' : '' }}">
                            All Categories
                        </a>
                        
                        <!-- Recursive category list -->
                        <div class="space-y-2 mt-2">
                            @foreach($categories as $cat)
                                <div class="space-y-1">
                                    <a href="{{ route('shop', array_merge(request()->query(), ['category' => $cat->slug, 'page' => 1])) }}"
                                       class="text-sm font-semibold hover:text-purple-600 block transition {{ request('category') === $cat->slug ? 'text-purple-600 font-bold' : 'text-gray-700' }}">
                                        {{ $cat->name }}
                                    </a>
                                    
                                    @if($cat->children->count() > 0)
                                        <div class="pl-4 space-y-1 border-l border-purple-100/60 ml-1">
                                            @foreach($cat->children as $child)
                                                <a href="{{ route('shop', array_merge(request()->query(), ['category' => $child->slug, 'page' => 1])) }}"
                                                   class="text-xs hover:text-purple-600 block transition {{ request('category') === $child->slug ? 'text-purple-600 font-bold' : 'text-gray-500' }}">
                                                    {{ $child->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="bg-white border border-gray-150 p-6 rounded-2xl shadow-sm space-y-4">
                    <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wider">Price Range</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Min (৳)</label>
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0" min="0"
                                   class="w-full bg-gray-50 border border-gray-200 rounded px-2.5 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Max (৳)</label>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="1000" min="0"
                                   class="w-full bg-gray-50 border border-gray-200 rounded px-2.5 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-purple-500">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-2 bg-purple-600 hover:bg-primary text-white rounded text-xs font-bold transition">
                        Apply Price
                    </button>
                </div>
            </form>
        </aside>

        <!-- Main Product Grid -->
        <main class="lg:col-span-3 space-y-2 md:space-y-4 lg:space-y-6">
            
            <!-- Filters & Sorting bar -->
            <div class="bg-white border border-gray-150 p-4 rounded-2xl shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="hidden sm:block">
                    @if(request()->filled('search'))
                        <span class="text-sm text-gray-500">Search results for "<strong class="text-gray-900">{{ request('search') }}</strong>"</span>
                    @elseif(request()->filled('category'))
                        @php $currentCat = \App\Models\Category::where('slug', request('category'))->first(); @endphp
                        <span class="text-sm text-gray-500">Browsing <strong class="text-gray-900">{{ $currentCat ? $currentCat->name : request('category') }}</strong></span>
                    @else
                        <span class="text-sm text-gray-500">Showing all catalog products</span>
                    @endif
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <!-- Mobile Filter Toggle -->
                    <button @click="filterOpen = !filterOpen"
                            class="lg:hidden flex-1 sm:flex-none flex items-center justify-center gap-1.5 py-2 px-3 bg-purple-50 border border-purple-200 rounded-lg text-xs font-bold text-primary hover:bg-purple-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span x-show="!filterOpen">Filters</span>
                        <span x-show="filterOpen" x-cloak>Hide</span>
                    </button>

                    <!-- Sorting Dropdown -->
                    <div class="flex-1 sm:flex-none flex items-center gap-1.5">
                        <label for="sort-selector" class="text-xs text-gray-400 uppercase font-bold whitespace-nowrap">Sort:</label>
                        <select id="sort-selector" onchange="location = this.value;"
                                class="flex-1 sm:flex-none bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-purple-500">
                            <option value="{{ route('shop', array_merge(request()->query(), ['sort' => 'latest', 'page' => 1])) }}" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="{{ route('shop', array_merge(request()->query(), ['sort' => 'price_asc', 'page' => 1])) }}" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Low to High</option>
                            <option value="{{ route('shop', array_merge(request()->query(), ['sort' => 'price_desc', 'page' => 1])) }}" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>High to Low</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                @forelse($products as $product)
                    @include('store.partials.product-card', ['product' => $product])
                @empty
                    <div class="col-span-full py-8 md:py-16 text-center text-gray-400 bg-white border border-gray-150 rounded-2xl">
                        <svg class="w-16 h-16 text-purple-200 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h4 class="font-bold text-gray-650 mt-4">No Products Found</h4>
                        <p class="text-xs text-gray-400 mt-1">Try modifying your filters or search terms.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="pt-6">
                    {{ $products->links() }}
                </div>
            @endif

        </main>
    </div>
</div>
@endsection
