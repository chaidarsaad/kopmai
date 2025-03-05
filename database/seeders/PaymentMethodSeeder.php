<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Bank Transfer BCA',
                'image' => 'payment-methods/bca.png',
                'account_number' => '1234567890',
                'account_name' => 'PT Online Shop'
            ],
            [
                'name' => 'Bank Transfer Mandiri',
                'image' => 'payment-methods/mandiri.png',
                'account_number' => '0987654321',
                'account_name' => 'PT Online Shop'
            ]
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}
