<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Models\Store;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Services\BiteshipService;
use Filament\Forms\Components\Wizard;
use Filament\Notifications\Notification;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Toko';
    protected static ?string $pluralLabel = 'Toko';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Toko')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Toggle::make('is_open')
                            ->label('Buka Toko?'),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Toko')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Toko')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('No WhatsApp Toko')
                            ->prefix('62')
                            ->helperText('Mohon masukan nomor tanpa angka 0 diawal. Contoh 812345678900')
                            ->placeholder('812345678900')
                            ->required()
                            ->numeric()
                            ->dehydrateStateUsing(fn($state) => '62' . ltrim($state, '62'))
                            ->formatStateUsing(fn($state) => ltrim($state, '62'))
                            ->validationAttribute('Nomor WhatsApp')
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('image')
                            ->imageEditor()
                            ->label('Logo Toko')
                            ->image()
                            ->image()
                            ->directory('stores'),
                        Forms\Components\FileUpload::make('banner')
                            ->imageEditor()
                            ->image()
                            ->label('Banner Toko')
                            ->directory('stores/banner'),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Toko')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\ColorPicker::make('primary_color')
                            ->label('Warna Utama Toko'),
                        Forms\Components\ColorPicker::make('secondary_color')
                            ->label('Warna Kedua Toko'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                Tables\Columns\ToggleColumn::make('is_open')
                    ->label('Buka Toko?'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Toko'),
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->label('Logo Toko'),
                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('No WA Toko'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Ubah Toko'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return Store::count() < 1;
    }
}
