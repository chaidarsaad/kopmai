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
                Forms\Components\Select::make('product_id')
                    ->native(false)
                    ->searchable()
                    ->label('Produk')
                    ->required()
                    ->options(Product::query()->pluck('name', 'id'))
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $product = Product::with('shop')->find($state);
                        if ($product) {
                            $set('price', $product->price ?? 0);
                            $set('product_name', $product->name ?? '');
                            $set('shop_id', $product->shop_id ?? null);
                            $set('is_ongkir', $product->shop->is_ongkir ?? false);
                            $set('shipping_cost', $product->shop->ongkir ?? 0);
                        }
                        self::updateTotalPrice($get, $set);
                    })
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->preload(),
                Forms\Components\Hidden::make('product_name')
                    ->dehydrated()
                    ->label('Nama Produk'),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->label('Jumlah')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->afterStateUpdated(fn($state, Forms\Set $set, Forms\Get $get) => self::updateTotalPrice($get, $set)),

                Forms\Components\TextInput::make('price')
                    ->label('Harga')
                    ->readOnly()
                    ->numeric()
                    ->mask(
                        RawJs::make(<<<'JS'
                                    $input => {
                                        let number = $input.replace(/[^\d]/g, '');
                                        if (number === '') return '0';
                                        return new Intl.NumberFormat('id-ID').format(Number(number));
                                    }
                                JS)
                    )
                    ->stripCharacters([',', '.'])
                    ->numeric()
                    ->prefix('Rp'),

                Forms\Components\TextInput::make('shipping_cost')
                    ->label('Ongkir')
                    ->prefix('Rp')
                    ->numeric()
                    ->hidden(fn(Forms\Get $get) => !$get('is_ongkir'))
                    ->afterStateUpdated(fn($state, Forms\Set $set, Forms\Get $get) => self::updateTotalPrice($get, $set)),
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
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
