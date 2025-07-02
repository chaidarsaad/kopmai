@section('title')
    Detail Produk
@endsection

<div class="mx-auto max-w-screen-xl min-h-screen bg-white pb-[70px] md:px-10 md:pb-10 pt-0 md:pt-[72px]">
    <!-- Header with Back Button -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-full md:max-w-screen-xl bg-white z-50">
        <div class="relative flex items-center justify-between h-16 px-4">
            <!-- Tombol Kiri -->
            <button type="submit"
                onclick="event.preventDefault();
             const backUrl = sessionStorage.getItem('previous_url') || '{{ route('home') }}';
             sessionStorage.removeItem('previous_url');
             window.Livewire.navigate(backUrl);"
                wire:navigate class="hover:bg-gray-50 rounded-full">
                <i class="bi bi-chevron-left text-xl"></i>
            </button>


            <!-- Judul (Di Tengah) -->
            <h1 class="text-lg font-medium">Detail Produk</h1>

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

    <!-- Main Content -->
    <div class="pt-16 md:pt-4 md:pb-40">
        <!-- Product Images Slider -->
        <div class="relative bg-gray-100">
            @if (!empty($product->image_url))
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                    class="w-full object-contain max-h-[500px] mx-auto">
            @elseif (!empty($currentImage))
                <img src="{{ Storage::url($currentImage) }}" alt="{{ $product->name }}"
                    class="w-full object-contain max-h-[500px] mx-auto">
            @else
                <img src="{{ asset('image/no-pictures.png') }}" alt="Gambar tidak tersedia"
                    class="w-full object-contain max-h-[500px] mx-auto">
            @endif

            @if (count($images) > 1)
                <button wire:click="previousImage"
                    class="absolute left-2 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 text-white"
                    @if ($currentImageIndex == 0) disabled @endif>
                    <i class="bi bi-chevron-left"></i>
                </button>

                <button wire:click="nextImage"
                    class="absolute right-2 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 text-white"
                    @if ($currentImageIndex == count($images) - 1) disabled @endif>
                    <i class="bi bi-chevron-right"></i>
                </button>
            @endif

            @if (count($images) > 1)
                <div class="flex overflow-x-auto space-x-2 p-4 bg-white">
                    @foreach ($images as $index => $image)
                        <img src="{{ Storage::url($image) }}"
                            wire:click="$set('currentImageIndex', {{ $index }})"
                            class="h-16 w-16 object-cover rounded cursor-pointer border
                    {{ $currentImageIndex === $index ? 'border-primary' : 'border-gray-200' }}">
                    @endforeach
                </div>
            @endif

        </div>

        <!-- Product Info -->
        <div class="p-4 border-b border-gray-100">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $product->name }}</h2>
                    @if ($product->shop_id)
                        <div class="mt-1 flex items-center gap-2 text-gray-800 text-lg font-semibold">
                            <i class="bi bi-shop text-primary text-2xl"></i>
                            <span>{{ $product->shop->name }}</span>
                        </div>
                    @endif

                    @if ($product->category)
                        <div class="mt-1 flex items-center gap-2">
                            <i class="bi bi-grid text-primary"></i>
                            <span class="text-sm font-medium text-gray-600">
                                {{ $product->category->name }}
                            </span>
                        </div>
                    @endif

                    <div class="mt-1 flex items-center gap-2">
                        <i class="bi bi-tag text-primary"></i>
                        <span class="text-2xl font-bold text-primary">
                            Rp {{ number_format($product->price, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <div class="p-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold mb-3">Deskripsi Produk</h3>
            <div class="space-y-2 text-gray-600 text-sm">
                {!! $product->description !!}
            </div>
        </div>
    </div>


    <!-- Bottom Navigation for Add to Cart & Buy -->
    <div class="fixed bottom-0 left-1/2 -translate-x-1/2 md:max-w-screen-xl w-full max-w-full bg-white p-4 z-50">
        <div class="flex gap-3">
            <a href="{{ route('home') }}" wire:navigate
                class="flex items-center gap-1 px-3 h-12 rounded-full border border-gray-300 hover:bg-gray-100 text-gray-600">
                <i class="bi bi-house-door text-xl"></i>
                <span class="text-sm font-medium">Beranda</span>
            </a>


            <button wire:click="addToCart({{ $product->id }})" wire:loading.attr="disabled"
                wire:target="addToCart({{ $product->id }})"
                class="flex-1 h-12 flex items-center justify-center rounded-full bg-primary text-white font-medium hover:bg-primary/90 transition-colors"
                x-data="{ loading: false }" x-init="Livewire.hook('message.sent', () => loading = true);
                Livewire.hook('message.processed', () => loading = false)">
                <span x-show="!loading" wire:loading.remove wire:target="addToCart({{ $product->id }})">Tambah ke
                    Keranjang</span>
                <span x-show="loading" wire:loading wire:target="addToCart({{ $product->id }})"
                    class="inline-flex items-center gap-2">
                    <div class="w-4 h-4 border-4 border-t-primary border-gray-200 rounded-full animate-spin">
                    </div>
                </span>
            </button>
        </div>

    </div>
</div>
