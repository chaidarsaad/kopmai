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

    @if (!isset($hideBottomNav))
        @livewire('components.top-navigation')
    @endif

    {{ $slot }}
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
