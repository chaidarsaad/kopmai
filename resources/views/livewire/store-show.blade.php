@section('title')
    Beranda
@endsection

<!-- Main Container -->
<div class="mx-auto max-w-screen-lg min-h-screen bg-white pb-[70px] md:px-10 md:pb-10 pt-0 md:pt-[72px]">
    <!-- Banner -->
    <div
        class="h-[160px] md:h-fit md:mt-1 relative overflow-hidden bg-gradient-to-br from-primary to-secondary md:rounded-2xl">
        @if ($store->bannerUrl)
            <img src="{{ $store->bannerUrl }}" alt="Banner" class="w-full h-full object-cover">
        @endif
        <div class="absolute inset-0 opacity-50 pattern-dots"></div>
    </div>

    <!-- Profile Section -->
    <div class="relative -mt-12 md:-mt-16 px-5 md:px-0">
        <div
            class="w-[90px] h-[90px] md:w-[120px] md:h-[120px] bg-gradient-to-br from-primary to-secondary rounded-2xl flex items-center justify-center shadow-lg">
            <img src="{{ $store->imageUrl ?? asset('image/store.png') }}" alt="{{ $store->name }}"
                class="w-[65px] h-[65px] md:w-[80px] md:h-[80px] object-cover rounded-lg">
        </div>
        <h4 class="mt-3 mb-1 text-gray-800 font-semibold text-xl md:text-2xl">{{ $store->name }}</h4>
        <p class="text-gray-500 text-sm md:text-base">{{ $store->description }}</p>
    </div>

    @if ($store->is_open)
        <!-- Filter Controls -->
        <div class="mt-6 px-4 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
            <select wire:change="setFilterType($event.target.value)"
                class="w-full px-4 h-10 rounded-full border bg-no-repeat bg-[right_1rem_center] pr-10 focus:ring-primary focus:border-primary">
                <option value="category" @selected($selectedFilterType === 'category')>Produk Berdasarkan Kategori</option>
                <option value="shop" @selected($selectedFilterType === 'shop')>Produk Berdasarkan Tenant</option>
            </select>

            @if ($selectedFilterType === 'category')
                <select wire:model.live="selectedCategory"
                    class="w-full px-4 h-10 rounded-full border {{ $selectedCategory !== 'all' ? 'border-primary' : 'border-gray-200' }} text-gray-600 focus:border-primary focus:ring-primary">
                    <option value="all">Semua Kategori</option>
                    @if ($paketSantri)
                        <option value="{{ $paketSantri->id }}">{{ $paketSantri->name }}</option>
                    @endif
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            @elseif ($selectedFilterType === 'shop')
                <select wire:model.live="selectedShop"
                    class="w-full px-4 h-10 rounded-full border {{ $selectedShop !== 'all' ? 'border-primary' : 'border-gray-200' }} text-gray-600 focus:border-primary focus:ring-primary">
                    <option value="all">Semua Tenant</option>
                    @foreach ($shops as $shop)
                        <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                    @endforeach
                </select>
            @endif

            <!-- Form Pencarian -->
            <a href="{{ route('search.page') }}" wire:navigate
                class="block h-10 w-full px-4 py-2 border border-gray-300 rounded-full bg-white text-gray-500 text-sm text-left">
                Cari produk...
            </a>
        </div>
    @endif

    <!-- Produk Section -->
    <div class="mt-6 px-4">
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
                        <a wire:navigate href="{{ route('product.detail', ['slug' => $item->slug]) }}">
                            <div class="relative w-full h-[180px] md:h-[250px]">
                                <img src="{{ $item->image_url ?? asset('image/no-pictures.png') }}"
                                    alt="{{ $item->name }}" class="w-full h-full object-cover">
                            </div>
                        </a>
                        <div class="p-3 flex flex-col flex-grow">
                            <a wire:navigate href="{{ route('product.detail', ['slug' => $item->slug]) }}">
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
