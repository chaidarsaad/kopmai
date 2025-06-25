<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Models\Shop;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informasi Dasar')
                            ->collapsible()
                            ->schema([
                                Forms\Components\Select::make('shp_id')
                                    ->label('Tenant')
                                    ->relationship('shop', 'name')
                                    ->preload()
                                    ->searchable(),
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Produk')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('description')
                                    ->label('Deskirpsi Produk')
                                    ->columnSpanFull(),
                            ]),
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Harga & Stok')
                            ->collapsible()
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga Produk')
                                    ->numeric()
                                    ->prefix('Rp'),
                                Forms\Components\TextInput::make('stock')
                                    ->label('Stok Produk')
                                    ->numeric(),
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true)
                                    ->required()
                                    ->helperText('Jika tidak diaktifkan maka produk tidak akan tampil di halaman depan')
                                    ->label('Tampilkan produk?'),
                            ]),
                    ]),
                Forms\Components\Section::make('Gambar')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('image')
                            ->label('Gambar Produk')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginationPageOptions([5, 25, 50, 100, 250])
            ->defaultPaginationPageOption(5)
            ->defaultSort('id', direction: 'desc')
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->circular()
                    ->stacked()
                    ->getStateUsing(fn($record) => "https://drive.google.com/thumbnail?id={$record->image}&sz=w1000"),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shop.name')
                    ->label('Tenant')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->searchable()
                    ->label('Harga')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktifkan produk?')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('shop_id')
                    ->label('Tenant')
                    ->options(
                        Shop::whereHas('products', function ($query) {
                            $query->where('category_id', $this->ownerRecord->id);
                        })->pluck('name', 'id')
                    )
                    ->preload()
                    ->searchable()
                    ->multiple(),
                SelectFilter::make('is_active')
                    ->native(false)
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ])
                    ->label('Status Produk'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
