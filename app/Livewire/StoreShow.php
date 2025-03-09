<?php

namespace App\Livewire;

use App\Models\Cart;
use Livewire\Component;
use App\Models\Store;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;

class StoreShow extends Component
{
    public int $on_page = 6;
    public bool $hasMoreProducts = true;
    public array $displayedProductIds = [];

    public $store;
    public $cartCount;
    public $categories;
    public $shops;
    public $selectedCategory;
    public $selectedShop = 'all';
    public $selectedFilterType = 'category';

    public $paketSantri;

    public function mount()
    {
        $this->paketSantri = Category::where('name', 'Paket Santri')->first();
        $this->store = Store::first();
        $this->cartCount = Cart::where('user_id', auth()->id())->sum('quantity');

        if ($this->paketSantri) {
            $this->selectedCategory = $this->paketSantri->id;
        } else {
            $this->selectedCategory = 'all';
        }
        if ($this->store) {
            $this->categories = Category::where('name', '!=', 'Paket Santri')->inRandomOrder()->get();
            $this->shops = Shop::where('is_active', 1)->inRandomOrder()->get();
        }
        $this->getProducts();
        $this->checkHasMoreProducts();
    }

    public function updatedSelectedCategory()
    {
        $this->resetProducts();
    }

    public function updatedSelectedShop()
    {
        $this->resetProducts();
    }

    public function resetProducts()
    {
        $this->displayedProductIds = [];
        $this->getProducts();
        $this->checkHasMoreProducts();
    }

    public function render()
    {
        if (!$this->store) {
            return view('livewire.coming-soon')
                ->layout('components.layouts.app', ['hideBottomNav' => true]);
        }

        return view('livewire.store-show', [
            'products' => collect($this->displayedProductIds)->map(fn($id) => Product::find($id))
        ]);
    }

    public function getProducts()
    {
        $query = Product::query()
            ->where('is_active', 1)
            ->whereHas('shop', function ($q) {
                $q->where('is_active', 1);
            });

        if ($this->selectedFilterType === 'category' && $this->selectedCategory !== 'all') {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->selectedFilterType === 'shop' && $this->selectedShop !== 'all') {
            $query->where('shop_id', $this->selectedShop);
        }

        if (!empty($this->displayedProductIds)) {
            $query->whereNotIn('id', $this->displayedProductIds);
        }

        $products = $query->inRandomOrder()->take(6)->get();

        $this->displayedProductIds = array_merge($this->displayedProductIds, $products->pluck('id')->toArray());
    }

    public function loadMore(): void
    {
        $this->getProducts();
        $this->checkHasMoreProducts();
    }

    public function setFilterType($type)
    {
        $this->selectedFilterType = $type;
        $this->resetProducts();
    }

    public function checkHasMoreProducts(): void
    {
        $query = Product::where('is_active', 1)
            ->whereHas('shop', function ($q) {
                $q->where('is_active', 1);
            });

        if ($this->selectedFilterType === 'category' && $this->selectedCategory !== 'all') {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->selectedFilterType === 'shop' && $this->selectedShop !== 'all') {
            $query->where('shop_id', $this->selectedShop);
        }

        // Exclude displayed products from count
        if (!empty($this->displayedProductIds)) {
            $query->whereNotIn('id', $this->displayedProductIds);
        }

        $this->hasMoreProducts = $query->count() > 0;
    }

    public function updateCartCount()
    {
        $this->cartCount = Cart::where('user_id', auth()->id())->sum('quantity');
    }

    public function addToCart($productId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        try {
            $cart = Cart::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->first();

            if ($cart) {
                $cart->update([
                    'quantity' => $cart->quantity + 1
                ]);
            } else {
                Cart::create([
                    'user_id' => auth()->id(),
                    'product_id' => $productId,
                    'quantity' => 1
                ]);
            }

            $this->updateCartCount();

            $this->dispatch('showAlert', [
                'message' => 'Berhasil ditambahkan ke keranjang',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('showAlert', [
                'message' => 'Gagal menambahkan ke keranjang' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
}
