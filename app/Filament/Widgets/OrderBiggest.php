<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class OrderBiggest extends BaseWidget
{
    use InteractsWithPageFilters, HasWidgetShield;

    protected static ?string $heading = 'Sumber Pemasukan';
    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        if (!empty($startDate)) {
            $startDate = Carbon::parse($startDate);
        }

        if (!empty($endDate)) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        $user = auth()->user();
        $isOwnerTenant = $user->hasRole('owner_tenant');

        // Query dasar
        $query = Order::query()
            ->where('payment_status', 'paid')
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->when($startDate && !$endDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->when(!$startDate && $endDate, fn($q) => $q->where('created_at', '<=', $endDate));

        if ($isOwnerTenant) {
            // Untuk owner_tenant, filter order yang punya item dari tenant dia
            $query->whereHas('orderItems.product', function ($q) use ($user) {
                $q->where('shop_id', $user->shop_id);
            });
        }

        return $table
            ->defaultSort('total_amount', 'desc')
            ->paginationPageOptions([10, 25, 50, 100, 250])
            ->defaultPaginationPageOption(10)
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->url(function ($record) {
                        $user = auth()->user();

                        if ($user->hasRole('owner_tenant')) {
                            return route('filament.pengelola.resources.pesanan.view', ['record' => $record->getRouteKey()]);
                        }

                        if ($user->hasRole('pengelola_web')) {
                            return route('filament.pengelola.resources.pesanan.edit', ['record' => $record->getRouteKey()]);
                        }

                        return null; // Atau arahkan ke halaman default jika role lain
                    })
                    ->label('No. Pesanan'),

                Tables\Columns\TextColumn::make('student.nama_santri')
                    ->label('Nama Santri'),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Jumlah')
                    ->formatStateUsing(function ($record) use ($isOwnerTenant, $user) {
                        if ($isOwnerTenant) {
                            // Hitung subtotal berdasarkan buying_price dari produk milik tenant
                            $total = $record->orderItems()
                                ->whereHas('product', function ($q) use ($user) {
                                $q->where('shop_id', $user->shop_id);
                            })
                                ->join('products', 'order_items.product_id', '=', 'products.id')
                                ->selectRaw('SUM(products.buying_price * order_items.quantity) as total')
                                ->value('total');

                            return 'Rp ' . number_format($total ?? 0, 2, ',', '.');
                        }

                        // Untuk admin/pengelola, tetap tampilkan subtotal pesanan
                        return 'Rp ' . number_format($record->subtotal, 2, ',', '.');
                    })

            ]);
    }
}
