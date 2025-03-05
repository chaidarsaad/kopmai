<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\OrderStatusService;

class Orders extends Component
{
    use WithPagination;

    public function getStatusClass($status)
    {
        return OrderStatusService::getStatusColor($status);
    }

    public function cancelOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order && $order->status === 'pending') {
            $order->update(['status' => 'cancelled']);
            session()->flash('message', 'Pesanan berhasil dibatalkan');
        }
    }

    public function getOrders()
    {
        $query = Order::with('items')
            ->where('user_id', auth()->id())
            ->latest();

        return $query->paginate(1);
    }

    public function render()
    {
        return view('livewire.orders', [
            'orders' => $this->getOrders(),
            'statusLabels' => array_combine(
                [
                    OrderStatusService::STATUS_PENDING,
                    OrderStatusService::STATUS_PROCESSING,
                    OrderStatusService::STATUS_COMPLETED,
                    OrderStatusService::STATUS_CANCELLED
                ],
                array_map(
                    fn($status) => OrderStatusService::getStatusLabel($status),
                    [
                        OrderStatusService::STATUS_PENDING,
                        OrderStatusService::STATUS_PROCESSING,
                        OrderStatusService::STATUS_COMPLETED,
                        OrderStatusService::STATUS_CANCELLED
                    ]
                )
            )
        ]);
    }
}
