<x-layout.app>
    @php
        $firstVariant = $product->variants->where('is_active', true)->first();
        $defaultColorId = $firstVariant?->color_id;
        $defaultSizeId = $firstVariant?->size_id;
    @endphp

    <div
        x-data="{
            variants: {{ $variantsJson }},
            selectedColorId: {{ $defaultColorId ?? 'null' }},
            selectedSizeId: {{ $defaultSizeId ?? 'null' }},

            get selectedVariant() {
                if (!this.selectedColorId || !this.selectedSizeId) return null;
                return this.variants.find(v =>
                    v.color_id === this.selectedColorId &&
                    v.size_id === this.selectedSizeId
                ) ?? null;
            },

            get availableColors() {
                const seen = new Set();
                return this.variants.filter(v => {
                    if (seen.has(v.color_id)) return false;
                    seen.add(v.color_id); return true;
                });
            },

            get sizesForColor() {
                if (!this.selectedColorId) return [];
                return this.variants.filter(v => v.color_id === this.selectedColorId);
            },

            get effectivePrice() {
                return this.selectedVariant
                    ? this.selectedVariant.effective_price
                    : {{ (float) $product->base_price }};
            },

            get inStock() {
                return this.selectedVariant && this.selectedVariant.stock > 0;
            },

            get stockLabel() {
                if (!this.selectedVariant) return '';
                if (this.selectedVariant.stock === 0) return 'Out of stock';
                if (this.selectedVariant.stock <= 3) return 'Only ' + this.selectedVariant.stock + ' left';
                return 'In stock';
            },

            isSizeAvailableForColor(sizeId) {
                if (!this.selectedColorId) return false;
                const v = this.variants.find(v => v.color_id === this.selectedColorId && v.size_id === sizeId);
                return v && v.stock > 0;
            },

            selectColor(colorId) {
                this.selectedColorId = colorId;
                // If currently selected size is not available in new color, reset it
                const available = this.isSizeAvailableForColor(this.selectedSizeId);
                if (!available) this.selectedSizeId = null;
            },
        }"
        class="max-w-7xl mx-auto px-6 py-16"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-start">

            {{-- Left: Image --}}
            <div class="bg-white rounded-[28px] border border-[#E8E8ED] p-12 flex items-center justify-center min-h-[480px]">
                @if ($product->images && count($product->images) > 0)
                    <img src="{{ Storage::url($product->images[0]) }}"
                         class="w-full max-h-[420px] object-contain mix-blend-multiply transition-transform duration-500 hover:scale-105"
                         alt="{{ $product->name }}">
                @else
                    <div class="text-[#86868B] text-[14px]">No image uploaded</div>
                @endif
            </div>

            {{-- Right: Details --}}
            <div class="py-4">

                {{-- Breadcrumb --}}
                <p class="text-[12px] text-[#86868B] mb-6">
                    <a href="{{ route('home') }}" class="hover:text-black transition-colors">Home</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('shop') }}" class="hover:text-black transition-colors">Shop</a>
                    <span class="mx-2">/</span>
                    {{ $product->name }}
                </p>

                {{-- Brand + Name --}}
                <p class="text-[12px] font-bold tracking-[0.2em] text-[#007AFF] uppercase mb-2">
                    {{ $product->brand ?? $product->category?->name }}
                </p>
                <h1 class="text-[48px] font-semibold leading-[1.05] tracking-[-0.03em] mb-4">
                    {{ $product->name }}
                </h1>

                {{-- Dynamic Price --}}
                <p class="text-[28px] font-semibold mb-8">
                    M <span x-text="effectivePrice.toLocaleString('en-LS', {minimumFractionDigits: 2})"></span>
                </p>

                {{-- Colour Picker --}}
                <div class="mb-8">
                    <p class="text-[12px] font-semibold tracking-[0.15em] text-[#86868B] uppercase mb-3">
                        Colour —
                        <span class="text-[#1D1D1F]" x-text="availableColors.find(v => v.color_id === selectedColorId)?.color_name ?? ''"></span>
                    </p>
                    <div class="flex gap-3">
                        <template x-for="cv in availableColors" :key="cv.color_id">
                            <button
                                @click="selectColor(cv.color_id)"
                                :title="cv.color_name"
                                :style="'background-color:' + cv.hex"
                                :class="selectedColorId === cv.color_id
                                    ? 'ring-2 ring-offset-2 ring-[#007AFF]'
                                    : 'ring-1 ring-[#E8E8ED]'"
                                class="w-8 h-8 rounded-full transition-all duration-200">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Size Picker --}}
                <div class="mb-8">
                    <p class="text-[12px] font-semibold tracking-[0.15em] text-[#86868B] uppercase mb-3">Size</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="sv in sizesForColor" :key="sv.size_id">
                            <button
                                @click="sv.stock > 0 && (selectedSizeId = sv.size_id)"
                                :disabled="sv.stock === 0"
                                :class="{
                                    'bg-[#1D1D1F] text-white border-[#1D1D1F]': selectedSizeId === sv.size_id,
                                    'text-[#1D1D1F] border-[#E8E8ED] hover:border-[#1D1D1F]': selectedSizeId !== sv.size_id && sv.stock > 0,
                                    'text-[#C7C7CC] border-[#E8E8ED] cursor-not-allowed line-through': sv.stock === 0,
                                }"
                                class="border rounded-full px-5 py-2 text-[13px] font-medium transition-all duration-200"
                                x-text="sv.size_label">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Stock Label --}}
                <p class="text-[13px] mb-6"
                   :class="{
                       'text-red-500': selectedVariant && selectedVariant.stock === 0,
                       'text-amber-500': selectedVariant && selectedVariant.stock > 0 && selectedVariant.stock <= 3,
                       'text-green-600': selectedVariant && selectedVariant.stock > 3,
                       'text-[#86868B]': !selectedVariant,
                   }"
                   x-text="stockLabel || 'Select a size'">
                </p>

                {{-- Add to Bag --}}
                <button
                    :disabled="!inStock"
                    :class="inStock
                        ? 'bg-[#1D1D1F] text-white hover:opacity-90'
                        : 'bg-[#E8E8ED] text-[#86868B] cursor-not-allowed'"
                    class="w-full py-4 rounded-full text-[15px] font-semibold transition-all duration-200 mb-3">
                    <span x-text="!selectedSizeId ? 'Select a size' : (!inStock ? 'Out of Stock' : 'Add to Bag')"></span>
                </button>

                <button class="w-full py-4 rounded-full border border-[#E8E8ED] text-[15px] font-medium text-[#1D1D1F] hover:border-[#1D1D1F] transition-all duration-200">
                    Save to Wishlist
                </button>

                {{-- Description --}}
                @if ($product->description)
                    <div class="mt-10 pt-8 border-t border-[#E8E8ED]">
                        <h3 class="text-[13px] font-semibold tracking-[0.15em] text-[#86868B] uppercase mb-4">About</h3>
                        <div class="text-[15px] text-[#1D1D1F] leading-relaxed prose prose-sm">
                            {!! $product->description !!}
                        </div>
                    </div>
                @endif

                {{-- Meta --}}
                <div class="mt-8 pt-6 border-t border-[#E8E8ED] space-y-3">
                    @if ($product->category)
                        <div class="flex justify-between text-[13px]">
                            <span class="text-[#86868B]">Category</span>
                            <span>{{ $product->category->name }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-[13px]" x-show="selectedVariant">
                        <span class="text-[#86868B]">SKU</span>
                        <span x-text="selectedVariant?.sku ?? ''"></span>
                    </div>
                    <div class="flex justify-between text-[13px]">
                        <span class="text-[#86868B]">Delivery</span>
                        <span>1–3 business days</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

</x-layout.app>