<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters, HasWidgetShield;
    protected static ?int $sort = 0;
    protected function getStats(): array
    {
        $user = Auth::user();

        // Jika role adalah owner_tenant
        if ($user->hasRole('owner_tenant')) {
            $productCount = Product::where('shop_id', $user->shop_id)->count();

            return [
                Stat::make('Total Produk Saya', $productCount)
                    ->description('Jumlah Produk Tenant Anda'),
            ];
        }

        // Jika role selain owner_tenant (misalnya admin)
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        if (!empty($startDate)) {
            $startDate = Carbon::parse($startDate);
        }

        if (!empty($endDate)) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        $orderQuery = Order::where('payment_status', 'paid');
        if ($startDate && $endDate) {
            $orderQuery->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $orderQuery->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $orderQuery->where('created_at', '<=', $endDate);
        }

        $expense = OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
            $query->where('payment_status', 'paid');

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->where('created_at', '>=', $startDate);
            } elseif ($endDate) {
                $query->where('created_at', '<=', $endDate);
            }
        })->join('products', 'order_items.product_id', '=', 'products.id')
            ->sum(DB::raw('order_items.quantity * products.modal'));

        $order_count = $orderQuery->count();
        $omset = $orderQuery->sum('subtotal');
        $laba = $omset - $expense;

        return [
            Stat::make('Total Admin', User::where('is_admin', 1)->count())
                ->description('Jumlah Akun Admin'),
            Stat::make('Total Wali', User::where('is_admin', 0)->count())
                ->description('Jumlah Wali yang daftar'),
            Stat::make('Total Tenant', Shop::count()),
            Stat::make('Total Produk', Product::count()),
            Stat::make('Total Pesanan', $order_count),
            Stat::make('Total Omset', 'Rp ' . number_format($omset, 0, ",", ".")),
            Stat::make('Total Modal', 'Rp ' . number_format($expense, 0, ",", ".")),
            Stat::make('Total Laba', 'Rp ' . number_format($laba, 0, ",", ".")),
        ];
    }
}
