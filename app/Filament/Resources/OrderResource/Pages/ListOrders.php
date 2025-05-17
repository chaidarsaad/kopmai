<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use App\Models\Order;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Pesanan')
                ->modalHeading('Buat Pesanan'),
        ];
    }

    public function getTabs(): array
    {
        $user = auth()->user(); // Ambil user yang sedang login

        return [
            'Menunggu Pembayaran' => Tab::make()
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereNull('payment_proof')
                        ->where('status', '!=', 'completed')
                        ->where('status', '!=', 'cancelled')
                        ->where('payment_status', '!=', 'paid')
                )
                ->badge(function () use ($user) {
                    $query = Order::query();
                    if ($user->hasRole('owner_tenant')) {
                        $query->whereHas('orderItems.product', function ($q) use ($user) {
                            $q->where('shop_id', $user->shop_id); // Filter pesanan berdasarkan produk yang milik tenant
                        });
                    }

                    return $query->whereNull('payment_proof')
                        ->where('status', '!=', 'completed')
                        ->where('status', '!=', 'cancelled')
                        ->where('payment_status', '!=', 'paid')
                        ->count();
                }),

            'Sudah Dibayar (Belum di verifikasi)' => Tab::make()
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereNotNull('payment_proof')
                        ->where('status', 'pending')
                        ->where('payment_status', '!=', 'paid')
                )
                ->badge(function () use ($user) {
                    $query = Order::query();
                    if ($user->hasRole('owner_tenant')) {
                        $query->whereHas('orderItems.product', function ($q) use ($user) {
                            $q->where('shop_id', $user->shop_id); // Filter pesanan berdasarkan produk yang milik tenant
                        });
                    }

                    return $query->whereNotNull('payment_proof')
                        ->where('status', 'pending')
                        ->where('payment_status', '!=', 'paid')
                        ->count();
                }),

            'Diproses' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'processing'))
                ->badge(function () use ($user) {
                    $query = Order::query();
                    if ($user->hasRole('owner_tenant')) {
                        $query->whereHas('orderItems.product', function ($q) use ($user) {
                            $q->where('shop_id', $user->shop_id); // Filter pesanan berdasarkan produk yang milik tenant
                        });
                    }

                    return $query->where('status', 'processing')->count();
                }),

            'Selesai' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                ->badge(function () use ($user) {
                    $query = Order::query();
                    if ($user->hasRole('owner_tenant')) {
                        $query->whereHas('orderItems.product', function ($q) use ($user) {
                            $q->where('shop_id', $user->shop_id); // Filter pesanan berdasarkan produk yang milik tenant
                        });
                    }

                    return $query->where('status', 'completed')->count();
                }),

            'Dibatalkan' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled'))
                ->badge(function () use ($user) {
                    $query = Order::query();
                    if ($user->hasRole('owner_tenant')) {
                        $query->whereHas('orderItems.product', function ($q) use ($user) {
                            $q->where('shop_id', $user->shop_id); // Filter pesanan berdasarkan produk yang milik tenant
                        });
                    }

                    return $query->where('status', 'cancelled')->count();
                }),

            'Semua' => Tab::make()
                ->badge(function () use ($user) {
                    $query = Order::query();
                    if ($user->hasRole('owner_tenant')) {
                        $query->whereHas('orderItems.product', function ($q) use ($user) {
                            $q->where('shop_id', $user->shop_id); // Filter pesanan berdasarkan produk yang milik tenant
                        });
                    }

                    return $query->count(); // Total count dari semua pesanan
                }),
        ];
    }
}
