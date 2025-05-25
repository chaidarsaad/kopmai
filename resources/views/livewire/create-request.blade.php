@section('title')
    Buat Permohonan
@endsection

<div class="mx-auto max-w-screen-xl min-h-screen bg-white pb-[70px] md:px-10 md:pb-10 pt-0 md:pt-[72px]">
    <!-- Header -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] md:max-w-screen-xl bg-white z-50">
        <div class="relative flex items-center justify-between h-16 px-4">
            <button onclick="history.back()" class="hover:bg-gray-50 rounded-full p-2">
                <i class="bi bi-chevron-left text-xl"></i>
            </button>
            <h1 class="absolute left-1/2 -translate-x-1/2 text-lg font-semibold">Buat Permohonan</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="pt-24 md:pt-12 pb-20 px-4 space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 p-4 space-y-4">

            <!-- Nama Pemesan -->
            <div>
                <label class="text-sm text-gray-600 mb-1.5 block">Nama Pemesan</label>
                <input type="text" wire:model="createRequestData.nama_pemesan"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                    placeholder="Masukkan nama pemesan">
                @error('createRequestData.nama_pemesan')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tanggal Permohonan -->
            <div>
                <label class="text-sm text-gray-600 mb-1.5 block">Tanggal Permohonan</label>
                <input type="date" wire:model="createRequestData.tanggal_permohonan"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary">
                @error('createRequestData.tanggal_permohonan')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Deadline Permohonan -->
            <div>
                <label class="text-sm text-gray-600 mb-1.5 block">Deadline Permohonan</label>
                <input type="date" wire:model="createRequestData.deadline"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary">
                @error('createRequestData.deadline')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kelas / Divisi -->
            <div>
                <label class="text-sm text-gray-600 mb-1.5 block">Kelas / Divisi</label>
                <input type="text" wire:model="createRequestData.kelas_divisi"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                    placeholder="Contoh: U3">
                @error('createRequestData.kelas_divisi')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Nama Barang -->
            <div>
                <label class="text-sm text-gray-600 mb-1.5 block">Nama Barang</label>
                <input type="text" wire:model="createRequestData.nama_barang"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                    placeholder="Contoh: Laptop, MP3 Player">
                @error('createRequestData.nama_barang')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Jumlah Barang -->
            <div>
                <label class="text-sm text-gray-600 mb-1.5 block">Jumlah Barang</label>
                <input type="number" wire:model="createRequestData.jumlah_barang" min="1"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary">
                @error('createRequestData.jumlah_barang')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tujuan / Keperluan -->
            <div>
                <label class="text-sm text-gray-600 mb-1.5 block">Tujuan / Keperluan</label>
                <textarea wire:model="createRequestData.tujuan" rows="3"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                    placeholder="Jelaskan keperluan permohonan ini..."></textarea>
                @error('createRequestData.tujuan')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Sumber Dana -->
            <div>
                <label class="text-sm text-gray-600 mb-1.5 block">Sumber Dana</label>
                <input type="text" wire:model="createRequestData.sumber_dana"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                    placeholder="Contoh: Pribadi, Donatur">
                @error('createRequestData.sumber_dana')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Budget -->
            <div x-data="{
                rawValue: @entangle('createRequestData.budget'),
                formattedValue: '',
                init() {
                    // Format initial value jika ada data sebelumnya
                    this.formattedValue = this.formatCurrency(this.rawValue);

                    // Watch perubahan dari Livewire
                    this.$watch('rawValue', (value) => {
                        this.formattedValue = this.formatCurrency(value);
                    });
                },
                formatCurrency(value) {
                    // Bersihkan nilai non-numerik dan parse ke integer
                    const numericValue = parseInt(value.toString().replace(/[^0-9]/g, '')) || 0;

                    // Format ke Rupiah dengan separator ribuan
                    return new Intl.NumberFormat('id-ID', {
                        maximumFractionDigits: 0
                    }).format(numericValue);
                },
                handleInput(event) {
                    // Bersihkan input dan update rawValue
                    const value = event.target.value.replace(/[^0-9]/g, '');
                    this.rawValue = value === '' ? 0 : parseInt(value, 10);
                }
            }">
                <label class="text-sm text-gray-600 mb-1.5 block">Budget (Rp)</label>
                <input type="text" x-model="formattedValue" x-on:input="handleInput($event)" id="budgetInput"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                    placeholder="Contoh: 100.000" inputmode="numeric">
                @error('createRequestData.budget')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

        </div>
    </div>

    <!-- Bottom Action Button -->
    <div class="fixed bottom-0 left-1/2 -translate-x-1/2 md:max-w-screen-xl w-full max-w-[480px] bg-white p-4 z-50">
        <div class="flex gap-3">
            <a href="{{ route('profile') }}" wire:navigate
                class="flex items-center gap-1 px-3 h-12 rounded-full border border-gray-300 hover:bg-gray-100 text-gray-600">
                <i class="bi bi-person-circle text-xl"></i>
                <span class="text-sm font-medium">Profil</span>
            </a>
            <button wire:click="createRequest" wire:loading.attr="disabled" wire:target="createRequest"
                class="w-full h-12 flex items-center justify-center rounded-full bg-primary text-white font-medium hover:bg-primary/90 transition-colors"
                x-data="{ loading: false }" x-init="Livewire.hook('message.sent', () => loading = true);
                Livewire.hook('message.processed', () => loading = false)">

                <!-- Default: Simpan Permohonan -->
                <span x-show="!loading" wire:loading.remove wire:target="createRequest">Simpan Permohonan</span>

                <!-- Saat loading -->
                <span x-show="loading" wire:loading wire:target="createRequest" class="inline-flex items-center gap-2">
                    <span>Menyimpan...</span>
                </span>
            </button>

        </div>
    </div>
</div>
