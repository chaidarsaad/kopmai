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
                'product_id',
                'product_name',
                DB::raw('COUNT(DISTINCT order_id) as total_orders'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(price * quantity) as total_revenue')
            ])
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
            ->when($user->hasRole('owner_tenant'), function ($query) use ($user) {
                $query->whereHas('product', function ($q) use ($user) {
                    $q->where('shop_id', $user->shop_id);
                });
            })
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_quantity');

        return $table
            ->paginationPageOptions([10, 25, 50, 100, 250])
            ->defaultPaginationPageOption(10)
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->limit(30)
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
