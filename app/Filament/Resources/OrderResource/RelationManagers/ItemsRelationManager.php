<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();

                // Jika user adalah owner tenant, hanya tampilkan produk dari tenantnya
                if ($user->hasRole('owner_tenant')) {
                    $query->whereHas('product', function ($productQuery) use ($user) {
                        $productQuery->where('shop_id', $user->shop_id);
                    });
                }

                return $query;
            })
            ->recordTitleAttribute('product_name')
            ->columns([
                Tables\Columns\ToggleColumn::make('is_received')
                    ->label('Terima Produk')
                    ->onColor('success')
                    ->offColor('danger')
                    ->sortable()
                    ->disabled(fn() => Auth::user()->hasRole('owner_tenant')),
                Tables\Columns\ImageColumn::make('product.image_url')
                    ->label('Gambar Produk'),
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Nama Produk'),
                Tables\Columns\TextColumn::make('product.shop.name')
                    ->label('Tenant'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah'),
            ])
            ->filters([])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    protected static function updateTotalPrice(Forms\Get $get, Forms\Set $set): void
    {
        $selectedProducts = collect($get('orderProducts'))->filter(fn($item) => !empty($item['product_id']) && !empty($item['quantity']));
        $products = Product::with('shop')->find($selectedProducts->pluck('product_id'));

        $prices = Product::find($selectedProducts->pluck('product_id'))->pluck('price', 'id');
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {
            return $subtotal + ($prices[$product['product_id']] * $product['quantity']);
        }, 0);

        $shippingCost = $products->unique('shop_id')->sum(fn($product) => $product->shop->ongkir ?? 0);

        $totalAmount = $subtotal + $shippingCost;

        $set('subtotal', $subtotal);
        $set('total_amount', $totalAmount);
    }
}
