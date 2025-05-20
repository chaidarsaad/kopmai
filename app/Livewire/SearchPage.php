<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class SearchPage extends Component
{
    use WithPagination;
    public int $on_page = 8;
    public bool $hasMoreProducts = true;
    public array $displayedProductIds = [];
    public $cartCount;
    public $search = '';

    public function updateCartCount()
    {
        $this->cartCount = Cart::where('user_id', auth()->id())->sum('quantity');
    }

    public function mount()
    {
        $this->cartCount = Cart::where('user_id', auth()->id())->sum('quantity');
        $this->getProducts();
        $this->checkHasMoreProducts();
    }

    public function addToCart($productId)
    {
        if (!auth()->check()) {
            $this->redirectRoute('login', navigate: true);
        }

        try {
            $cart = Cart::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->first();

            if ($cart) {
                $cart->update(['quantity' => $cart->quantity + 1]);
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

    public function updatingSearch()
    {
        $this->resetProducts();
    }

    public function resetProducts()
    {
        $this->displayedProductIds = [];
        $this->on_page = 8;
        $this->getProducts();
        $this->checkHasMoreProducts();
    }

    public function render()
    {
        return view('livewire.search-page', [
            'products' => collect($this->displayedProductIds)->map(fn($id) => Product::find($id))
        ])->layout('components.layouts.app', ['hideBottomNav' => true]);
    }

    public function getProducts()
    {
        $searchTerm = strtolower($this->search);

        $query = Product::whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%'])
            ->where('is_active', 1)
            ->whereHas('shop', function ($q) {
                $q->where('is_active', 1);
            });

        if (!empty($this->displayedProductIds)) {
            $query->whereNotIn('id', $this->displayedProductIds);
        }

        $products = $query->inRandomOrder()->take(8)->get();

        $this->displayedProductIds = array_merge(
            $this->displayedProductIds,
            $products->pluck('id')->toArray()
        );
    }

    public function loadMore(): void
    {
        $this->getProducts();
        $this->checkHasMoreProducts();
    }

    public function checkHasMoreProducts(): void
    {
        $searchTerm = strtolower($this->search);

        $query = Product::whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%'])
            ->where('is_active', 1)
            ->whereHas('shop', function ($q) {
                $q->where('is_active', 1);
            });

        if (!empty($this->displayedProductIds)) {
            $query->whereNotIn('id', $this->displayedProductIds);
        }

        $this->hasMoreProducts = $query->count() > 0;
    }
}
