<x-layout.app>
    <div class="max-w-2xl mx-auto px-6 py-24 text-center">

        {{-- Success icon --}}
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-8">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <span class="text-[12px] font-bold tracking-[0.2em] text-[#007AFF] uppercase mb-4 block">
            Order Confirmed
        </span>
        <h1 class="text-[48px] font-semibold tracking-[-0.03em] leading-tight mb-4">
            Thank you,<br>{{ $order->customer->first_name }}.
        </h1>
        <p class="text-[17px] text-[#86868B] leading-relaxed mb-12">
            Your order has been placed successfully. Our team will reach out to confirm your
            <strong class="text-[#1D1D1F]">{{ $order->paymentMethod->name }}</strong> payment
            and arrange delivery to <strong class="text-[#1D1D1F]">{{ $order->shipping_address['city'] }}</strong>.
        </p>

        {{-- Order details card --}}
        <div class="bg-white border border-[#E8E8ED] rounded-[28px] p-8 text-left mb-8">

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-[16px] font-semibold">Order #{{ $order->id }}</h2>
                <span class="text-[12px] font-semibold px-3 py-1 rounded-full"
                      style="background-color: {{ $order->status->color }}22; color: {{ $order->status->color }}">
                    {{ $order->status->name }}
                </span>
            </div>

            {{-- Items --}}
            <div class="space-y-4 mb-6">
                @foreach ($order->items as $item)
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#F5F5F7] flex items-center justify-center flex-shrink-0">
                            @php $images = $item->variant->product->images ?? [] @endphp
                            @if (!empty($images))
                                <img src="{{ Storage::url($images[0]) }}"
                                     class="w-full h-full object-contain mix-blend-multiply rounded-xl"
                                     alt="{{ $item->variant->product->name }}">
                            @else
                                <div class="w-4 h-4 rounded-full"
                                     style="background-color: {{ $item->variant->color->hex_code }}"></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-[14px] font-semibold">{{ $item->variant->product->name }}</p>
                            <p class="text-[12px] text-[#86868B]">
                                {{ $item->variant->size->label }} ({{ $item->variant->size->system }})
                                · {{ $item->variant->color->name }}
                                × {{ $item->quantity }}
                            </p>
                        </div>
                        <p class="text-[14px] font-semibold">
                            M {{ number_format($item->line_total, 2) }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- Totals --}}
            <div class="border-t border-[#E8E8ED] pt-4 space-y-2">
                <div class="flex justify-between text-[13px]">
                    <span class="text-[#86868B]">Subtotal</span>
                    <span>M {{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-[13px]">
                    <span class="text-[#86868B]">Shipping</span>
                    <span class="text-green-600">Free</span>
                </div>
                <div class="flex justify-between text-[15px] font-semibold pt-2 border-t border-[#E8E8ED]">
                    <span>Total</span>
                    <span>M {{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Payment instructions --}}
        <div class="bg-[#F5F5F7] rounded-[24px] p-6 text-left mb-10">
            <h3 class="text-[14px] font-semibold mb-2">Payment Instructions</h3>
            @if($order->paymentMethod->code === 'cash')
                <p class="text-[13px] text-[#86868B] leading-relaxed">
                    You selected <strong class="text-[#1D1D1F]">Cash on Delivery</strong>.
                    Please have <strong class="text-[#1D1D1F]">M {{ number_format($order->total, 2) }}</strong> ready when your order arrives.
                </p>
            @elseif($order->paymentMethod->code === 'mpesa')
                <p class="text-[13px] text-[#86868B] leading-relaxed">
                    Send <strong class="text-[#1D1D1F]">M {{ number_format($order->total, 2) }}</strong> via M-Pesa.
                    Our team will send you the payment number via the phone number you provided.
                </p>
            @elseif($order->paymentMethod->code === 'ecocash')
                <p class="text-[13px] text-[#86868B] leading-relaxed">
                    Send <strong class="text-[#1D1D1F]">M {{ number_format($order->total, 2) }}</strong> via EcoCash.
                    Our team will send you the payment number via the phone number you provided.
                </p>
            @else
                <p class="text-[13px] text-[#86868B] leading-relaxed">
                    Transfer <strong class="text-[#1D1D1F]">M {{ number_format($order->total, 2) }}</strong> to our bank account.
                    Our team will send you the banking details via email.
                </p>
            @endif
        </div>

        <div class="flex justify-center gap-4">
            <a href="{{ route('shop') }}"
               class="bg-[#1D1D1F] text-white px-8 py-3 rounded-full text-[14px] font-medium hover:opacity-90 transition">
                Continue Shopping
            </a>
            <a href="{{ route('home') }}"
               class="border border-[#E8E8ED] text-[#1D1D1F] px-8 py-3 rounded-full text-[14px] font-medium hover:border-[#1D1D1F] transition">
                Back to Home
            </a>
        </div>

    </div>
</x-layout.app>