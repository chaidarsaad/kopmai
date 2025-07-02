<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class ProductDetail extends Component
{
    public $product;
    public $images = [];
    public $currentImageIndex = 0;
    public $cartCount = 0;

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)->firstOrFail();

        if (!empty($this->product->images)) {
            $this->images = collect($this->product->images)->reverse()->values()->all();
            $this->currentImageIndex = 0;
        }

        if (Auth::check()) {
            $this->updateCartCount();
        } else {
            $this->cartCount = 0;
        }
    }


    public function updateCartCount()
    {
        if (Auth::check()) {
            $this->cartCount = Cart::where('user_id', Auth::id())
                ->whereHas('product', function ($query) {
                    $query->where('is_active', 1);
                })
                ->with([
                    'product' => function ($query) {
                        $query->where('is_active', 1);
                    }
                ])
                ->whereHas('product.shop', function ($q) {
                    $q->where('is_active', 1);
                })
                ->sum('quantity');
        } else {
            $this->cartCount = 0;
        }
    }


    public function addToCart($productId)
    {
        if (!auth()->check()) {
            $this->redirectRoute('login', navigate: true);
            return;
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

    public function nextImage()
    {
        if ($this->currentImageIndex < count($this->images) - 1) {
            $this->currentImageIndex++;
        }
    }

    public function previousImage()
    {
        if ($this->currentImageIndex > 0) {
            $this->currentImageIndex--;
        }
    }


    public function render()
    {
        return view('livewire.product-detail', [
            'images' => $this->images,
            'currentImage' => $this->images[$this->currentImageIndex] ?? null
        ])->layout('components.layouts.app', ['hideBottomNav' => true]);
    }

}
