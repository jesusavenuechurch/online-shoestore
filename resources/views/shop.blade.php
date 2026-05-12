<x-layout.app>
    <div class="max-w-7xl mx-auto px-6 py-16">

        {{-- Header --}}
        <div class="mb-10">
            <span class="text-[12px] font-bold tracking-[0.2em] text-[#007AFF] uppercase mb-3 block">
                Collection
            </span>
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <h1 class="text-[48px] font-semibold tracking-[-0.03em] leading-tight">
                    All Kicks
                </h1>
                <p class="text-[#86868B] text-[14px]">
                    {{ $products->total() }} {{ Str::plural('product', $products->total()) }}
                </p>
            </div>
        </div>

        {{-- Category filters --}}
        @if ($categories->count() > 0)
            <div class="flex gap-2 flex-wrap mb-10">
                <a href="{{ route('shop') }}"
                   class="px-5 py-2 rounded-full text-[13px] font-medium transition-all duration-200
                       {{ ! request('category') ? 'bg-[#1D1D1F] text-white' : 'border border-[#E8E8ED] text-[#86868B] hover:border-[#1D1D1F] hover:text-[#1D1D1F]' }}">
                    All
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('shop', ['category' => $category->slug]) }}"
                       class="px-5 py-2 rounded-full text-[13px] font-medium transition-all duration-200
                           {{ request('category') === $category->slug ? 'bg-[#1D1D1F] text-white' : 'border border-[#E8E8ED] text-[#86868B] hover:border-[#1D1D1F] hover:text-[#1D1D1F]' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Product grid --}}
        @if ($products->isEmpty())
            <div class="text-center py-32">
                <p class="text-[#86868B] text-[19px] mb-8">No products found.</p>
                <a href="{{ route('shop') }}"
                   class="bg-[#1D1D1F] text-white px-8 py-3 rounded-full text-[14px] font-medium hover:opacity-90 transition">
                    View All
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($products as $product)
                    @php
                        $images = is_array($product->images) ? $product->images : [];
                        $hero = $images[0] ?? null;
                        $colors = $product->variants->pluck('color')->unique('id');
                        $totalStock = $product->variants->sum('stock_quantity');
                        $minPrice = $product->variants->min('price_override') ?? $product->base_price;
                        $maxPrice = $product->variants->max('price_override') ?? $product->base_price;
                    @endphp

                    <a href="{{ route('products.show', $product->slug) }}"
                       class="bg-white border border-[#E8E8ED] rounded-[28px] overflow-hidden group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">

                        {{-- Image --}}
                        <div class="relative bg-[#F5F5F7] aspect-square overflow-hidden">
                            @if ($hero)
                                <img src="{{ Storage::url($hero) }}"
                                     class="w-full h-full object-contain mix-blend-multiply p-8 transition-transform duration-500 group-hover:scale-105"
                                     alt="{{ $product->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="text-[#86868B] text-[12px] tracking-wide">No image</span>
                                </div>
                            @endif

                            {{-- Stock badge --}}
                            @if ($totalStock === 0)
                                <div class="absolute top-4 left-4 bg-red-500 text-white text-[11px] font-semibold px-3 py-1 rounded-full">
                                    Sold Out
                                </div>
                            @elseif ($totalStock <= 5)
                                <div class="absolute top-4 left-4 bg-amber-400 text-white text-[11px] font-semibold px-3 py-1 rounded-full">
                                    Low Stock
                                </div>
                            @else
                                <div class="absolute top-4 left-4 bg-white border border-[#E8E8ED] text-[#1D1D1F] text-[11px] font-semibold px-3 py-1 rounded-full">
                                    In Stock
                                </div>
                            @endif

                            {{-- Quick view hint on hover --}}
                            <div class="absolute inset-0 bg-[#1D1D1F]/0 group-hover:bg-[#1D1D1F]/5 transition-all duration-300 flex items-end justify-center pb-6 opacity-0 group-hover:opacity-100">
                                <span class="bg-white text-[#1D1D1F] text-[12px] font-semibold px-5 py-2 rounded-full shadow-md">
                                    View Product
                                </span>
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="p-6 flex flex-col flex-1">
                            <p class="text-[11px] font-bold tracking-[0.2em] text-[#86868B] uppercase mb-1">
                                {{ $product->brand ?? $product->category?->name ?? 'SOLE DISTRICT' }}
                            </p>
                            <h3 class="text-[16px] font-semibold mb-3 flex-1">{{ $product->name }}</h3>

                            {{-- Colour swatches --}}
                            @if ($colors->count() > 0)
                                <div class="flex gap-2 mb-4">
                                    @foreach ($colors->take(6) as $color)
                                        <span
                                            class="w-4 h-4 rounded-full border border-[#E8E8ED] flex-shrink-0"
                                            style="background-color: {{ $color->hex_code }}"
                                            title="{{ $color->name }}">
                                        </span>
                                    @endforeach
                                    @if ($colors->count() > 6)
                                        <span class="text-[11px] text-[#86868B] self-center">
                                            +{{ $colors->count() - 6 }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            {{-- Price --}}
                            <div class="flex items-center justify-between">
                                <p class="text-[16px] font-semibold">
                                    @if ($minPrice != $maxPrice)
                                        M {{ number_format($minPrice, 2) }} — M {{ number_format($maxPrice, 2) }}
                                    @else
                                        M {{ number_format($product->base_price, 2) }}
                                    @endif
                                </p>
                                <div class="w-8 h-8 bg-[#1D1D1F] rounded-full flex items-center justify-center group-hover:bg-[#007AFF] transition-colors duration-200">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($products->hasPages())
                <div class="mt-16 flex justify-center">
                    <div class="flex items-center gap-2">

                        {{-- Previous --}}
                        @if ($products->onFirstPage())
                            <span class="w-10 h-10 rounded-full border border-[#E8E8ED] flex items-center justify-center text-[#C7C7CC] cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}"
                               class="w-10 h-10 rounded-full border border-[#E8E8ED] flex items-center justify-center hover:border-[#1D1D1F] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </a>
                        @endif

                        {{-- Page numbers --}}
                        @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                            @if ($page == $products->currentPage())
                                <span class="w-10 h-10 rounded-full bg-[#1D1D1F] text-white flex items-center justify-center text-[14px] font-medium">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                   class="w-10 h-10 rounded-full border border-[#E8E8ED] text-[#86868B] flex items-center justify-center text-[14px] hover:border-[#1D1D1F] hover:text-[#1D1D1F] transition">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Next --}}
                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}"
                               class="w-10 h-10 rounded-full border border-[#E8E8ED] flex items-center justify-center hover:border-[#1D1D1F] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <span class="w-10 h-10 rounded-full border border-[#E8E8ED] flex items-center justify-center text-[#C7C7CC] cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @endif

                    </div>
                </div>
            @endif

        @endif
    </div>
</x-layout.app>