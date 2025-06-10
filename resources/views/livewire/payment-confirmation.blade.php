@section('title')
    Konfirmasi Pembayaran
@endsection

<div class="mx-auto max-w-screen-xl min-h-screen bg-white pb-24">
    <!-- Header -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] md:max-w-screen-xl bg-white z-50">
        <div class="relative flex items-center justify-between h-16 px-4">
            <button onclick="history.back()" class="hover:bg-gray-50 rounded-full">
                <i class="bi bi-chevron-left text-xl"></i>
            </button>
            <h1 class="absolute left-1/2 -translate-x-1/2 text-lg font-medium whitespace-nowrap">Konfirmasi Pembayaran
            </h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-20 p-4">
        <!-- Error Message -->
        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Order Info -->
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <div class="flex justify-between items-center mb-2">
                <h2 class="font-medium">Detail Pesanan</h2>
                <span class="text-sm text-gray-500">{{ $order->order_number }}</span>
            </div>
            <div class="text-sm text-gray-500 mb-3">{{ $order->created_at->format('d M Y H:i') }}</div>
            <div class="flex justify-between items-center font-medium">
                <span>Total Pembayaran</span>
                <span class="text-primary">Rp{{ number_format($order->total_amount, 2, ',', '.') }}</span>
            </div>
        </div>

        <!-- Upload Form -->
        <form wire:submit.prevent="submit" class="space-y-4">
            <!-- Upload Payment Proof -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Transfer</label>
                <div x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true"
                    x-on:livewire-upload-finish="isUploading = false" x-on:livewire-upload-error="isUploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress">
                    <label
                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-primary transition-colors cursor-pointer">
                        <div class="space-y-2 text-center">
                            @if ($payment_proof)
                                {{-- <img src="{{ $payment_proof->temporaryUrl() }}"
                                    class="mx-auto h-20 w-20 object-cover rounded-lg"> --}}
                                <p class="text-green-600 font-semibold">Bukti transfer sukses diupload, silahkan klik
                                    tombol Kirim Konfirmasi</p>
                            @else
                                <div class="mx-auto h-12 w-12 text-gray-400">
                                    <i class="bi bi-image text-4xl"></i>
                                </div>
                            @endif
                            <div class="flex items-center justify-center text-sm text-gray-600 gap-x-1">
                                <label
                                    class="relative rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary cursor-pointer">
                                    <span>Upload file</span>
                                    <input type="file" wire:model="payment_proof" class="sr-only" accept="image/*">
                                </label>
                                <p>atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">Ukuran maksimal 2MB</p>
                        </div>
                    </label>

                    <!-- Progress Bar -->
                    <div x-show="isUploading" class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-primary h-2.5 rounded-full" x-bind:style="`width: ${progress}%`"></div>
                        </div>
                    </div>
                </div>
                @error('payment_proof')
                    <span class="text-red-500 text-lg mt-1">{{ $message }}</span>
                @enderror
            </div>
        </form>
    </div>

    <!-- Bottom Button -->
    <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full md:max-w-screen-xl bg-white p-4 z-50">
        <button wire:click="submit" wire:loading.attr="disabled" wire:target="submit"
            class="w-full bg-primary text-white py-3 rounded-xl font-medium hover:bg-primary/90 transition-colors"
            x-data="{ loading: false }" x-init="Livewire.hook('message.sent', () => loading = true);
            Livewire.hook('message.processed', () => loading = false)">

            <span x-show="!loading" wire:loading.remove wire:target="submit">Kirim Konfirmasi</span>

            <!-- Saat loading -->
            <span x-show="loading" wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                <div class="w-4 h-4 border-4 border-t-primary border-gray-200 rounded-full animate-spin">
                </div>
            </span>
        </button>
    </div>
</div>
