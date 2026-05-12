<x-layout.app>
    <div class="max-w-7xl mx-auto px-6 py-16">

        <h1 class="text-[40px] font-semibold tracking-[-0.03em] mb-2">Your Bag</h1>
        <p class="text-[#86868B] text-[14px] mb-12">{{ $count }} {{ Str::plural('item', $count) }}</p>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-2xl mb-8 text-[14px]">
                {{ session('success') }}
            </div>
        @endif

        @if ($items->isEmpty())
            <div class="text-center py-32">
                <p class="text-[#86868B] text-[19px] mb-8">Your bag is empty.</p>
                <a href="{{ route('shop') }}"
                   class="bg-[#1D1D1F] text-white px-8 py-3 rounded-full text-[14px] font-medium hover:opacity-90 transition">
                    Continue Shopping
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

                {{-- Cart Items --}}
                <div class="lg:col-span-2 space-y-4">
                    @foreach ($items as $variantId => $item)
                        <div class="bg-white border border-[#E8E8ED] rounded-[24px] p-6 flex gap-6 items-center">

                            {{-- Image --}}
                            <div class="w-24 h-24 bg-[#F5F5F7] rounded-2xl flex items-center justify-center flex-shrink-0">
                                @if (!empty($item['images']))
                                    <img src="{{ Storage::url($item['images'][0]) }}"
                                         class="w-full h-full object-contain mix-blend-multiply rounded-2xl"
                                         alt="{{ $item['product_name'] }}">
                                @else
                                    <div class="w-6 h-6 rounded-full" style="background-color: {{ $item['hex'] }}"></div>
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] font-semibold tracking-widest text-[#86868B] uppercase mb-1">
                                    {{ $item['brand'] ?? 'SOLE DISTRICT' }}
                                </p>
                                <h3 class="text-[16px] font-semibold truncate">{{ $item['product_name'] }}</h3>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="w-3 h-3 rounded-full border border-[#E8E8ED] inline-block flex-shrink-0"
                                          style="background-color: {{ $item['hex'] }}"></span>
                                    <p class="text-[13px] text-[#86868B]">
                                        {{ $item['color'] }} · {{ $item['size'] }}
                                    </p>
                                </div>
                                <p class="text-[14px] font-semibold mt-2">
                                    M {{ number_format($item['unit_price'], 2) }}
                                </p>
                            </div>

                            {{-- Quantity + Remove --}}
                            <div class="flex flex-col items-end gap-3">

                                {{-- Quantity --}}
                                <form action="{{ route('cart.update', $variantId) }}" method="POST"
                                      class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}"
                                            class="w-7 h-7 rounded-full border border-[#E8E8ED] text-[#1D1D1F] text-[14px] hover:border-[#1D1D1F] transition flex items-center justify-center">
                                        −
                                    </button>
                                    <span class="text-[14px] font-medium w-4 text-center">{{ $item['quantity'] }}</span>
                                    <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}"
                                            class="w-7 h-7 rounded-full border border-[#E8E8ED] text-[#1D1D1F] text-[14px] hover:border-[#1D1D1F] transition flex items-center justify-center">
                                        +
                                    </button>
                                </form>

                                {{-- Line total --}}
                                <p class="text-[14px] font-semibold text-[#1D1D1F]">
                                    M {{ number_format($item['unit_price'] * $item['quantity'], 2) }}
                                </p>

                                {{-- Remove --}}
                                <form action="{{ route('cart.remove', $variantId) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-[12px] text-[#86868B] hover:text-red-500 transition">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Order Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white border border-[#E8E8ED] rounded-[24px] p-8 sticky top-28">
                        <h2 class="text-[18px] font-semibold mb-6">Order Summary</h2>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-[14px]">
                                <span class="text-[#86868B]">Subtotal</span>
                                <span>M {{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-[14px]">
                                <span class="text-[#86868B]">Shipping</span>
                                <span class="text-green-600">Free</span>
                            </div>
                            <div class="border-t border-[#E8E8ED] pt-3 flex justify-between text-[16px] font-semibold">
                                <span>Total</span>
                                <span>M {{ number_format($subtotal, 2) }}</span>
                            </div>
                        </div>

                        <a href="{{ route('checkout') }}"
                           class="block w-full bg-[#1D1D1F] text-white text-center py-4 rounded-full text-[15px] font-semibold hover:opacity-90 transition mb-3">
                            Checkout
                        </a>
                        <a href="{{ route('shop') }}"
                           class="block w-full text-center py-4 rounded-full border border-[#E8E8ED] text-[14px] font-medium text-[#1D1D1F] hover:border-[#1D1D1F] transition">
                            Continue Shopping
                        </a>
                    </div>
                </div>

            </div>
        @endif
    </div>
</x-layout.app>