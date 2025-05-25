@section('title')
    Permohonan Saya
@endsection

<div class="mx-auto max-w-screen-xl min-h-screen bg-white pb-[70px] md:px-10 md:pb-10 pt-0 md:pt-[72px]">
    <!-- Header -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] md:max-w-screen-xl bg-white z-50">
        <div class="relative flex items-center justify-between h-16 px-4">
            <a wire:navigate href="{{ route('profile') }}" class="hover:bg-gray-50 rounded-full p-2">
                <i class="bi bi-chevron-left text-xl"></i>
            </a>
            <h1 class="absolute left-1/2 -translate-x-1/2 text-lg font-semibold">Permohonan Saya</h1>
            <a wire:navigate href="{{ route('buat.permohonan') }}" title="Buat Permohonan"
                class="hidden sm:flex items-center gap-1 hover:bg-gray-100 text-primary rounded-full px-3 py-1 text-sm transition">
                <i class="bi bi-plus-circle text-base"></i>
                <span class="hidden sm:inline">Buat Permohonan</span>
            </a>
        </div>
    </div>

    <!-- Alert -->
    <div x-data="{ show: false, message: '' }" x-show="show"
        class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-md mx-4 mt-4">
        <p x-text="message"></p>
    </div>

    <!-- Main Content -->
    <div class="pt-24 md:pt-8 pb-8 px-4 space-y-4">
        @forelse($requests as $request)
            <div x-data="{ open: false }" class="border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                <!-- Order Header -->
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-500 font-medium">
                            {{ $request->request_number }}
                        </span>
                        <span
                            class="{{ $this->getStatusClass($request->status) }} text-xs font-semibold px-2 py-1 rounded-full">
                            {{ $statusLabels[$request->status] }}
                        </span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <div>
                            Tanggal Permohonan:
                            <br>
                            {{ \Carbon\Carbon::parse($request->tanggal_permohonan)->locale('id')->translatedFormat('l, d F Y') }}
                        </div>
                        <div>
                            Deadline:
                            <br>
                            {{ \Carbon\Carbon::parse($request->deadline)->locale('id')->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                </div>

                <!-- Accordion Toggle -->
                <div class="px-4 pt-3">
                    <button @click="open = !open"
                        class="text-sm text-primary font-semibold hover:underline focus:outline-none">
                        <span
                            x-text="open ? 'Sembunyikan detail permohonan' : 'Klik untuk lihat detail permohonan'"></span>
                    </button>
                </div>

                <!-- Detail -->
                <div x-show="open" x-collapse>
                    <div class="p-4 text-sm text-gray-700 space-y-2">
                        <p><strong>Nama Pemesan:</strong> {{ $request->nama_pemesan }}</p>
                        <p><strong>Kelas / Divisi:</strong> {{ $request->kelas_divisi }}</p>
                        <p><strong>Nama Barang:</strong> {{ $request->nama_barang }}</p>
                        <p><strong>Jumlah Barang:</strong> {{ $request->jumlah_barang }}</p>
                        <p><strong>Tujuan:</strong> {{ $request->tujuan }}</p>
                        <p><strong>Sumber Dana:</strong> {{ $request->sumber_dana }}</p>
                        <p><strong>Budget:</strong> Rp {{ number_format($request->budget, 2, ',', '.') }}</p>

                        @if (!empty($request->alasan))
                            <p class="text-red-500"><strong>Alasan Penolakan:</strong> {{ $request->alasan }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <a wire:navigate href="{{ route('buat.permohonan') }}"
                class="fixed bottom-4 left-1/2 -translate-x-1/2 md:hidden bg-primary text-white px-4 py-2 rounded-full shadow-lg flex items-center gap-2 z-50">
                <i class="bi bi-plus-circle-fill text-lg"></i>
                <span>Buat Permohonan</span>
            </a>
        @empty
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 text-gray-300 mb-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <p class="text-xl font-medium text-gray-400 mb-1">Belum Ada Permohonan</p>
                <p class="text-sm text-gray-400 mb-4">Anda belum membuat permohonan apapun.</p>
                <a wire:navigate href="{{ route('buat.permohonan') }}"
                    class="px-6 py-2 bg-primary text-white rounded-full text-sm hover:bg-primary/90 transition-colors">
                    <i class="bi bi-plus-circle me-1"></i> Buat Permohonan
                </a>
            </div>
        @endforelse
    </div>

    <script>
        Livewire.on('showAlert', data => {
            let alertBox = document.querySelector('[x-data]');
            alertBox.__x.$data.show = true;
            alertBox.__x.$data.message = data.message;
            setTimeout(() => alertBox.__x.$data.show = false, 5000);
        });
    </script>
</div>
