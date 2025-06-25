@section('title')
    Bayar
@endsection

<div class="mx-auto max-w-screen-xl min-h-screen bg-white pb-[140px] md:px-10 md:pb-10 pt-0 md:pt-[0px]">
    <!-- Header -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-full md:max-w-screen-xl bg-white z-50">
        <div class="relative flex items-center justify-between h-16 px-4">
            <button onclick="history.back()" class="hover:bg-gray-50 rounded-full">
                <i class="bi bi-chevron-left text-xl"></i>
            </button>
            <h1 class="absolute left-1/2 -translate-x-1/2 text-lg font-medium">Buat Pesanan</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-20 pb-12 md:pb-40 px-4 space-y-8">
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
                            @if (!empty($cart->product->first_image_url))
                                <img src="{{ $cart->product->first_image_url }}" alt="{{ $cart->product->name }}"
                                    class="w-20 h-20 object-cover rounded-lg">
                            @elseif (!empty($cart->product->image_url))
                                <img src="{{ $cart->product->image_url }}" alt="{{ $cart->product->name }}"
                                    class="w-20 h-20 object-cover rounded-lg">
                            @else
                                <img src="{{ asset('image/no-pictures.png') }}" alt="Gambar tidak tersedia"
                                    class="w-20 h-20 object-cover rounded-lg">
                            @endif
                            <div class="flex-1">
                                <h3 class="text-sm font-medium line-clamp-2">{{ $cart->product->name }}</h3>
                                <div class="text-sm text-gray-500 mt-1">{{ $cart->quantity }} x Rp
                                    {{ number_format($cart->product->price, 2, ',', '.') }}</div>
                                <div class="text-primary font-medium">Rp
                                    {{ number_format($cart->product->price * $cart->quantity, 2, ',', '.') }}</div>
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
                                        {{ number_format($shop['ongkir'], 2, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="pt-1 space-y-2">
                        <div class="pt-2 border-t border-gray-200">
                            <div class="flex justify-between font-medium">
                                <span>Total</span>
                                <span class="text-primary">Rp
                                    {{ number_format($total + $shippingCost, 2, ',', '.') }}</span>
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

                <!-- Searchable Student -->
                <div class="mb-4">
                    <label class="text-sm text-gray-600 mb-1.5 block">Nama Santri</label>

                    <div x-data="{
                        open: @entangle('showStudentDropdown'),
                        closeDropdown() {
                            this.open = false;
                            setTimeout(() => this.open = false, 100);
                        }
                    }" @click.away="closeDropdown()" class="relative">
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="searchStudent"
                                placeholder="Cari nama santri..."
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                                autocomplete="off" @focus="open = true; $wire.showStudents()" />

                            <!-- Tombol Clear (X) -->
                            @if (!empty($searchStudent))
                                <button type="button"
                                    class="absolute inset-y-0 right-8 flex items-center pr-2 text-gray-400 hover:text-gray-600"
                                    wire:click="clearSelectedStudent">
                                    <i class="bi bi-x-circle-fill text-lg"></i>
                                </button>
                            @endif

                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        @if ($shippingData['student_id'])
                            <div class="flex justify-between items-center">
                                <button type="button"
                                    wire:click="openEditStudentModal({{ $shippingData['student_id'] }})"
                                    class="text-sm text-orange-600 hover:underline mt-1">
                                    ✎ Edit Santri
                                </button>
                            </div>
                        @endif

                        <!-- Dropdown Results -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 max-h-60 overflow-auto">
                            @if ($showStudentDropdown)
                                @if (count($filteredStudents) > 0)
                                    <ul>
                                        @foreach ($filteredStudents as $student)
                                            <li wire:click="selectStudent({{ $student['id'] }}, '{{ addslashes($student['nama_santri']) }}')"
                                                class="flex justify-between items-center px-4 py-2 hover:bg-primary/10 cursor-pointer transition-colors">
                                                <div class="truncate max-w-[70%]" class="block w-full truncate">
                                                    {{ $student['nama_santri'] }}
                                                </div>
                                            </li>
                                        @endforeach


                                    </ul>
                                    <div class="px-4 py-2 text-gray-500">
                                        Tidak ada nama Santri yang sesuai?
                                        <button type="button" wire:click="openAddStudentModal"
                                            class="text-sm text-blue-600 hover:underline">
                                            + Tambah Santri
                                        </button>
                                    </div>
                                @else
                                    <div class="px-4 py-2 text-gray-500">
                                        Santri tidak ditemukan
                                        <button type="button" wire:click="openAddStudentModal"
                                            class="text-sm text-blue-600 hover:underline">
                                            + Tambah Santri
                                        </button>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <input type="hidden" wire:model="shippingData.student_id" />
                    @error('shippingData.student_id')
                        <span class="text-red-500 text-lg mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- nomor_induk_santri -->
                {{-- <div>
                    <label class="text-sm text-gray-600 mb-1.5 block">Nomor Induk Santri</label>
                    <input readonly wire:model="shippingData.nomor_induk_santri" type="text"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary">
                    @error('shippingData.nomor_induk_santri')
                        <span class="text-red-500 text-lg mt-1">{{ $message }}</span>
                    @enderror
                </div> --}}

                <!-- nama_wali -->
                <div>
                    <label class="text-sm text-gray-600 mb-1.5 block">Nama BIN / BINTI</label>
                    <input readonly wire:model="shippingData.nama_wali" type="text"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary">
                    @error('shippingData.nama_wali')
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
    <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full md:max-w-screen-xl bg-white p-4 z-50">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm text-gray-600">Total Pembayaran:</p>
                <p class="text-lg font-semibold text-primary">Rp
                    {{ number_format($total + $shippingCost, 2, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500">{{ count($carts) }} Produk</p>
            </div>
        </div>

        <button wire:click="createOrder" wire:loading.attr="disabled" wire:target="createOrder"
            class="w-full h-12 flex items-center justify-center gap-2 rounded-full bg-primary text-white font-medium hover:bg-primary/90 transition-colors"
            x-data="{ loading: false }" x-init="Livewire.hook('message.sent', () => loading = true);
            Livewire.hook('message.processed', () => loading = false)">

            <span x-show="!loading" wire:loading.remove wire:target="createOrder">
                <i class="bi bi-bag-check"></i>
                Buat Pesanan</span>

            <!-- Saat loading -->
            <span x-show="loading" wire:loading wire:target="createOrder" class="inline-flex items-center gap-2">
                <div class="w-6 h-6 border-4 border-t-primary border-gray-200 rounded-full animate-spin">
                </div>
            </span>
        </button>
    </div>

    @if ($showStudentModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
            <div class="bg-white w-full max-w-lg p-6 rounded-lg shadow-lg">
                <h2 class="text-lg font-semibold mb-4">
                    {{ $isEditingStudent ? 'Edit Santri' : 'Tambah Santri' }}
                </h2>

                <form wire:submit.prevent="saveStudent" class="space-y-4">
                    {{-- <div>
                        <label class="text-sm">Nomor Induk Santri</label>
                        <input type="text" wire:model.defer="studentForm.nomor_induk_santri"
                            class="w-full border rounded px-3 py-2" />
                        @error('studentForm.nomor_induk_santri')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div> --}}

                    <div>
                        <label class="text-sm">Nama Santri</label>
                        <input type="text" wire:model.defer="studentForm.nama_santri"
                            class="w-full border rounded px-3 py-2" />
                        @error('studentForm.nama_santri')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm">Nama BIN / BINTI</label>
                        <input type="text" wire:model.defer="studentForm.nama_wali_santri"
                            class="w-full border rounded px-3 py-2" />
                        @error('studentForm.nama_wali_santri')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" wire:click="$set('showStudentModal', false)"
                            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
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
