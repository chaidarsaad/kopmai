@section('title')
    Cari Produk
@endsection

<!-- Main Container -->
<div class="max-w-[480px] mx-auto bg-white min-h-screen relative shadow-lg pb-[70px]">
    <!-- Header -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white z-50">
        <div class="relative flex items-center justify-between h-16 px-4 border-b border-gray-100">
            <button onclick="history.back()" class="hover:bg-gray-50 rounded-full">
                <i class="bi bi-chevron-left text-xl"></i>
            </button>
            <input autofocus type="text"wire:model.live.debounce.1000ms="search" placeholder="Cari produk..."
                class="ml-2 w-full px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-primary focus:outline-none">
            <!-- Tombol Kanan -->
            <a href="{{ route('shopping-cart') }}" wire:navigate
                class="relative hover:bg-gray-50 rounded-full cursor-pointer p-2">
                <i class="bi bi-bag text-2xl"></i>
                <span class="absolute top-0 left-5 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                    {{ $cartCount }}
                </span>
            </a>
        </div>
    </div>

    <div class="p-3 mt-16">
        @if ($products->isEmpty())
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-12 px-4">
                <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                    <i class="bi bi-bag-x text-4xl text-primary"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Produk</h3>
            </div>
        @else
            <div class="grid grid-cols-2 gap-3 items-start">
                @foreach ($products as $item)
                    <div
                        class="bg-white rounded-2xl overflow-hidden shadow-sm hover:-translate-y-1 transition-transform duration-300 flex flex-col h-auto">

                        <a wire:navigate href="{{ route('product.detail', ['slug' => $item->slug]) }}">
                            <div class="relative">

                                <img src="{{ $item->image_url ?? asset('image/no-pictures.png') }}"
                                    alt="{{ $item->name }}" class="w-full h-[180px] object-cover">
                            </div>
                            <div class="p-3">
                                <h6 class="text-lg font-medium text-gray-700 line-clamp-2">{{ $item->name }}</h6>
                                @if (isset($item->shop_id) && $item->shop_id !== '')
                                    <p class="text-md text-gray-700 mt-1">{{ $item->shop->name }}</p>
                                @endif
                                <div class="mt-2 flex items-center gap-1">
                                    <span class="text-md text-gray-500">Rp</span>
                                    <span
                                        class="text-primary font-semibold">{{ number_format($item->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </a>
                        <!-- Tombol Tambah ke Keranjang -->
                        <div class="p-3">
                            <button wire:click="addToCart({{ $item->id }})"
                                class="w-full flex items-center justify-center bg-primary text-white rounded-lg py-2 font-semibold hover:bg-primary/90 transition">
                                + Keranjang
                            </button>
                        </div>
                    </div>
                @endforeach

            </div>
            @if ($hasMoreProducts)
                <div x-intersect.full="$wire.loadMore()">
                    <div wire:target="loadMore" class="mt-3">
                        <div class="grid grid-cols-2 gap-3 auto-rows-auto">
                            <div
                                class="animate-pulse bg-white rounded-2xl overflow-hidden shadow-sm hover:-translate-y-1 transition-transform duration-300">
                                <div class="relative w-full h-[180px] bg-gray-200"></div>

                                <div class="p-3">
                                    <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                                    <div class="flex items-center gap-1 mt-2">
                                        <div class="h-4 bg-gray-200 rounded w-6"></div>
                                        <div class="h-5 bg-gray-200 rounded w-16"></div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="animate-pulse bg-white rounded-2xl overflow-hidden shadow-sm hover:-translate-y-1 transition-transform duration-300">
                                <div class="relative w-full h-[180px] bg-gray-200"></div>

                                <div class="p-3">
                                    <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                                    <div class="flex items-center gap-1 mt-2">
                                        <div class="h-4 bg-gray-200 rounded w-6"></div>
                                        <div class="h-5 bg-gray-200 rounded w-16"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        @endif
    </div>
</div>
