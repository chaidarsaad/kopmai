<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOPMAI STORE</title>

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/png" href="{{ $store->imageUrl ?? asset('image/store.png') }}">
    <link rel="apple-touch-icon" href="{{ $store->imageUrl ?? asset('image/store.png') }}">
    <meta name="msapplication-TileImage" content="{{ $store->imageUrl ?? asset('image/store.png') }}">
    <meta name="theme-color" content= "{{ $store->primary_color ?? '#ff6666' }}">
    <script>
        const originalWarn = console.warn;
        console.warn = function(message, ...args) {
            if (
                typeof message === "string" &&
                message.includes(
                    "cdn.tailwindcss.com should not be used in production"
                )
            ) {
                return;
            }
            originalWarn.apply(console, [message, ...args]);
        };
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "{{ $store->primary_color ?? '#ff6666' }}",
                        secondary: "{{ $store->secondary_color ?? '#818CF8' }}",
                        accent: '#C7D2FE',
                    }
                }
            }
        }
    </script>
    @filamentPWA
</head>

<body class="bg-white">

    <nav
        class="mx-auto px-[55px] max-w-screen-xl hidden md:flex fixed top-0 left-0 right-0 z-50 items-center justify-between py-4 bg-white">

        {{-- <div class="flex items-center gap-2 text-lg font-bold text-gray-800">
        <img src="{{ Storage::url($about->image) }}" alt="{{ $about->name }}" class="h-10 w-10 object-contain">
        {{ $about->name }}
    </div> --}}
        <div class="flex items-center gap-2">
            <img src="{{ asset('image/navkopmai.png') }}" alt="Logo" class="h-10 w-full object-contain">
        </div>

        <div class="flex gap-6 text-lg">
            <a href="{{ route('home') }}" wire:navigate class="text-gray-600 hover:text-primary ">Beranda</a>
            @auth
                <a href="{{ route('shopping-cart') }}" wire:navigate
                    onclick="sessionStorage.setItem('previous_url', window.location.href)"
                    class="text-gray-600 hover:text-primary">
                    Keranjang</a>
                <a href="{{ route('orders') }}" wire:navigate class="text-gray-600 hover:text-primary">Pesanan</a>
            @endauth
            @auth
                <a href="{{ route('profile') }}" wire:navigate class="text-gray-600 hover:text-primary">
                    Akun
                </a>
            @else
                <a href="{{ route('login') }}" wire:navigate class="text-gray-600 hover:text-primary">
                    Masuk
                </a>
            @endauth

        </div>
    </nav>


    <div class="flex items-center justify-center h-[calc(100vh-70px)] md:h-[calc(100vh-72px)] px-4">
        <div class="text-center">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Oops, halaman tidak ditemukan</h3>
        </div>
    </div>


    <!-- Bottom Navigation -->
    <nav
        class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-full bg-white border-t border-gray-200 h-[70px] z-50 md:hidden">

        <div class="grid grid-cols-4 h-full">
            <a wire:navigate href="{{ route('home') }}" wire:click="setActiveMenu('home')"
                class="flex flex-col items-center justify-center">
                <i class="bi bi-house text-2xl mb-0.5"></i>
                <span class="text-xs">Beranda</span>
            </a>
            <a wire:navigate href="{{ route('shopping-cart') }}" wire:click="setActiveMenu('shopping-cart')"
                class="flex flex-col items-center justify-center">
                <i class="bi bi-bag text-2xl mb-0.5"></i>
                <span class="text-xs">Keranjang</span>
            </a>
            <a wire:navigate href="{{ route('orders') }}" wire:click="setActiveMenu('orders')"
                class="flex flex-col items-center justify-center">
                <i class="bi bi-receipt text-2xl mb-0.5"></i>
                <span class="text-xs">Pesanan</span>
            </a>
            <a wire:navigate href="{{ route('profile') }}" wire:click="setActiveMenu('profile')"
                class="flex flex-col items-center justify-center">
                <i class="bi bi-person text-2xl mb-0.5"></i>
                <span class="text-xs">Akun</span>
            </a>
        </div>
    </nav>



    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    @livewireScripts
    @stack('scripts')
</body>

</html>
