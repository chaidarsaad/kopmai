<?php

namespace App\Livewire;

use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Cart;
use App\Models\Store;
use App\Models\Order;
use App\Models\User;
use App\Services\BiteshipService;
use App\Notifications\NewOrderNotification;
// use Illuminate\Support\Facades\Notification;
use App\Services\MidtransService;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class Checkout extends Component
{
    public $carts = [];
    public $total = 0;
    public $shippingCost = 0;
    public $store;
    public $kelasList = [];
    public $totalItems = 0;
    public $shopsWithShipping = [];

    protected $midtrans;

    public $shippingData = [
        'recipient_name' => '',
        'nama_santri' => '',
        'classroom_id' => '',
        'phone' => '',
        'notes' => ''
    ];

    public $rules = [
        'shippingData.nama_santri' => 'required|min:3',
        'shippingData.classroom_id' => 'required',
        'shippingData.recipient_name' => 'required|min:3',
        'shippingData.phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
    ];

    public $messages = [
        'shippingData.recipient_name.required' => 'Nama penerima wajib diisi.',
        'shippingData.classroom_id.required' => 'Kelas santri wajib diisi.',
        'shippingData.nama_santri.required' => 'Nama santri wajib diisi.',
        'shippingData.nama_santri.min' => 'Nama santri minimal 3 karakter.',
        'shippingData.recipient_name.min' => 'Nama penerima minimal 3 karakter.',
        'shippingData.phone.required' => 'Nomor telepon wajib diisi.',
        'shippingData.phone.regex' => 'Format nomor telepon tidak valid.',
        'shippingData.phone.min' => 'Nomor telepon minimal 10 digit.',
    ];


    public function boot(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    public function mount()
    {
        $this->shippingData['phone'] = Auth::user()->phone_number ?? '';
        $this->shippingData['nama_santri'] = Auth::user()->nama_santri ?? '';

        $this->loadCarts();
        if ($this->carts->isEmpty()) {
            return redirect()->route('home');
        }
        $this->store = Store::first();
        $this->kelasList = Classroom::orderBy('id', 'asc')->pluck('name', 'id');

        if (auth()->check()) {
            $user = auth()->user();
            $this->shippingData['recipient_name'] = $user->name;
        }
    }

    public function loadCarts()
    {
        $this->carts = Cart::where('user_id', auth()->id())
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
            })->get();

        $this->calculateTotal();
    }


    public function calculateTotal()
    {
        $this->total = 0;
        $this->totalItems = 0;
        $this->shippingCost = 0;
        $this->shopsWithShipping = []; // Menyimpan daftar toko dengan ongkir

        foreach ($this->carts as $cart) {
            $this->total += $cart->product->price * $cart->quantity;
            $this->totalItems += $cart->quantity;

            $shop = $cart->product->shop;
            if ($shop && $shop->is_ongkir) {
                if (!isset($this->shopsWithShipping[$shop->id])) {
                    $this->shopsWithShipping[$shop->id] = [
                        'name' => $shop->name,
                        'ongkir' => $shop->ongkir,
                    ];
                }
            }
        }

        // Total ongkir dihitung dari semua shop yang memiliki ongkir aktif
        $this->shippingCost = array_sum(array_column($this->shopsWithShipping, 'ongkir'));
    }



    public function render()
    {
        if ($this->carts->isEmpty()) {
            return redirect()->route('home');
        }
        return view('livewire.checkout')
            ->layout('components.layouts.app', ['hideBottomNav' => true]);
    }

    public function createOrder()
    {
        $this->validate();
        if (!$this->carts->isEmpty()) {
            try {
                $user = Auth::user();
                $inputPhone = $this->shippingData['phone'];
                $inputNamaSantri = $this->shippingData['nama_santri'];

                if (empty($user->phone_number) || $user->phone_number !== $inputPhone) {
                    $user->update(['phone_number' => $inputPhone]);
                }

                if (empty($user->nama_santri) || $user->nama_santri !== $inputNamaSantri) {
                    $user->update(['nama_santri' => $inputNamaSantri]);
                }

                $this->shippingData['recipient_name'] = Auth::user()->name;
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'subtotal' => $this->total,
                    'total_amount' => $this->total + $this->shippingCost,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'nama_santri' => $this->shippingData['nama_santri'],
                    'classroom_id' => $this->shippingData['classroom_id'],
                    'recipient_name' => $this->shippingData['recipient_name'],
                    'phone' => $this->shippingData['phone'],
                    'notes' => $this->shippingData['notes']
                ]);

                foreach ($this->carts as $cart) {
                    $order->items()->create([
                        'product_id' => $cart->product_id,
                        'product_name' => $cart->product->name,
                        'quantity' => $cart->quantity,
                        'price' => $cart->product->price
                    ]);
                }

                Cart::where('user_id', auth()->id())->delete();

                // Notification::route('mail', $this->store->email_notification)
                //     ->notify(new NewOrderNotification($order));

                if ($this->store->is_use_payment_gateway) {
                    $result = $this->midtrans->createTransaction($order, $order->items);

                    if (!$result['success']) {
                        throw new \Exception($result['message']);
                    }

                    $order->update(['payment_gateway_transaction_id' => $result['token']]);

                    $this->dispatch('payment-success', [
                        'payment_gateway_transaction_id' => $result['token'],
                        'order_id' => $order->order_number
                    ]);
                } else {
                    $admin = User::role(['owner_tenant', 'pengelola_web'])->get();
                    $title = "Ada pesanan baru dari wali santri: {$order->user->name}";
                    $body = "Untuk santri: {$order->nama_santri}";

                    // Notification::make()
                    //     ->title($title)
                    //     ->body($body)
                    //     ->actions([
                    //         Action::make('view')
                    //             ->label('Lihat')
                    //             ->url(fn() => route('filament.pengelola.resources.pesanan.index'))
                    //             ->button()
                    //             ->markAsRead(),
                    //     ])
                    //     ->sendToDatabase($admin);

                    // return redirect()->route('order-detail', ['orderNumber' => $order->order_number]);
                    $this->redirectRoute('order-detail', ['orderNumber' => $order->order_number], navigate: true);
                }
            } catch (\Exception $e) {
                $this->dispatch('showAlert', [
                    'message' => $e->getMessage(),
                    'type' => 'error'
                ]);
            }
        } else {
            $this->dispatch('showAlert', [
                'message' => 'Keranjang belanja kosong',
                'type' => 'error'
            ]);
        }
    }
}
