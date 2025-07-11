@section('title')
    Beranda
@endsection

<!-- Main Container -->
<div class="mx-auto max-w-screen-xl min-h-screen bg-white pb-[70px] md:px-10 md:pb-10 pt-0 md:pt-[72px]">
    <!-- carousel -->
    @if ($carousels->isNotEmpty())
        <div x-data="carousel()" class="relative w-full overflow-hidden">
            <!-- Slides -->
            <div class="flex transition-transform duration-500 ease-in-out"
                :style="`transform: translateX(-${active * 100}%)`">
                @foreach ($carousels as $carousel)
                    <div class="w-full flex-shrink-0 aspect-[2/1] flex items-center justify-center bg-white">
                        @if (!empty($carousel->url))
                            <a href="{{ $carousel->url }}" wire:navigate
                                class="w-full h-full flex items-center justify-center">
                                <img src="{{ Storage::url($carousel->image) }}" alt="carousel image"
                                    class="w-full h-full object-contain rounded-none md:rounded-xl" />
                            </a>
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <img src="{{ Storage::url($carousel->image) }}" alt="carousel image"
                                    class="w-full h-full object-contain rounded-none md:rounded-xl" />
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Ultra Small Numeric Slide Indicator -->
            <div
                class="absolute bottom-2 left-1/2 transform -translate-x-1/2 bg-black/30 text-white text-[9px] px-1.5 py-[1px] rounded-full leading-none">
                <span x-text="`${active + 1} / ${total}`"></span>
            </div>

            <!-- Navigation Arrows -->
            <button @click="prev()"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black/40 text-white p-1 rounded-full">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button @click="next()"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black/40 text-white p-1 rounded-full">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    @endif




    <!-- Profile Section -->
    <div class="relative -mt-12 md:-mt-16 px-5 md:px-0 md:hidden">
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
    <div class="mt-6 px-4 mb-4">
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
                            onclick="sessionStorage.setItem('previous_url', window.location.href)">
                            <div
                                class="relative w-full h-[200px] flex items-center justify-center overflow-hidden group">
                                @if (!empty($item->first_image_url))
                                    {{-- Utamakan gambar dari array `images` --}}
                                    <img src="{{ $item->first_image_url }}" alt="{{ $item->name }}"
                                        class="max-h-full max-w-full object-contain group-hover:scale-110">
                                @elseif (!empty($item->image_url))
                                    {{-- Jika `images` kosong, baru pakai `image` (Google Drive) --}}
                                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                        class="max-h-full max-w-full object-contain group-hover:scale-110">
                                @else
                                    {{-- Fallback jika dua-duanya kosong --}}
                                    <img src="{{ asset('image/no-pictures.png') }}" alt="Gambar tidak tersedia"
                                        class="max-h-full max-w-full object-contain group-hover:scale-110">
                                @endif
                            </div>
                        </a>
                        <div class="p-3 flex flex-col flex-grow">
                            <a wire:navigate href="{{ route('product.detail', ['slug' => $item->slug]) }}"
                                onclick="sessionStorage.setItem('previous_url', window.location.href)">
                                <h6 class="text-base font-medium text-gray-700 line-clamp-2">{{ $item->name }}</h6>
                            </a>
                            @if (!empty($item->shop_id))
                                <p class="text-sm text-gray-700 mt-1">{{ $item->shop->name }}</p>
                            @endif
                            <div class="mt-2 flex items-center gap-1">
                                <span class="text-sm text-gray-500">Rp</span>
                                <span
                                    class="text-primary font-semibold">{{ number_format($item->price, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex-grow"></div> <!-- Memastikan ruang di bawah isi produk -->
                            <button wire:click="addToCart({{ $item->id }})" wire:loading.attr="disabled"
                                wire:target="addToCart({{ $item->id }})"
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

@push('scripts')
    <script>
        function carousel() {
            return {
                active: 0,
                total: {{ $carousels->count() }},
                interval: null,
                init() {
                    this.start()
                },
                start() {
                    this.interval = setInterval(() => {
                        this.next()
                    }, 10000)
                },
                stop() {
                    clearInterval(this.interval)
                    this.interval = null
                },
                next() {
                    this.active = (this.active + 1) % this.total
                },
                prev() {
                    this.active = (this.active - 1 + this.total) % this.total
                },
                goTo(index) {
                    this.active = index
                }
            }
        }
    </script>
@endpush
