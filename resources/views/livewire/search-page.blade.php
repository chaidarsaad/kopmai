@section('title')
    Cari Produk
@endsection

<!-- Main Container -->
<div class="mx-auto max-w-screen-xl min-h-screen bg-white pb-[70px] md:px-10 md:pb-10 pt-0 md:pt-[72px]">
    <!-- Header -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] md:max-w-screen-xl bg-white z-50">
        <div class="relative flex items-center justify-between h-16 px-4">
            <form wire:submit.prevent="resetProducts" class="flex items-center w-full gap-2">
                <a href="{{ route('home') }}" wire:navigate class="hover:bg-gray-50 rounded-full">
                    <i class="bi bi-chevron-left text-xl"></i>
                </a>

                <input type="text" wire:model.defer="search" placeholder="Cari produk..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-primary focus:outline-none">

                <button type="submit" class="hidden"></button> <!-- optional: biar form bisa submit pakai Enter -->

                <!-- Tombol Kanan -->
                <a href="{{ route('shopping-cart') }}" wire:navigate
                    class="relative hover:bg-gray-50 rounded-full cursor-pointer p-2">
                    <i class="bi bi-bag text-2xl"></i>
                    <span
                        class="absolute top-0 left-5 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ $cartCount }}
                    </span>
                </a>
            </form>

        </div>
    </div>

    <!-- Produk Section -->
    <div class="mt-20 md:mt-6 px-4">
        @if ($products->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 px-4">
                <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                    <i class="bi bi-bag-x text-4xl text-primary"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Produk</h3>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4">
                @foreach ($products as $item)
                    <div
                        class="bg-white rounded-2xl overflow-hidden shadow hover:shadow-md transition-transform duration-300 flex flex-col h-full">
                        <a wire:navigate href="{{ route('product.detail', ['slug' => $item->slug]) }}"
                            onclick="localStorage.setItem('previous_url', window.location.href)">
                            <div class="relative w-full h-[180px] md:h-[250px] overflow-hidden group">
                                <img src="{{ $item->image_url ?? asset('image/no-pictures.png') }}"
                                    alt="{{ $item->name }}"
                                    class="w-full h-full object-cover transform transition-transform duration-300 group-hover:scale-110">
                            </div>
                        </a>
                        <div class="p-3 flex flex-col flex-grow">
                            <a wire:navigate href="{{ route('product.detail', ['slug' => $item->slug]) }}"
                                onclick="localStorage.setItem('previous_url', window.location.href)">
                                <h6 class="text-base font-medium text-gray-700 line-clamp-2">{{ $item->name }}</h6>
                            </a>
                            @if (!empty($item->shop_id))
                                <p class="text-sm text-gray-700 mt-1">{{ $item->shop->name }}</p>
                            @endif
                            <div class="mt-2 flex items-center gap-1">
                                <span class="text-sm text-gray-500">Rp</span>
                                <span
                                    class="text-primary font-semibold">{{ number_format($item->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex-grow"></div> <!-- Memastikan ruang di bawah isi produk -->
                            <button wire:click="addToCart({{ $item->id }})"
                                class="w-full bg-primary text-white rounded-lg py-2 font-semibold hover:bg-primary/90 transition">
                                + Keranjang
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($hasMoreProducts)
                <div x-intersect.full="$wire.loadMore()"
                    class="mt-4 flex justify-center items-center space-x-2 pb-4 md:pb-0">
                    <!-- Spinner -->
                    <div class="w-6 h-6 border-4 border-t-primary border-gray-200 rounded-full animate-spin"></div>
                    <!-- Loading text -->
                    <div class="text-sm text-gray-500">Memuat lebih banyak produk...</div>
                </div>
            @endif
        @endif
    </div>
</div>
