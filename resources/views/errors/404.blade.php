<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $store->name ?? 'Toko Online' }} | @yield('title')</title>

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/png" href="{{ $store->imageUrl ?? asset('image/store.png') }}">
    <link rel="apple-touch-icon" href="{{ $store->imageUrl ?? asset('image/store.png') }}">
    <meta name="msapplication-TileImage" content="{{ $store->imageUrl ?? asset('image/store.png') }}">
    <meta name="theme-color" content= "{{ $store->primary_color ?? '#ff6666' }}">
    <script>
        // Simpan console.warn asli
        const originalWarn = console.warn;

        // Ganti console.warn untuk sementara
        console.warn = function(message, ...args) {
            if (
                typeof message === "string" &&
                message.includes(
                    "cdn.tailwindcss.com should not be used in production"
                )
            ) {
                return; // abaikan pesan ini
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

    @if (!isset($hideTopNav))
        @livewire('components.top-navigation')
    @endif

    <div class="flex items-center justify-center h-[calc(100vh-70px)] md:h-[calc(100vh-72px)] px-4">
        <div class="text-center">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Oops, halaman tidak ditemukan</h3>
        </div>
    </div>


    @if (!isset($hideBottomNav))
        @livewire('components.bottom-navigation')
    @endif

    @livewire('components.alert')

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    @livewireScripts
    @stack('scripts')
</body>

</html>
