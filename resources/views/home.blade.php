<x-layout.app>

    {{-- Hero --}}
    <section class="max-w-7xl mx-auto px-6 py-24 text-center">
        <span class="text-[12px] font-bold tracking-[0.2em] text-[#007AFF] uppercase mb-4 block">
            Engineered for Performance
        </span>
        <h1 class="text-[64px] md:text-[96px] font-semibold leading-[1.05] tracking-[-0.04em] mb-8">
            SOLE DISTRICT <br>
            <span class="text-[#86868B]">Step Different.</span>
        </h1>
        <p class="text-[19px] text-[#86868B] max-w-lg mx-auto leading-relaxed mb-10">
            Authentic Shoes. Every size. Every colour. Fast delivery.
        </p>
        <div class="flex justify-center gap-4">
            <a href="{{ route('shop') }}" class="bg-[#1D1D1F] text-white px-8 py-3 rounded-full text-[14px] font-medium hover:opacity-90 transition">
                Shop Now
            </a>
            <a href="{{ route('shop') }}" class="text-[#0066CC] hover:underline text-[14px] font-medium self-center">
                View all >
            </a>
        </div>
    </section>

    {{-- Featured Products --}}
    <section class="px-6 pb-24">
        <div class="max-w-7xl mx-auto">

            <h2 class="text-[12px] font-bold tracking-[0.2em] text-[#86868B] uppercase mb-8">
                Latest Drops
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($featured as $product)
                    @php
                        $colors = $product->variants->pluck('color')->unique('id');
                        $totalStock = $product->variants->sum('stock_quantity');
                    @endphp

                    <a href="{{ route('products.show', $product->slug) }}"
                       class="bg-white rounded-[28px] border border-[#E8E8ED] p-8 flex flex-col items-center text-center group hover:shadow-lg transition-shadow duration-300 overflow-hidden">

                        {{-- Product image --}}
                        <div class="w-full mb-6 transition-transform duration-500 group-hover:scale-105">
                            @php
                                $images = is_array($product->images) ? $product->images : [];
                                $hero = $images[0] ?? null;
                            @endphp

                            @if ($hero)
                                <img src="{{ Storage::url($hero) }}"
                                    class="w-full h-full object-contain mix-blend-multiply"
                                    alt="{{ $product->name }}">
                            @else
                                <div class="w-full h-full bg-[#F5F5F7] rounded-2xl flex items-center justify-center">
                                    <span class="text-[#86868B] text-[12px]">No image</span>
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="w-full">
                            <p class="text-[11px] font-semibold tracking-widest text-[#86868B] uppercase mb-1">
                                {{ $product->brand ?? $product->category?->name }}
                            </p>
                            <h3 class="text-[17px] font-semibold mb-1">{{ $product->name }}</h3>

                            {{-- Colour swatches --}}
                            @if ($colors->count() > 0)
                                <div class="flex justify-center gap-2 my-3">
                                    @foreach ($colors->take(5) as $color)
                                        <span
                                            class="w-4 h-4 rounded-full border border-[#E8E8ED] inline-block"
                                            style="background-color: {{ $color->hex_code }}"
                                            title="{{ $color->name }}">
                                        </span>
                                    @endforeach
                                    @if ($colors->count() > 5)
                                        <span class="text-[11px] text-[#86868B] self-center">+{{ $colors->count() - 5 }}</span>
                                    @endif
                                </div>
                            @endif

                            <p class="text-[17px] font-medium mt-2">M {{ number_format($product->base_price, 2) }}</p>

                            {{-- Stock indicator --}}
                            @if ($totalStock === 0)
                                <p class="text-[11px] text-red-500 mt-1">Out of stock</p>
                            @elseif ($totalStock <= 5)
                                <p class="text-[11px] text-amber-500 mt-1">Only {{ $totalStock }} left</p>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="col-span-3 text-center py-24 text-[#86868B]">
                        No products yet — add some in the admin.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

</x-layout.app>