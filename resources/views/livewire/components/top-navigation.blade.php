<nav
    class="mx-auto px-[55px] max-w-screen-xl hidden md:flex fixed top-0 left-0 right-0 z-50 items-center justify-between py-4 bg-white">

    <div class="flex items-center gap-2 text-lg font-bold text-gray-800">
        <img src="{{ Storage::url($about->image) }}" alt="{{ $about->name }}" class="h-10 w-10 object-contain">
        {{ $about->name }}
    </div>

    <div class="flex gap-6">
        <a href="{{ route('home') }}" wire:navigate
            class="text-gray-600 hover:text-primary {{ $activeMenu === 'home' ? 'text-primary font-semibold' : '' }}">Beranda</a>
        @auth
            <a href="{{ route('orders') }}" wire:navigate
                class="text-gray-600 hover:text-primary {{ $activeMenu === 'orders' ? 'text-primary font-semibold' : '' }}">Pesanan</a>
        @endauth
        @auth
            <a href="{{ route('profile') }}" wire:navigate
                class="text-gray-600 hover:text-primary {{ $activeMenu === 'profile' ? 'text-primary font-semibold' : '' }}">
                Akun
            </a>
        @else
            <a href="{{ route('login') }}" wire:navigate
                class="text-gray-600 hover:text-primary {{ $activeMenu === 'profile' ? 'text-primary font-semibold' : '' }}">
                Masuk
            </a>
        @endauth

        <a href="{{ route('shopping-cart') }}" wire:navigate
            class="text-gray-600 hover:text-primary {{ $activeMenu === 'shopping-cart' ? 'text-primary font-semibold' : '' }}">
            <i class="bi bi-bag"></i>
        </a>
    </div>
</nav>
