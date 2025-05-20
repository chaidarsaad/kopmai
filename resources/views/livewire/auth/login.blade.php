@section('title')
    Masuk
@endsection

<div class="mx-auto bg-white min-h-screen flex items-center justify-center">
    <div class="p-6 w-full max-w-md mx-auto">
        <!-- Logo & Welcome Text -->
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center mb-2">
                <img src="{{ $store->imageUrl ?? asset('image/store.png') }}" alt="Store" class="w-[170px] h-[170px]  ">
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang</h1>
            <p class="text-gray-500">Silakan masuk untuk melanjutkan</p>
        </div>

        <!-- Login Form -->
        <form wire:submit.prevent="login" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input oninput="this.value = this.value.toLowerCase()" wire:model.lazy="name" type="text"
                    placeholder="Masukkan nama lengkap Anda"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary">
                @error('name')
                    <span class="text-red-500 text-lg mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <div class="mt-1 relative">
                        <input wire:model.lazy="password" type="{{ $showPassword ? 'text' : 'password' }}"
                            id="password"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                            placeholder="Password Anda">

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
            </div>

            <!-- <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" class="rounded border-gray-300 text-primary focus:ring-primary">
                        <label for="remember" class="ml-2 text-sm text-gray-600">Ingat saya</label>
                    </div>
                    <a href="#" class="text-sm text-primary hover:underline">Lupa password?</a>
                </div> -->

            <button type="submit"
                class="w-full bg-primary text-white py-3 rounded-xl font-medium hover:bg-primary/90 transition-colors">
                Masuk
            </button>

            <p class="text-center text-sm text-gray-600">
                Belum punya akun?
                <a wire:navigate href="{{ route('register') }}" class="text-primary hover:underline">Daftar sekarang</a>
            </p>
        </form>
    </div>
</div>
