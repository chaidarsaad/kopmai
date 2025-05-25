@section('title')
    Profil
@endsection

<div class="mx-auto max-w-screen-xl min-h-screen bg-white pb-[70px] md:px-10 md:pb-10 pt-0 md:pt-[72px]">


    <!-- Profile Content -->
    <div class="">
        <!-- Profile Header -->
        <div class="bg-gradient-to-br from-primary to-secondary p-6">
            <div class="flex items-center gap-4">
                <div class="text-white">
                    <h2 class="text-xl font-semibold">{{ $name }}</h2>
                    <p class="text-white/80">{{ $email }}</p>
                </div>
            </div>
        </div>

        <!-- Profile Menu -->
        <div class="p-4 space-y-4">
            <!-- Account Settings -->
            <div class="space-y-2">
                <h3 class="text-sm font-medium text-gray-500">Akun</h3>
                <div class="space-y-1">
                    <a href="{{ route('profile.update') }}" wire:navigate
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-person-circle text-primary"></i>
                            <span>Ubah Profil</span>
                        </div>
                        <i class="bi bi-chevron-right text-gray-400"></i>
                    </a>

                </div>
            </div>

            <div class="space-y-2">
                <div class="space-y-1">
                    <a href="{{ route('permohonan') }}" wire:navigate
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-box-seam text-primary"></i>
                            <span>Permohonan Saya</span>
                        </div>
                        <i class="bi bi-chevron-right text-gray-400"></i>
                    </a>

                </div>
            </div>

            <div class="space-y-2">
                <div class="space-y-1">
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank"
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-question-circle text-primary"></i>
                            <span>Hubungi Admin via WhatsApp</span>
                        </div>
                        <i class="bi bi-chevron-right text-gray-400"></i>
                    </a>

                </div>
            </div>

            @if ($is_pengelola)
                <div class="space-y-2">
                    <h3 class="text-sm font-medium text-gray-500">Pengelola</h3>
                    <div class="space-y-1">
                        <a href="{{ route('filament.pengelola.pages.dashboard') }}"
                            class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100">
                            <div class="flex items-center gap-3">
                                <i class="bi bi-person-circle text-primary"></i>
                                <span>Halaman Pengelola</span>
                            </div>
                            <i class="bi bi-chevron-right text-gray-400"></i>
                        </a>

                    </div>
                </div>
            @endif

            <!-- Logout Button -->
            <button wire:click="logout" wire:loading.attr="disabled" wire:target="logout"
                class="w-full mt-6 p-4 text-red-500 flex items-center justify-center gap-2 bg-red-50 rounded-xl hover:bg-red-100"
                x-data="{ loading: false }" x-init="Livewire.hook('message.sent', () => loading = true);
                Livewire.hook('message.processed', () => loading = false)">

                <span x-show="!loading" wire:loading.remove wire:target="logout"> <i class="bi bi-box-arrow-right"></i>
                    Keluar</span>

                <span x-show="loading" wire:loading wire:target="logout" class="inline-flex items-center gap-2">
                    <div class="w-6 h-6 border-4 border-t-primary border-gray-200 rounded-full animate-spin">
                    </div>
                </span>
            </button>
        </div>
    </div>
</div>
