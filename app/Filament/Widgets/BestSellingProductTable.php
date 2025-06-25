<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class BestSellingProductTable extends BaseWidget
{
    use InteractsWithPageFilters, HasWidgetShield;
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Produk Terjual';

    public function table(Table $table): Table
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        $user = auth()->user();

        $query = OrderItem::query()
            ->select([
                'order_items.product_id',
                'order_items.product_name',
                DB::raw('COUNT(DISTINCT order_items.order_id) as total_orders'),
                DB::raw('SUM(order_items.quantity) as total_quantity'),
            ])
            ->when($user->hasRole('owner_tenant'), function ($query) use ($user) {
                // Join ke products untuk ambil buying_price
                $query->join('products', 'order_items.product_id', '=', 'products.id')
                    ->addSelect(DB::raw('SUM(products.buying_price * order_items.quantity) as total_revenue'))
                    ->where('products.shop_id', $user->shop_id);
            }, function ($query) {
                // Untuk non-tenant, tetap pakai price * quantity
                $query->addSelect(DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'));
            })
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->where('payment_status', 'paid');

                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                } elseif ($endDate) {
                    $query->where('created_at', '<=', $endDate);
                }
            })
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('total_quantity');


        return $table
            ->paginationPageOptions([10, 25, 50, 100, 250])
            ->defaultPaginationPageOption(10)
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->limit(30)
                    ->url(function ($record) {
                        $product = \App\Models\Product::find($record->product_id);
                        return $product
                            ? route('filament.pengelola.resources.produk.edit', ['record' => $product->slug])
                            : null;
                    })
                    ->label('Nama Produk'),
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Total Terjual'),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Total Pendapatan')
                    ->money('IDR'),
            ]);
    }


    public function getTableRecordKey(Model $record): string
    {
        return (string) $record->product_id;
    }
}
