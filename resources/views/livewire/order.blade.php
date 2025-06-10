@section('title')
    Pesanan
@endsection

<div class="mx-auto max-w-screen-xl min-h-screen bg-white pb-[70px] md:px-10 md:pb-10 pt-0 md:pt-[72px]">
    <!-- Header -->
    <div class="md:hidden fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white z-50">
        <div class="relative flex items-center justify-between h-16 px-4 border-b border-gray-100">
            <h1 class="absolute left-1/2 -translate-x-1/2 text-lg font-medium">Pesanan Saya</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-24 md:pt-8 pb-8 px-4 space-y-4">
        <!-- Order Card 1 -->
        @forelse($orders as $order)
            <div x-data="{ open: false }" class="border border-gray-200 rounded-2xl overflow-hidden">
                <!-- Order Header -->
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-gray-400">
                            {{ $order->order_number }}
                        </div>
                        <span class="{{ $this->getStatusClass($order->status) }} font-medium">
                            {{ $statusLabels[$order->status] }}
                        </span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <div>
                            Nama Santri: {{ $order->nama_santri }}
                        </div>
                        <div>
                            {{ $order->created_at->locale('id')->translatedFormat('l, d F Y H:i') }}
                        </div>
                    </div>
                </div>

                <!-- Accordion Toggle Button -->
                <div class="px-4 pt-4">
                    <button @click="open = !open"
                        class="text-sm text-primary font-semibold hover:underline focus:outline-none">
                        <span x-text="open ? 'Sembunyikan produk' : 'Klik untuk lihat produk dipesan'"></span>
                    </button>
                </div>

                <!-- Order Items Accordion -->
                <div x-show="open" x-collapse>
                    @foreach ($order->items as $item)
                        <div class="p-4">
                            <div class="flex gap-3">
                                <img src="{{ $item->product->image_url ?? asset('image/no-pictures.png') }}"
                                    alt="{{ $item->product->name }}" class="w-20 h-20 object-cover rounded-lg">
                                <div>
                                    <h3 class="text-sm font-medium">{{ $item->product->name }}</h3>
                                    <div class="mt-2">
                                        <span class="text-sm text-gray-600">{{ $item->quantity }} x </span>
                                        <span class="text-sm font-medium">Rp
                                            {{ number_format($item->price, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Footer -->
                <div class="px-4 py-3 border-t border-gray-100">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Belanja</span>
                        <span class="text-primary font-semibold">Rp
                            {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Order Actions -->
                <div class="p-4 border-t border-gray-100 flex justify-end gap-3">
                    <a wire:navigate href="{{ route('order-detail', ['orderNumber' => $order->order_number]) }}"
                        class="px-4 py-2 text-sm bg-primary text-white rounded-full hover:bg-primary/90">
                        Lihat Detail
                    </a>
                </div>
            </div>

        @empty
            <div class="flex flex-col items-center justify-center min-h-[60vh]">
                <!-- Icon pesanan kosong -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-gray-300 mb-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <p class="text-xl font-medium text-gray-400 mb-2">Belum Ada Pesanan</p>
                <p class="text-sm text-gray-400">Anda belum melakukan pemesanan apapun</p>

                <!-- Tombol Mulai Belanja -->
                <a wire:navigate href="{{ route('home') }}"
                    class="mt-6 px-6 py-2 bg-primary text-white rounded-full text-sm hover:bg-primary/90 transition-colors">
                    Mulai Belanja
                </a>
            </div>

        @endforelse
    </div>
</div>
