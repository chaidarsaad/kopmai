<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopResource\Pages;
use App\Filament\Resources\ShopResource\RelationManagers;
use App\Models\Shop;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShopResource extends Resource
{
    protected static ?string $model = Shop::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Produk';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationLabel = 'Tenant';
    protected static ?string $pluralLabel = 'Tenant';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Tenant')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->helperText('Jika tidak diaktifkan maka produk dari tenant ini tidak akan tampil di halaman depan')
                            ->label('Aktifkan tenant?'),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Tenant')
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('No WhatsApp Tenant')
                            ->prefix('62')
                            ->helperText('Mohon masukan nomor tanpa angka 0 diawal. Contoh 812345678900')
                            ->placeholder('812345678900')
                            ->numeric()
                            ->dehydrateStateUsing(fn($state) => '62' . ltrim($state, '62'))
                            ->formatStateUsing(fn($state) => ltrim($state, '62'))
                            ->validationAttribute('Nomor WhatsApp')
                            ->default(null),
                        Forms\Components\TextInput::make('username_telegram')
                            ->label('Username Telegram')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('acc_bank')
                            ->label('Informasi Bank')
                            ->placeholder('Contoh: 03124321 Bank BCA A/N Nugroho, 1323213 Bank BRI A/N Fatmawati')
                            ->helperText('Jika terdapat 2 Bank, pisahkan dengan koma')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->label('Alamat Tenant')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\Toggle::make('is_ongkir')
                            ->label('Aktifkan Ongkir untuk tenant ini?')
                            ->default(false)
                            ->live(),
                        Forms\Components\TextInput::make('ongkir')
                            ->label('Ongkir Tenant')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(null)
                            ->visible(fn($get) => $get('is_ongkir')),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginationPageOptions([5, 25, 50, 100, 250])
            ->defaultPaginationPageOption(5)
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktifkan tenant?')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Tenant')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('No WhatsApp Tenant')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat Tenant')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->native(false)
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ])
                    ->label('Status Tenant'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Ubah Tenant'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShops::route('/'),
            'create' => Pages\CreateShop::route('/create'),
            'edit' => Pages\EditShop::route('/{record}/edit'),
        ];
    }
}
