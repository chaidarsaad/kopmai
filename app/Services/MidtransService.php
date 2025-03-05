<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = false;
    }

    public function createTransaction($order, $items)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->recipient_name,
                'email' => auth()->user()->email,
                'phone' => $order->phone,
                'shipping_address' => [
                    'first_name' => $order->recipient_name,
                    'phone' => $order->phone,
                    'address' => $order->address_detail,
                    'city' => $order->city,
                    'postal_code' => '',
                    'country_code' => 'IDN'
                ]
            ],
            'item_details' => array_merge(
                $items->map(function ($item) {
                    return [
                        'id' => $item->product_id,
                        'price' => (int) $item->price,
                        'quantity' => $item->quantity,
                        'name' => substr($item->product_name, 0, 50)
                    ];
                })->toArray(),
                [[
                    'id' => 'SHIPPING',
                    'price' => (int) $order->shipping_cost,
                    'quantity' => 1,
                    'name' => substr($order->shipping_service . '-' . $order->shipping_description, 0, 50)
                ]]
            )
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return [
                'success' => true,
                'token' => $snapToken,
                'redirect_url' => "https://" .
                    (Config::$isProduction ? "app.midtrans.com" : "app.sandbox.midtrans.com") .
                    "/snap/v4/vtweb" . $snapToken
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getStatus($order)
    {
        try {
            $status = Transaction::status($order->order_number);
            return [
                'success' => true,
                'message' => 'Success get transaction status',
                'data' => $status
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }
}
