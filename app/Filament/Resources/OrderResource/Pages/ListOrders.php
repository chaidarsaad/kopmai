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
        return [
            'Menunggu Pembayaran' => Tab::make()
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereNull('payment_proof')
                        ->where('status', '!=', 'completed')
                        ->where('status', '!=', 'cancelled')
                        ->where('payment_status', '!=', 'paid') // Tambahkan ini
                )
                ->badge(Order::whereNull('payment_proof')
                    ->where('status', '!=', 'completed')
                    ->where('status', '!=', 'cancelled')
                    ->where('payment_status', '!=', 'paid') // Tambahkan ini
                    ->count()),

            'Sudah Dibayar (Belum di verifikasi)' => Tab::make()
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereNotNull('payment_proof')
                        ->where('status', 'pending')
                        ->where('payment_status', '!=', 'paid')
                )
                ->badge(Order::whereNotNull('payment_proof')
                    ->where('status', 'pending')
                    ->where('payment_status', '!=', 'paid')
                    ->count()),
            'Diproses' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'processing'))
                ->badge(Order::query()->where('status', 'processing')->count()),

            'Selesai' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                ->badge(Order::query()->where('status', 'completed')->count()),
            'Dibatalkan' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled'))
                ->badge(Order::query()->where('status', 'cancelled')->count()),
            'Semua' => Tab::make()
                ->badge(Order::count()),
        ];
    }
}
