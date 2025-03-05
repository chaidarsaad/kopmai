<?php

namespace App\Observers;

use App\Models\OrderItem;
use App\Models\Product;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "created" event.
     */
    public function created(OrderItem $orderItem): void
    {
        $product = Product::find($orderItem->product_id);

        if ($product) {
            if (!is_null($product->stock)) {
                $product->decrement('stock', $orderItem->quantity);

                if ($product->fresh()->stock <= 0) {
                    $product->update(['is_active' => false]);
                }
            }
        }
    }

    /**
     * Handle the OrderItem "deleted" event.
     */
    public function deleted(OrderItem $orderItem): void
    {
        $product = Product::find($orderItem->product_id);

        if ($product) {
            if (!is_null($product->stock)) {
                $product->increment('stock', $orderItem->quantity);

                if (!$product->is_active && $product->fresh()->stock > 0) {
                    $product->update(['is_active' => true]);
                }
            }
        }
    }
}
