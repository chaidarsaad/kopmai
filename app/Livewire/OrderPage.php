<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Services\OrderStatusService;

class OrderPage extends Component
{

    public function getStatusClass($status)
    {
        return OrderStatusService::getStatusColor($status);
    }

    public function getOrders()
    {
        $orders = Order::with('items')
            ->where('user_id', auth()->id())
            ->orderBy('id', 'DESC')
            ->get();
        return $orders;
    }

    public function render()
    {
        return view('livewire.order', [
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
