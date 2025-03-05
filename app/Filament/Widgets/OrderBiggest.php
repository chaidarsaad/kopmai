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
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Sumber Pemasukan';
    protected static ?int $sort = 1;
    public function table(Table $table): Table
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        if (!empty($this->filters['startDate'])) {
            $startDate = Carbon::parse($this->filters['startDate']);
        }

        if (!empty($this->filters['endDate'])) {
            $endDate = Carbon::parse($this->filters['endDate'])->endOfDay();
        }

        $query = Order::query()->where('payment_status', 'paid')->orderBy('subtotal', 'desc');
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $table
        ->paginationPageOptions([10, 25, 50, 100, 250])
        ->defaultPaginationPageOption(10)
        ->query($query)
        ->columns([
            Tables\Columns\TextColumn::make('order_number')
                ->label('No. Pesanan'),
            Tables\Columns\TextColumn::make('recipient_name')
                ->label('Nama Wali'),
            Tables\Columns\TextColumn::make('subtotal')
                ->label('Jumlah')
                ->money('IDR'),
        ]);
    }
}
