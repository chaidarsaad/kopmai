@section('title')
    Daftar
@endsection

<div class="mx-auto bg-white min-h-screen flex items-center justify-center">
    <div class="p-6 w-full max-w-md mx-auto">
        <!-- Logo & Welcome Text -->
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center mb-2">
                <img src="{{ $store->imageUrl ?? asset('image/store.png') }}" alt="Store" class="w-[170px] h-[170px]  ">
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang</h1>
            <p class="text-gray-500">Silakan daftar untuk melanjutkan</p>
        </div>

        <!-- Login Form -->
        <form wire:submit.prevent="register" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <div class="mt-1">
                    <input oninput="this.value = this.value.toLowerCase()" wire:model.lazy="name" type="text"
                        placeholder="Masukkan nama lengkap Anda"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                @error('name')
                    <span class="text-red-500 text-lg mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="mt-1">
                    <input type="email" wire:model.lazy="email" placeholder="Masukkan email anda"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                @error('email')
                    <span class="text-red-500 text-lg mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="mt-1 relative">
                    <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model.lazy="password"
                        placeholder="Masukkan password"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary">
                    <!-- Password Toggle Button -->
                    <button type="button" wire:click="togglePassword"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                        @if ($showPassword)
                            <i id="eyeIconPC" class="bi bi-eye"></i>
                        @else
                            <i id="eyeIconPC" class="bi bi-eye-slash"></i>
                        @endif
                    </button>
                </div>
                @error('password')
                    <span class="text-red-500 text-lg mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <div class="mt-1 relative">
                    <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model.lazy="password_confirmation"
                        placeholder="Masukkan konfirmasi password"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary">

                </div>
                @error('password_confirmation')
                    <span class="text-red-500 text-lg mt-1">{{ $message }}</span>
                @enderror
            </div>

            <button wire:loading.attr="disabled" type="submit" wire:target="register"
                class="w-full bg-primary text-white py-3 rounded-xl font-medium hover:bg-primary/90 transition-colors"
                x-data="{ loading: false }" x-init="Livewire.hook('message.sent', () => loading = true);
                Livewire.hook('message.processed', () => loading = false)">

                <span x-show="!loading" wire:loading.remove wire:target="register">Daftar</span>
                <span x-show="loading" wire:loading wire:target="register" class="inline-flex items-center gap-2">
                    <div class="w-4 h-4 border-4 border-t-primary border-gray-200 rounded-full animate-spin"></div>
                </span>

            </button>

            <p class="text-center text-sm text-gray-600">
                Sudah punya akun?
                <a wire:navigate wire:navigate href="{{ route('login') }}" class="text-primary hover:underline">Masuk
                    sekarang</a>
            </p>
            <p class="text-center text-sm text-gray-600 mt-2">
                <a href="{{ route('home') }}" wire:navigate class="text-primary hover:underline">← Kembali ke
                    Beranda</a>
            </p>
        </form>
    </div>
</div>
