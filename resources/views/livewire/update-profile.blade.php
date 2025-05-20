@section('title')
    Ubah Profil
@endsection

<div class="mx-auto max-w-screen-lg min-h-screen bg-white pb-[70px] md:px-10 md:pb-10 pt-0 md:pt-[72px]">
    <!-- Header -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] md:max-w-screen-lg bg-white z-50">
        <div class="relative flex items-center justify-between h-16 px-4">
            <a wire:navigate href="{{ route('profile') }}" class="hover:bg-gray-50 rounded-full">
                <i class="bi bi-chevron-left text-xl"></i>
            </a>
            <h1 class="absolute left-1/2 -translate-x-1/2 text-lg font-medium">Ubah Profil</h1>
        </div>
    </div>

    <!-- Alert -->
    <div x-data="{ show: false, message: '' }" x-show="show"
        class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-md mx-4 mt-4">
        <p x-text="message"></p>
    </div>

    <!-- Main Content -->
    <div class="pt-20 md:pt-8 pb-12 px-4 space-y-8">
        <form wire:submit.prevent="updateProfile">
            <div class="bg-white rounded-xl border border-gray-100 p-4 space-y-4">
                <!-- Nama -->
                <div>
                    <label class="text-sm text-gray-600 mb-1.5 block">Nama</label>
                    <input oninput="this.value = this.value.toLowerCase()" type="text" wire:model="name"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                        placeholder="Masukkan nama lengkap">
                    @error('name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="text-sm text-gray-600 mb-1.5 block">Email</label>
                    <input type="text" wire:model="email"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                        placeholder="Masukkan email">
                    @error('email')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Nomor HP -->
                <div>
                    <label class="text-sm text-gray-600 mb-1.5 block">Nomor HP</label>
                    <input type="tel" wire:model="phone_number"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                        placeholder="Contoh: 08123456789">
                    @error('phone_number')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="text-sm text-gray-600 mb-1.5 block">Password</label>
                    <div class="mt-1 relative">
                        <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model.lazy="password"
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                            placeholder="Masukkan password jika ingin diubah">
                        <button type="button" wire:click="togglePassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                            @if ($showPassword)
                                <i class="bi bi-eye"></i>
                            @else
                                <i class="bi bi-eye-slash"></i>
                            @endif
                        </button>
                    </div>
                    @error('password')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label class="text-sm text-gray-600 mb-1.5 block">Konfirmasi Password</label>
                    <div class="mt-1 relative">
                        <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model.lazy="password_confirmation"
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                            placeholder="Masukkan password jika ingin diubah">
                    </div>
                    @error('password_confirmation')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div
                class="fixed bottom-0 left-1/2 -translate-x-1/2 md:max-w-screen-lg w-full max-w-[480px] bg-white p-4 z-50">
                <button type="submit"
                    class="w-full h-12 flex items-center justify-center rounded-full bg-primary text-white font-medium hover:bg-primary/90 transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <script>
        Livewire.on('showAlert', data => {
            let alertBox = document.querySelector('[x-data]');
            alertBox.__x.$data.show = true;
            alertBox.__x.$data.message = data.message;
            setTimeout(() => alertBox.__x.$data.show = false, 3000);
        });
    </script>
</div>
