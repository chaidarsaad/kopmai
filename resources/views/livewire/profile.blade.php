@section('title')
    Profil
@endsection

<div class="max-w-[480px] mx-auto bg-white min-h-screen relative shadow-lg">
    <!-- Header -->
    {{-- <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white z-50">
        <div class="flex items-center h-16 px-4 border-b border-gray-100">
            <button onclick="history.back()" class="hover:bg-gray-50 rounded-full">
                <i class="bi bi-arrow-left text-xl"></i>
            </button>
            <h1 class="ml-2 text-lg font-medium">Profil Saya</h1>
        </div>
    </div> --}}

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

            @if ($is_admin === 1)
                <div class="space-y-2">
                    <h3 class="text-sm font-medium text-gray-500">Admin</h3>
                    <div class="space-y-1">
                        <a href="{{ route('filament.admin.pages.dashboard') }}""
                            class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100">
                            <div class="flex items-center gap-3">
                                <i class="bi bi-person-circle text-primary"></i>
                                <span>Halaman Admin</span>
                            </div>
                            <i class="bi bi-chevron-right text-gray-400"></i>
                        </a>

                    </div>
                </div>
            @endif


            <!-- Logout Button -->
            <button wire:click="logout"
                class="w-full mt-6 p-4 text-red-500 flex items-center justify-center gap-2 bg-red-50 rounded-xl hover:bg-red-100">
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar</span>
            </button>
        </div>
    </div>
</div>
