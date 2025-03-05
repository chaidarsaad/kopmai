<?php

namespace App\Filament\Resources;

use App\Exports\OrdersExport;
use App\Exports\ProductsExport;
use App\Filament\Exports\OrderExporter;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Services\OrderStatusService;
use Carbon\Carbon;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Manajemen Pesanan';
    protected static ?string $navigationLabel = 'Pesanan';
    protected static ?string $pluralLabel = 'Pesanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informasi Umum')
                            ->collapsible()
                            ->schema([
                                Forms\Components\TextInput::make('order_number')
                                    ->label('No. Pesanan')
                                    ->disabled(),
                                Forms\Components\TextInput::make('created_at')
                                    ->label('Tanggal Pesan')
                                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d M Y H:i'))
                                    ->disabled(),
                                Forms\Components\TextInput::make('user.email')
                                    ->label('Email')
                                    ->formatStateUsing(fn($record, $state) => $record->user?->email ?? '-')
                                    ->disabled(),
                                Forms\Components\TextInput::make('user.name')
                                    ->label('Nama Wali')
                                    ->formatStateUsing(fn($record, $state) => $record->user?->name ?? '-')
                                    ->disabled(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('No HP Wali')
                                    ->tel()
                                    ->disabled(),
                            ]),
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Penerima')
                            ->collapsible()
                            ->schema([
                                Forms\Components\TextInput::make('nama_santri')
                                    ->label('Nama Santri')
                                    ->disabled(),
                                Forms\Components\Select::make('classroom_id')
                                    ->relationship('classroom', 'name')
                                    ->preload()
                                    ->native(false)
                                    ->label('Kelas Santri')
                                    ->disabled(),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Catatan Tambahan')
                                    ->disabled(),
                            ]),
                    ]),
                Forms\Components\Section::make('Detail Harga')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Harga')
                            ->readOnly()
                            ->numeric()
                            ->default(0),
                    ]),
                Forms\Components\Section::make('Status Pesanan')
                    ->collapsible()
                    ->schema([
                        Forms\Components\FileUpload::make('payment_proof')
                            ->label('Bukti Pembayaran')
                            ->image()
                            ->disk('public')
                            ->directory('payment-proofs')
                            ->openable()
                            ->downloadable()
                            ->disabled(),
                        Forms\Components\Select::make('payment_status')
                            ->label('Status Pembayaran')
                            ->options([
                                OrderStatusService::PAYMENT_UNPAID => OrderStatusService::getPaymentStatusLabel(OrderStatusService::PAYMENT_UNPAID),
                                OrderStatusService::PAYMENT_PAID => OrderStatusService::getPaymentStatusLabel(OrderStatusService::PAYMENT_PAID),
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                OrderStatusService::STATUS_PENDING => OrderStatusService::getStatusLabel(OrderStatusService::STATUS_PENDING),
                                OrderStatusService::STATUS_PROCESSING => OrderStatusService::getStatusLabel(OrderStatusService::STATUS_PROCESSING),
                                OrderStatusService::STATUS_COMPLETED => OrderStatusService::getStatusLabel(OrderStatusService::STATUS_COMPLETED),
                                OrderStatusService::STATUS_CANCELLED => OrderStatusService::getStatusLabel(OrderStatusService::STATUS_CANCELLED),
                            ])
                            ->native(false)
                            ->required(),
                    ]),
                Forms\Components\Section::make('Produk dipesan')
                    ->collapsed()
                    ->schema([
                        self::getItemsRepeater(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->paginationPageOptions([5, 25, 50, 100, 250])
            ->defaultPaginationPageOption(5)
            ->defaultSort('id', direction: 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_number')
                    ->label('No. Pesanan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('recipient_name')
                    ->label('Nama Wali')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_santri')
                    ->label('Nama Santri')
                    ->searchable(),
                Tables\Columns\TextColumn::make('classroom.name')
                    ->label('Kelas Santri')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('IDR')
                    ->label('Total')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        OrderStatusService::PAYMENT_UNPAID => 'danger',
                        OrderStatusService::PAYMENT_PAID => 'success',
                    })
                    ->formatStateUsing(fn($state) => OrderStatusService::getPaymentStatusLabel($state)),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        OrderStatusService::STATUS_PENDING => 'warning',
                        OrderStatusService::STATUS_PROCESSING => 'info',
                        OrderStatusService::STATUS_COMPLETED => 'success',
                        OrderStatusService::STATUS_CANCELLED => 'danger',
                    })
                    ->formatStateUsing(fn($state) => OrderStatusService::getStatusLabel($state)),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('Status Pembayaran')
                    ->options([
                        OrderStatusService::PAYMENT_UNPAID => OrderStatusService::getPaymentStatusLabel(OrderStatusService::PAYMENT_UNPAID),
                        OrderStatusService::PAYMENT_PAID => OrderStatusService::getPaymentStatusLabel(OrderStatusService::PAYMENT_PAID),
                    ])->preload()
                    ->native(false),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('pesanan_dari'),
                        DatePicker::make('pesanan_sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['pesanan_dari'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['pesanan_sampai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['pesanan_dari'] ?? null) {
                            $indicators[] = Indicator::make('Pesanan dari ' . Carbon::parse($data['pesanan_dari'])->toFormattedDateString())
                                ->removeField('pesanan_dari');
                        }

                        if ($data['pesanan_sampai'] ?? null) {
                            $indicators[] = Indicator::make('Pesanan sampai ' . Carbon::parse($data['pesanan_sampai'])->toFormattedDateString())
                                ->removeField('pesanan_sampai');
                        }

                        return $indicators;
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Ubah Pesanan'),
                // Tables\Actions\Action::make('single_export')
                //     ->color('warning')
                //     ->label('Export')
                //     ->action(fn($record) => redirect()->route('download-order', ['order_id' => $record->id])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->color('gray')
                    ->label('Export Pesanan')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Dari tanggal')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('Sampai tanggal')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        return redirect()->route('download-rekap', [
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date']
                        ]);
                    })
            ])
        ;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('orderProducts')
            ->addable(false)
            ->deletable(false)
            ->label('Detail Produk')
            ->relationship()
            ->live()
            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                self::updateTotalPrice($get, $set);
            })
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->native(false)
                    ->searchable()
                    ->label('Produk')
                    ->disabled()
                    ->required()
                    ->options(Product::query()->pluck('name', 'id'))
                    ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get, $state) {
                        $product = Product::with('shop')->find($state);
                        $set('unit_price', $product->price ?? 0);
                        $set('stock', $product->stock ?? 0);
                        $set('shop_id', $product->shop_id ?? null);
                        $set('image_url', $product->image_url ?? null);
                        $set('is_ongkir', $product->shop->is_ongkir ?? false);
                        $set('shipping_cost', $product->shop->ongkir ?? 0);
                    })
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $product = Product::with('shop')->find($state);
                        $set('unit_price', $product->price ?? 0);
                        $set('stock', $product->stock ?? 0);
                        $set('shop_id', $product->shop_id);
                        $set('is_ongkir', $product->shop->is_ongkir ?? false);
                        $set('shipping_cost', $product->shop->ongkir ?? 0);
                        $quantity = $get('quantity') ?? 1;
                        $stock = $get('stock');
                        self::updateTotalPrice($get, $set);
                    })
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->preload(),
                Placeholder::make('image_preview')
                    ->label('Gambar Produk')
                    ->content(
                        fn(Forms\Get $get) => $get('image_url')
                            ? new HtmlString('<img src="' . e($get('image_url')) . '" style="width: 100%; height: auto; object-fit: cover; border-radius: 5px;" />')
                            : 'Tidak ada gambar'
                    )
                    ->columnSpanFull(),
                Forms\Components\Select::make('shop_id')
                    ->options(Shop::query()->pluck('name', 'id'))
                    ->preload()
                    ->disabled()
                    ->native(false)
                    ->label('Toko'),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->label('Jumlah')
                    ->numeric()
                    ->disabled()
                    ->default(1)
                    ->minValue(1)
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        self::updateTotalPrice($get, $set);
                    }),
                Forms\Components\TextInput::make('price')
                    ->label('Harga')
                    ->prefix('Rp')
                    ->disabled()
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('shipping_cost')
                    ->label('Ongkir')
                    ->prefix('Rp')
                    ->disabled()
                    ->numeric()
                    ->hidden(fn(Forms\Get $get) => !$get('is_ongkir')),
            ]);
    }

    protected static function updateTotalPrice(Forms\Get $get, Forms\Set $set): void
    {
        $selectedProducts = collect($get('orderProducts'))->filter(fn($item) => !empty($item['product_id']) && !empty($item['quantity']));

        $prices = Product::find($selectedProducts->pluck('product_id'))->pluck('price', 'id');
        $total = $selectedProducts->reduce(function ($total, $product) use ($prices) {
            return $total + ($prices[$product['product_id']] * $product['quantity']);
        }, 0);

        $set('total_price', $total);
    }
}
