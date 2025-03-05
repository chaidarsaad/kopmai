@section('title')
    Bayar
@endsection

<div class="max-w-[480px] mx-auto bg-white min-h-screen relative shadow-lg pb-[140px]">
    <!-- Header -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white z-50">
        <div class="relative flex items-center justify-between h-16 px-4 border-b border-gray-100">
            <button onclick="history.back()" class="hover:bg-gray-50 rounded-full">
                <i class="bi bi-chevron-left text-xl"></i>
            </button>
            <h1 class="absolute left-1/2 -translate-x-1/2 text-lg font-medium">Buat Pesanan</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-20 pb-12 px-4 space-y-8">
        <!-- Section 1: Order Summary -->
        <div>
            <div class="flex items-center gap-2 mb-4">
                <i class="bi bi-cart-check text-lg text-primary"></i>
                <h2 class="text-lg font-medium">Ringkasan Pesanan</h2>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <div class="space-y-4">
                    @foreach ($carts as $cart)
                        <div class="flex gap-3">
                            <img src="{{ $cart->product->image_url ?? asset('image/no-pictures.png') }}"
                                alt="{{ $cart->product->name }}" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h3 class="text-sm font-medium line-clamp-2">{{ $cart->product->name }}</h3>
                                <div class="text-sm text-gray-500 mt-1">{{ $cart->quantity }} x Rp
                                    {{ number_format($cart->product->price) }}</div>
                                <div class="text-primary font-medium">Rp
                                    {{ number_format($cart->product->price * $cart->quantity) }}</div>
                            </div>
                        </div>
                    @endforeach
                    @if ($shippingCost > 0)
                        <div class="pt-4 space-y-2">
                            <span class="font-semibold">Ongkir</span>
                            @foreach ($shopsWithShipping as $shop)
                                <div class="flex justify-between">
                                    <span>Tenant {{ $shop['name'] }}</span>
                                    <span class="text-primary">Rp
                                        {{ number_format($shop['ongkir'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="pt-1 space-y-2">
                        <div class="pt-2 border-t border-gray-200">
                            <div class="flex justify-between font-medium">
                                <span>Total</span>
                                <span class="text-primary">Rp
                                    {{ number_format($total + $shippingCost, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Section 2: Recipient Information -->
        <div>
            <div class="flex items-center gap-2 mb-4">
                <i class="bi bi-person text-lg text-primary"></i>
                <h2 class="text-lg font-medium">Data Pemesan</h2>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4 space-y-4">
                <!-- Name -->
                {{-- <div>
                    <label class="text-sm text-gray-600 mb-1.5 block">Nama Wali</label>
                    <input type="text" wire:model="shippingData.recipient_name"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                        placeholder="Masukkan nama lengkap penerima">
                    @error('shippingData.recipient_name')
                        <span class="text-red-500 text-lg mt-1">{{ $message }}</span>
                    @enderror
                </div> --}}

                <!-- santri -->
                <div>
                    <label class="text-sm text-gray-600 mb-1.5 block">Nama Santri</label>
                    <input type="text" wire:model="shippingData.nama_santri"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                        placeholder="Masukkan nama lengkap santri">
                    @error('shippingData.nama_santri')
                        <span class="text-red-500 text-lg mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- kelas santri -->
                <div>
                    <label class="text-sm text-gray-600 mb-1.5 block">Kelas Santri</label>
                    <select wire:model="shippingData.classroom_id"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelasList as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                    @error('shippingData.classroom_id')
                        <span class="text-red-500 text-lg mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="text-sm text-gray-600 mb-1.5 block">No HP Wali</label>
                    <input wire:model="shippingData.phone" type="tel"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                        placeholder="Contoh: 08123456789">
                    @error('shippingData.phone')
                        <span class="text-red-500 text-lg mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>


        <!-- Section 5: Additional Notes -->
        <div>
            <div class="flex items-center gap-2 mb-4">
                <i class="bi bi-pencil text-lg text-primary"></i>
                <h2 class="text-lg font-medium">Catatan Tambahan</h2>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-4">
                <textarea wire:model.live="shippingData.notes"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                    rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
            </div>
        </div>
    </div>

    <!-- Fixed Bottom Section -->
    <div
        class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white border-t border-gray-100 p-4 z-50">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm text-gray-600">Total Pembayaran:</p>
                <p class="text-lg font-semibold text-primary">Rp
                    {{ number_format($total + $shippingCost, 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500">{{ count($carts) }} Produk</p>
            </div>
        </div>

        <button wire:click="createOrder"
            class="w-full h-12 flex items-center justify-center gap-2 rounded-full bg-primary text-white font-medium hover:bg-primary/90 transition-colors">
            <i class="bi bi-bag-check"></i>
            Buat Pesanan
        </button>
    </div>
</div>

@push('scripts')
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('payment-success', (data) => {
                const snapToken = data[0].payment_gateway_transaction_id;
                const orderId = data[0].order_id;

                if (snapToken) {
                    try {
                        window.snap.pay(snapToken, {
                            onSuccess: function(result) {
                                window.location.href = `/order-detail/${orderId}`;
                            },
                            onPending: function(result) {
                                window.location.href = `/order-detail/${orderId}`;
                            },
                            onError: function(result) {
                                alert('Pembayaran gagal! Silakan coba lagi.');
                            },
                            onClose: function() {
                                alert(
                                    'Anda menutup halaman pembayaran sebelum menyelesaikan transaksi'
                                );
                                window.location.href = `/`;
                            }
                        });
                    } catch (error) {
                        alert('Terjadi kesalahan saat membuka popup pembayaran');
                    }
                }
            });
        });
    </script>
@endpush
