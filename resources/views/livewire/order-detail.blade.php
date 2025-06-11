@section('title')
    Detail Pesanan
@endsection

<div class="mx-auto max-w-screen-xl min-h-screen bg-white pb-[70px] md:px-10 md:pb-10 pt-0 md:pt-[72px]">
    <!-- Header -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-full md:max-w-screen-xl bg-white z-50">
        <div class="relative flex items-center justify-between h-16 px-4">
            <a href="{{ route('orders') }}" wire:navigate class="hover:bg-gray-50 rounded-full">
                <i class="bi bi-chevron-left text-xl"></i>
            </a>
            <h1 class="absolute left-1/2 -translate-x-1/2 text-lg font-medium">Detail Pesanan</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-20 md:pt-0 p-4 md:pb-40">
        <!-- Order Status -->
        <div class="bg-{{ $statusInfo['color'] }}-50 p-4 rounded-xl mb-6">
            <div class="flex items-center gap-3">
                <i class="bi {{ $statusInfo['icon'] }} text-2xl text-{{ $statusInfo['color'] }}-500"></i>
                <div>
                    <h2 class="font-medium text-{{ $statusInfo['color'] }}-600">{{ $statusInfo['title'] }}</h2>
                    <p class="text-sm text-{{ $statusInfo['color'] }}-600">{{ $statusInfo['message'] }}</p>
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="border border-gray-200 rounded-xl overflow-hidden mb-6">
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-medium">Detail Pesanan</h3>
                    <span class="text-sm text-gray-500">{{ $order->order_number }}</span>
                </div>
                <div class="text-sm text-gray-500"> {{ $order->created_at->format('d M Y H:i') }}</div>
            </div>

            <div class="p-4">
                @foreach ($order->items as $item)
                    <div class="flex gap-3 pb-4 border-b border-gray-100">
                        <img src="{{ $item->product->image_url ?? asset('image/no-pictures.png') }}" alt="Product"
                            class="w-20 h-20 object-cover rounded-lg">
                        <div>
                            <h4 class="font-medium">{{ $item->product_name }}</h4>
                            <div class="mt-1">
                                <span class="text-sm">{{ $item->quantity }} x </span>
                                <span class="font-medium">Rp {{ number_format($item->price, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if (count($shopsWithShipping) > 0)
                    <div class="pt-4 space-y-2">
                        <span class="font-semibold">Ongkir</span>
                        @foreach ($shopsWithShipping as $shop)
                            <div class="flex justify-between">
                                <span>Tenant {{ $shop['name'] }}</span>
                                <span class="text-primary">Rp {{ number_format($shop['ongkir'], 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="pt-2 border-t border-gray-200">
                    <div class="flex justify-between font-medium">
                        <span>Total</span>
                        <span class="text-primary">
                            Rp {{ number_format($order->total_amount, 2, ',', '.') }}
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Shipping Details -->
        <div class="border border-gray-200 rounded-xl overflow-hidden mb-6">
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <h3 class="font-medium">Informasi Pemesan</h3>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex gap-2">
                    <span class="text-gray-600 min-w-[140px]">Nama Wali</span>
                    <span>: {{ $order->recipient_name }}</span>
                </div>
                <div class="flex gap-2">
                    <span class="text-gray-600 min-w-[140px]">Nama Santri</span>
                    <span>: {{ $order->nama_santri }}</span>
                </div>
                <div class="flex gap-2">
                    <span class="text-gray-600 min-w-[140px]">Kelas Santri</span>
                    <span>: {{ $order->classroom->name }}</span>
                </div>
                <div class="flex gap-2">
                    <span class="text-gray-600 min-w-[140px]">No HP Wali</span>
                    <span>: {{ $order->phone }}</span>
                </div>
                <div class="flex gap-2">
                    <span class="text-gray-600 min-w-[140px]">Catatan Tambahan</span>
                    <span>: {{ $order->notes }}</span>
                </div>
            </div>
        </div>

        @if ($order->status === 'pending' && $order->payment_gateway_transaction_id == null)
            <!-- Payment Instructions -->
            <div class="space-y-4">
                <h3 class="font-medium">Petunjuk Pembayaran</h3>

                @foreach ($paymentMethods as $item)
                    <!-- BCA -->
                    <div class="border rounded-xl overflow-hidden">
                        <div class="flex items-center gap-3 p-4 bg-gray-50 border-b">
                            <img src="{{ Storage::url($item->image) }}" alt="BCA" class="h-6">
                            <span class="font-medium">{{ $item->name }}</span>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-center">
                                <div class="space-y-1">
                                    <div class="text-sm text-gray-500">Nomor Rekening:</div>
                                    <div class="font-mono font-medium text-lg">{{ $item->account_number }}</div>
                                    <div class="text-sm text-gray-500">a.n. {{ $item->account_name }}</div>
                                </div>
                                <button class="text-primary hover:text-primary/80 copy-account"
                                    data-account="{{ $item->account_number }}">
                                    <i class="bi bi-clipboard text-xl"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Important Notes -->
            <div class="mt-6 p-4 bg-blue-50 rounded-xl">
                <div class="flex items-start gap-3">
                    <i class="bi bi-info-circle-fill text-blue-500 mt-0.5"></i>
                    <div class="text-sm text-blue-700">
                        <p class="font-medium mb-1">Penting:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>TOLONG CANTUMKAN NAMA SANTRI PADA BERITA ACARA PENGIRIMAN UANGNYA</li>
                            <li>Transfer sesuai dengan nominal yang tertera</li>
                            <li>Simpan bukti pembayaran</li>
                            <li>Upload bukti pembayaran setelah transfer</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if ($order->payment_proof)
            <div class="p-4 border border-gray-200 rounded-xl mb-6 mt-6">
                <h3 class="font-medium mb-4">Bukti Pembayaran</h3>
                <div class="space-y-3">
                    <img src="{{ Storage::url($order->payment_proof) }}" alt="Bukti Pembayaran"
                        class="w-full rounded-lg border border-gray-100" />

                </div>
            </div>
        @endif
    </div>



    @if ($order->status === 'pending' && $order->payment_gateway_transaction_id == null && $order->payment_proof == null)
        <!-- Bottom Button -->
        <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full md:max-w-screen-xl bg-white  p-4 z-50">
            <a href="{{ route('payment-confirmation', ['orderNumber' => $order->order_number]) }}" wire:navigate
                class="block w-full bg-primary text-white py-3 rounded-xl font-medium hover:bg-primary/90 transition-colors text-center">
                Konfirmasi Pembayaran
            </a>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.copy-account').forEach(button => {
            button.addEventListener('click', function() {
                const accountNumber = this.getAttribute('data-account');
                const tempInput = document.createElement('textarea');
                tempInput.value = accountNumber;
                document.body.appendChild(tempInput);
                tempInput.select();
                tempInput.setSelectionRange(0, 99999); // Untuk iOS
                try {
                    document.execCommand('copy');
                    showCopySuccess(accountNumber);
                } catch (err) {
                    console.error('Gagal menyalin:', err);
                }
                document.body.removeChild(tempInput);
            });
        });
    });

    function showCopySuccess(account) {
        const toast = document.createElement('div');
        toast.innerText = 'Nomor rekening berhasil disalin: ' + account;
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.left = '50%';
        toast.style.transform = 'translateX(-50%)';
        toast.style.background = 'rgba(0, 0, 0, 0.75)';
        toast.style.color = 'white';
        toast.style.padding = '10px 20px';
        toast.style.borderRadius = '5px';
        toast.style.zIndex = '9999';
        document.body.appendChild(toast);
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 2000);
    }
</script>

@push('scripts')
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const snapToken = "{{ $order->payment_gateway_transaction_id }}";
            const orderId = "{{ $order->order_number }}";
            const orderStatus = "{{ $order->status }}";

            // Tampilkan popup Midtrans hanya jika status masih pending
            if (snapToken && orderStatus === 'pending') {
                try {
                    window.snap.pay(snapToken, {
                        onSuccess: function(result) {
                            window.location.reload();
                        },
                        onPending: function(result) {
                            window.location.reload();
                        },
                        onError: function(result) {
                            alert('Pembayaran gagal! Silakan coba lagi.');
                        },
                        onClose: function() {
                            // Jika user menutup popup, tetap di halaman detail order
                            window.location.reload();
                        }
                    });
                } catch (error) {
                    console.error('Terjadi kesalahan saat membuka popup pembayaran:', error);
                }
            }
        });
    </script>
@endpush
