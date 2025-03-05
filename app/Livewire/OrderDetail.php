<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PaymentMethod;
use App\Models\Order;
use Carbon\Carbon;
use App\Services\OrderStatusService;
use App\Services\MidtransService;
use App\Models\Store;

class OrderDetail extends Component
{
    public $order;
    public $paymentDeadline;
    public $paymentMethods;
    public $store;
    public $shopsWithShipping = [];

    public function mount($orderNumber)
    {
        $this->order = Order::where('order_number', $orderNumber)
            ->with('items.product.shop')
            ->firstOrFail();

        $this->paymentDeadline = Carbon::parse($this->order->created_at)->addHours(24);
        $this->paymentMethods = PaymentMethod::all();
        $this->store = Store::first();

        // Ambil daftar toko yang memiliki ongkir
        foreach ($this->order->items as $item) {
            $shop = $item->product->shop;
            if ($shop && $shop->is_ongkir) {
                if (!isset($this->shopsWithShipping[$shop->id])) {
                    $this->shopsWithShipping[$shop->id] = [
                        'name' => $shop->name,
                        'ongkir' => $shop->ongkir,
                    ];
                }
            }
        }
    }

    public function getStatusInfo()
    {
        return OrderStatusService::getStatusInfo(
            $this->order->status,
            $this->paymentDeadline,
            $this->order->completed_at,
            $this->order->payment_gateway_transaction_id
        );
    }

    public function render()
    {
        $statusInfo = $this->getStatusInfo();

        $this->checkPaymentStatus();

        return view('livewire.order-detail', [
            'statusInfo' => $statusInfo,
            'order' => $this->order
        ])->layout('components.layouts.app', ['hideBottomNav' => true]);
    }

    public function checkPaymentStatus()
    {
        if ($this->order && $this->order->payment_gateway_transaction_id) {
            try {
                $midtrans = app(MidtransService::class);
                $status = $midtrans->getStatus($this->order);

                $this->order->update([
                    'payment_gateway_data' => json_encode($status['data'])
                ]);

                if ($status['success']) {
                    switch ($status['data']->transaction_status) {
                        case 'settlement':
                            $this->order->update([
                                'payment_status' => 'paid',
                                'status' => 'processing'
                            ]);
                            break;
                        case 'deny':
                            $this->order->update([
                                'status' => 'cancelled'
                            ]);
                            break;
                        case 'cancel':
                            $this->order->update([
                                'status' => 'cancelled'
                            ]);
                            break;
                        case 'expire':
                            $this->order->update([
                                'status' => 'cancelled'
                            ]);
                            break;
                    }
                }
            } catch (\Exception $e) {
                report($e);
            }
        }
    }
}
