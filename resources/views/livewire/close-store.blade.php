@section('title')
    Tutup
@endsection

<div class="mx-auto max-w-screen-xl min-h-screen bg-white pb-[70px] md:px-10 md:pb-10 pt-0 md:pt-[72px]">
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

    <!-- Produk Section -->
    <div class="mt-6 px-4 mb-4">
        <div class="flex flex-col items-center justify-center py-12 px-4">
            <div class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                <i class="bi bi-bag-x text-4xl text-primary"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2 text-center">Koperasi sedang tutup, silahkan kembali lagi
                nanti</h3>
        </div>

    </div>


</div>
