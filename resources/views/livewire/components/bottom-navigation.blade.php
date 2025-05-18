 <!-- Bottom Navigation -->
 <nav x-data="{ lastScrollTop: 0, hide: false }" x-init="window.addEventListener('scroll', () => {
     let st = window.pageYOffset || document.documentElement.scrollTop;
     hide = st > lastScrollTop && st > 50; // sembunyi jika scroll ke bawah dan lebih dari 50px
     if (st < lastScrollTop) hide = false; // muncul saat scroll ke atas
     lastScrollTop = st <= 0 ? 0 : st;
 })" x-show="!hide" x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-full"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-full"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white border-t border-gray-200 h-[70px] z-50">

     <div class="grid grid-cols-4 h-full">
         <a wire:navigate href="{{ route('home') }}" wire:click="setActiveMenu('home')"
             class="flex flex-col items-center justify-center {{ $activeMenu === 'home' ? 'text-primary' : 'text-gray-500 hover:text-primary' }}">
             <i class="bi bi-house text-2xl mb-0.5"></i>
             <span class="text-xs">Beranda</span>
         </a>
         <a wire:navigate href="{{ route('shopping-cart') }}" wire:click="setActiveMenu('shopping-cart')"
             class="flex flex-col items-center justify-center {{ $activeMenu === 'shopping-cart' ? 'text-primary' : 'text-gray-500 hover:text-primary' }}  transition-colors">
             <i class="bi bi-bag text-2xl mb-0.5"></i>
             <span class="text-xs">Keranjang</span>
         </a>
         <a wire:navigate href="{{ route('orders') }}" wire:click="setActiveMenu('orders')"
             class="flex flex-col items-center justify-center {{ $activeMenu === 'orders' ? 'text-primary' : 'text-gray-500 hover:text-primary' }} transition-colors">
             <i class="bi bi-receipt text-2xl mb-0.5"></i>
             <span class="text-xs">Pesanan</span>
         </a>
         <a wire:navigate href="{{ route('profile') }}" wire:click="setActiveMenu('profile')"
             class="flex flex-col items-center justify-center {{ $activeMenu === 'profile' ? 'text-primary' : 'text-gray-500 hover:text-primary' }} transition-colors">
             <i class="bi bi-person text-2xl mb-0.5"></i>
             <span class="text-xs">Akun</span>
         </a>
     </div>
 </nav>
