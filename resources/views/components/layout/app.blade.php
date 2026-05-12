<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sole District' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Instrument Sans', sans-serif; -webkit-font-smoothing: antialiased; }
        .glass { background: rgba(251, 251, 253, 0.8); backdrop-filter: blur(20px); }
    </style>
</head>
<body class="bg-[#FBFBFD] text-[#1D1D1F] selection:bg-[#007AFF] selection:text-white">

    <nav class="glass border-b border-[#E8E8ED] fixed w-full top-0 z-50 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-[15px] font-semibold tracking-tight">SOLE DISTRICT</a>
            <div class="hidden md:flex gap-12 text-[12px] font-medium text-[#86868B]">
                <a href="{{ route('shop') }}" class="hover:text-black transition-colors">Shop</a>
                <a href="{{ route('shop') }}" class="hover:text-black transition-colors">Collections</a>
                <a href="#" class="hover:text-black transition-colors">Support</a>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('cart') }}" class="text-[12px] font-medium text-[#1D1D1F] hover:text-[#007AFF] transition-colors">
                    Bag ({{ app(\App\Services\CartService::class)->count() }})
                </a>
            </div>
        </div>
    </nav>

    <main class="pt-20">
        {{ $slot }}
    </main>

</body>
</html>