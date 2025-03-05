@section('title')
    Beranda
@endsection

<!-- Main Container -->
<div class="max-w-[480px] mx-auto bg-white min-h-screen shadow-lg pb-[70px]">
    <!-- Banner -->
    <div class="h-[160px] relative overflow-hidden bg-gradient-to-br from-primary to-secondary">
        @if ($store->bannerUrl)
            <img src="{{ $store->bannerUrl }}" alt="Banner" class="w-full h-full object-cover">
        @endif
        <div class="absolute inset-0 opacity-50 pattern-dots"></div>
    </div>

    <!-- Profile Section -->
    <div class="px-5 relative -mt-10">
        <div
            class="w-[90px] h-[90px] bg-gradient-to-br from-primary to-secondary rounded-[20px] flex items-center justify-center shadow-lg">
            <img src="{{ $store->imageUrl ?? asset('image/store.png') }}" alt="{{ $store->name }}"
                class="w-[65px] h-[65px]">
        </div>
        <h4 class="mt-3 mb-1 text-gray-800 font-semibold text-xl">{{ $store->name }}</h4>
        <p class="text-gray-500 text-sm">{{ $store->description }}</p>
    </div>

    <!-- Navigation Tabs -->
    <div class="mt-5 px-4">
        <select wire:change="setFilterType($event.target.value)"
            class="w-full px-4 h-10 rounded-full  appearance-none bg-no-repeat bg-[right_1rem_center] pr-10 focus:ring-primary focus:border-primary">
            <option value="category" @selected($selectedFilterType === 'category')>Produk berdasarkan Kategori</option>
            <option value="shop" @selected($selectedFilterType === 'shop')>Produk berdasarkan Tenant / Supplier</option>
        </select>
    </div>

    <!-- Filter Options -->
    <div class="mt-5 px-2.5 overflow-x-auto hide-scrollbar">
        <div class="flex gap-2.5 pb-2.5 whitespace-nowrap">
            @if ($selectedFilterType === 'category')
                {{-- tombol paket santri --}}
                @if ($paketSantri)
                    <button wire:click="$set('selectedCategory', '{{ $paketSantri->id }}')"
                        class="px-6 h-10 flex items-center rounded-full transition-colors border {{ $selectedCategory == $paketSantri->id ? 'bg-primary text-white border-primary' : 'text-gray-600 border-gray-200 hover:border-primary hover:text-primary' }}">
                        {{ $paketSantri->name }}
                    </button>
                @endif

                <button wire:click="$set('selectedCategory', 'all')"
                    class="px-6 h-10 flex items-center rounded-full transition-colors border {{ $selectedCategory === 'all' ? 'bg-primary text-white border-primary' : 'text-gray-600 border-gray-200 hover:border-primary hover:text-primary' }}">
                    Semua Kategori
                </button>
                @foreach ($categories as $category)
                    <button wire:click="$set('selectedCategory', '{{ $category->id }}')"
                        class="px-6 h-10 flex items-center rounded-full transition-colors border {{ $selectedCategory == $category->id ? 'bg-primary text-white border-primary' : 'text-gray-600 border-gray-200 hover:border-primary hover:text-primary' }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            @elseif ($selectedFilterType === 'shop')
                <button wire:click="$set('selectedShop', 'all')"
                    class="px-6 h-10 flex items-center rounded-full transition-colors border {{ $selectedShop === 'all' ? 'bg-primary text-white border-primary' : 'text-gray-600 border-gray-200 hover:border-primary hover:text-primary' }}">
                    Semua Tenant
                </button>
                @foreach ($shops as $shop)
                    <button wire:click="$set('selectedShop', '{{ $shop->id }}')"
                        class="px-6 h-10 flex items-center rounded-full transition-colors border {{ $selectedShop == $shop->id ? 'bg-primary text-white border-primary' : 'text-gray-600 border-gray-200 hover:border-primary hover:text-primary' }}">
                        {{ $shop->name }}
                    </button>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Form Pencarian -->
    <div class="px-4 mt-4">
        <a href="{{ route('search.page') }}" wire:navigate
            class="block h-10 w-full px-4 py-2 border border-gray-300 rounded-full bg-white text-gray-500 text-sm text-left">
            Cari produk...
        </a>
    </div>

    <div class="p-3 mt-4">
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
