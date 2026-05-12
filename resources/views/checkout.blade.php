<x-layout.app>
    <div class="max-w-7xl mx-auto px-6 py-16">

        <h1 class="text-[40px] font-semibold tracking-[-0.03em] mb-2">Checkout</h1>
        <p class="text-[#86868B] text-[14px] mb-12">Fill in your details and we'll get your order to you.</p>

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-2xl mb-8 text-[14px]">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

                {{-- Left: Form --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- Contact Details --}}
                    <div class="bg-white border border-[#E8E8ED] rounded-[24px] p-8">
                        <h2 class="text-[17px] font-semibold mb-6">Contact Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div>
                                <label class="block text-[12px] font-semibold tracking-wider text-[#86868B] uppercase mb-2">
                                    First Name
                                </label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}"
                                       class="w-full border border-[#E8E8ED] rounded-2xl px-4 py-3 text-[14px] focus:outline-none focus:border-[#1D1D1F] transition @error('first_name') border-red-400 @enderror"
                                       placeholder="Thabo">
                                @error('first_name')
                                    <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-[12px] font-semibold tracking-wider text-[#86868B] uppercase mb-2">
                                    Last Name
                                </label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}"
                                       class="w-full border border-[#E8E8ED] rounded-2xl px-4 py-3 text-[14px] focus:outline-none focus:border-[#1D1D1F] transition @error('last_name') border-red-400 @enderror"
                                       placeholder="Mokoena">
                                @error('last_name')
                                    <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-[12px] font-semibold tracking-wider text-[#86868B] uppercase mb-2">
                                    Email
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       class="w-full border border-[#E8E8ED] rounded-2xl px-4 py-3 text-[14px] focus:outline-none focus:border-[#1D1D1F] transition @error('email') border-red-400 @enderror"
                                       placeholder="thabo@example.com">
                                @error('email')
                                    <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-[12px] font-semibold tracking-wider text-[#86868B] uppercase mb-2">
                                    Phone
                                </label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                       class="w-full border border-[#E8E8ED] rounded-2xl px-4 py-3 text-[14px] focus:outline-none focus:border-[#1D1D1F] transition"
                                       placeholder="+266 50 123 456">
                            </div>
                        </div>
                    </div>

                    {{-- Delivery Address --}}
                    <div class="bg-white border border-[#E8E8ED] rounded-[24px] p-8">
                        <h2 class="text-[17px] font-semibold mb-6">Delivery Address</h2>
                        <div class="space-y-4">

                            <div>
                                <label class="block text-[12px] font-semibold tracking-wider text-[#86868B] uppercase mb-2">
                                    Street Address
                                </label>
                                <input type="text" name="address_line1" value="{{ old('address_line1') }}"
                                       class="w-full border border-[#E8E8ED] rounded-2xl px-4 py-3 text-[14px] focus:outline-none focus:border-[#1D1D1F] transition @error('address_line1') border-red-400 @enderror"
                                       placeholder="12 Kingsway">
                                @error('address_line1')
                                    <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-[12px] font-semibold tracking-wider text-[#86868B] uppercase mb-2">
                                    City / Town
                                </label>
                                <input type="text" name="city" value="{{ old('city') }}"
                                       class="w-full border border-[#E8E8ED] rounded-2xl px-4 py-3 text-[14px] focus:outline-none focus:border-[#1D1D1F] transition @error('city') border-red-400 @enderror"
                                       placeholder="Maseru">
                                @error('city')
                                    <p class="text-red-500 text-[12px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-[12px] font-semibold tracking-wider text-[#86868B] uppercase mb-2">
                                    Country
                                </label>
                                <input type="text" value="Lesotho" disabled
                                       class="w-full border border-[#E8E8ED] rounded-2xl px-4 py-3 text-[14px] bg-[#F5F5F7] text-[#86868B]">
                            </div>

                            <div>
                                <label class="block text-[12px] font-semibold tracking-wider text-[#86868B] uppercase mb-2">
                                    Order Notes <span class="normal-case font-normal">(optional)</span>
                                </label>
                                <textarea name="notes" rows="3"
                                          class="w-full border border-[#E8E8ED] rounded-2xl px-4 py-3 text-[14px] focus:outline-none focus:border-[#1D1D1F] transition resize-none"
                                          placeholder="Any specific delivery instructions...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="bg-white border border-[#E8E8ED] rounded-[24px] p-8">
                        <h2 class="text-[17px] font-semibold mb-2">Payment Method</h2>
                        <p class="text-[13px] text-[#86868B] mb-6">
                            All payments are processed manually. Select your preferred method and our team will contact you to confirm.
                        </p>

                        @error('payment_method_id')
                            <p class="text-red-500 text-[12px] mb-4">{{ $message }}</p>
                        @enderror

                        {{-- Payment Method --}}
                        <div class="bg-white border border-[#E8E8ED] rounded-[24px] p-8"
                            x-data="{ selected: {{ old('payment_method_id', 'null') }}, cashId: {{ $paymentMethods->where('code', 'cash')->first()?->id ?? 'null' }} }">

                            <h2 class="text-[17px] font-semibold mb-2">Payment Method</h2>
                            <p class="text-[13px] text-[#86868B] mb-6">
                                All payments are processed manually. Select your preferred method and our team will contact you to confirm.
                            </p>

                            @error('payment_method_id')
                                <p class="text-red-500 text-[12px] mb-4">{{ $message }}</p>
                            @enderror

                            <div class="grid grid-cols-2 gap-3">
                                @foreach ($paymentMethods as $method)
                                    <label
                                        @click="selected = {{ $method->id }}"
                                        :class="selected === {{ $method->id }}
                                            ? 'border-[#1D1D1F] bg-[#F5F5F7] shadow-sm'
                                            : 'border-[#E8E8ED] hover:border-[#86868B]'"
                                        class="relative flex items-center gap-3 border-2 rounded-2xl p-4 cursor-pointer transition-all duration-200">

                                        <input type="radio"
                                            name="payment_method_id"
                                            value="{{ $method->id }}"
                                            x-model="selected"
                                            class="sr-only">

                                        {{-- Icon --}}
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                                            {{ $method->code === 'cash' ? 'bg-green-100' : '' }}
                                            {{ $method->code === 'mpesa' ? 'bg-green-100' : '' }}
                                            {{ $method->code === 'ecocash' ? 'bg-red-100' : '' }}
                                            {{ $method->code === 'bank' ? 'bg-blue-100' : '' }}">
                                            @if($method->code === 'cash')
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            @elseif($method->code === 'mpesa')
                                                <span class="text-green-700 font-black text-[11px]">M-P</span>
                                            @elseif($method->code === 'ecocash')
                                                <span class="text-red-600 font-black text-[11px]">ECO</span>
                                            @else
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                </svg>
                                            @endif
                                        </div>

                                        {{-- Label --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[14px] font-semibold">{{ $method->name }}</p>
                                            <p class="text-[12px] text-[#86868B]">
                                                @if($method->code === 'cash') Pay on delivery
                                                @elseif($method->code === 'mpesa') Send to our M-Pesa number
                                                @elseif($method->code === 'ecocash') Send to our EcoCash number
                                                @else Direct bank transfer
                                                @endif
                                            </p>
                                        </div>

                                        {{-- Check circle --}}
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all duration-200"
                                            :class="selected === {{ $method->id }}
                                                ? 'border-[#1D1D1F] bg-[#1D1D1F]'
                                                : 'border-[#E8E8ED]'">
                                            <svg x-show="selected === {{ $method->id }}"
                                                x-transition
                                                class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            {{-- Payment reference — only show for non-cash --}}
                            <div x-show="selected && selected !== cashId"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="mt-4">
                                <label class="block text-[12px] font-semibold tracking-wider text-[#86868B] uppercase mb-2">
                                    Transaction Reference <span class="normal-case font-normal">(optional)</span>
                                </label>
                                <input type="text" name="payment_reference" value="{{ old('payment_reference') }}"
                                    class="w-full border border-[#E8E8ED] rounded-2xl px-4 py-3 text-[14px] focus:outline-none focus:border-[#1D1D1F] transition"
                                    placeholder="e.g. MPesa confirmation code">
                            </div>

                        </div>

                </div>

                {{-- Right: Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white border border-[#E8E8ED] rounded-[24px] p-8 sticky top-28">
                        <h2 class="text-[17px] font-semibold mb-6">Order Summary</h2>

                        <div class="space-y-3 mb-6">
                            @foreach ($items as $item)
                                <div class="flex justify-between text-[13px]">
                                    <span class="text-[#86868B] truncate pr-4">
                                        {{ $item['product_name'] }}
                                        <span class="block text-[11px]">{{ $item['size'] }} · {{ $item['color'] }} × {{ $item['quantity'] }}</span>
                                    </span>
                                    <span class="font-medium flex-shrink-0">
                                        M {{ number_format($item['unit_price'] * $item['quantity'], 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-[#E8E8ED] pt-4 space-y-2 mb-6">
                            <div class="flex justify-between text-[14px]">
                                <span class="text-[#86868B]">Subtotal</span>
                                <span>M {{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-[14px]">
                                <span class="text-[#86868B]">Shipping</span>
                                <span class="text-green-600">Free</span>
                            </div>
                            <div class="flex justify-between text-[16px] font-semibold pt-2 border-t border-[#E8E8ED]">
                                <span>Total</span>
                                <span>M {{ number_format($subtotal, 2) }}</span>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full bg-[#1D1D1F] text-white py-4 rounded-full text-[15px] font-semibold hover:opacity-90 transition">
                            Place Order
                        </button>

                        <p class="text-[11px] text-[#86868B] text-center mt-4 leading-relaxed">
                            By placing your order you agree to our terms. Our team will contact you to confirm payment.
                        </p>
                    </div>
                </div>

            </div>
        </form>
    </div>
</x-layout.app>