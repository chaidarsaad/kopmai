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
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
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
use Filament\Support\RawJs;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Filament\Forms\Components\TextInput;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Manajemen Pesanan';
    protected static ?string $navigationLabel = 'Pesanan';
    protected static ?string $pluralLabel = 'Pesanan';
    protected static ?string $slug = 'pesanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Umum')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->readOnly()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->label('No. Pesanan')
                            ->default(fn() => 'ORD-' . strtoupper(uniqid())),
                        Forms\Components\DatePicker::make('created_at')
                            ->label('Tanggal Pesan')
                            ->native(false)
                            ->displayFormat('l, d F Y')
                            ->default(now())
                            ->closeOnDateSelection()
                            ->required(),
                        Forms\Components\Select::make('student_id')
                            ->label('Nama Santri')
                            ->required()
                            ->relationship('student', 'nama_santri')
                            ->preload()
                            ->native(false)
                            ->searchable()
                            ->createOptionForm([
                                Section::make('Data Santri')
                                    ->collapsible()
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('nomor_induk_santri')
                                            ->label('Nomor Induk Santri')
                                            ->required()
                                            ->maxLength(50)
                                            ->unique(ignoreRecord: true),
                                        Forms\Components\TextInput::make('nama_santri')
                                            ->unique(ignoreRecord: true)
                                            ->label('Nama Santri')
                                            ->required()
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('nama_wali_santri')
                                            ->label('BIN / BINTI')
                                            ->required(),
                                    ])
                            ])
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                if ($state) {
                                    $student = \App\Models\Student::find($state);
                                    $set('nama_wali', $student?->nama_wali_santri);
                                } else {
                                    $set('nama_wali', null);
                                }
                            }),
                        Forms\Components\TextInput::make('nama_wali')
                            ->label('Nama Wali')
                            ->readOnly()
                            ->dehydrated(true)
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->default(fn() => Auth::id())
                            ->hidden(),
                        Forms\Components\TextInput::make('phone')
                            ->label('No HP Wali')
                            ->tel()
                            ->formatStateUsing(fn($record, $state) => $record->user?->phone_number ?? Auth::user()->phone_number ?? '-'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Tambahan')
                    ]),
                Forms\Components\Section::make('Detail Harga')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Harga')
                            ->readOnly()
                            ->mask(
                                RawJs::make(<<<'JS'
                                function(input) {
                                    const cleaned = input.replace(/\D/g, '');
                                    if (cleaned === '') return '';
                                    const numberValue = Number(cleaned);
                                    if (isNaN(numberValue)) return '';
                                    return new Intl.NumberFormat('id-ID').format(numberValue);
                                }
                                JS)
                            )
                            ->stripCharacters(['.', ','])
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live()
                        ,
                        Forms\Components\Hidden::make('subtotal')
                            ->label('Total Harga')
                            ->default(0)
                            ->live(),
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
                            ->downloadable(fn() => !Auth::user()->hasRole('owner_tenant')),
                        Forms\Components\Select::make('payment_status')
                            ->label('Status Pembayaran')
                            ->options([
                                OrderStatusService::PAYMENT_UNPAID => OrderStatusService::getPaymentStatusLabel(OrderStatusService::PAYMENT_UNPAID),
                                OrderStatusService::PAYMENT_PAID => OrderStatusService::getPaymentStatusLabel(OrderStatusService::PAYMENT_PAID),
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('paid_date')
                            ->label('Tanggal Bayar')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->displayFormat('l, d F Y'),
                        Forms\Components\TextInput::make('nominal_pembayaran')
                            ->label('Nominal Pembayaran')
                            ->mask(
                                RawJs::make(<<<'JS'
                                function(input) {
                                    const cleaned = input.replace(/\D/g, '');
                                    if (cleaned === '') return '';
                                    const numberValue = Number(cleaned);
                                    if (isNaN(numberValue)) return '';
                                    return new Intl.NumberFormat('id-ID').format(numberValue);
                                }
                                JS)
                            )
                            ->stripCharacters(['.', ','])
                            ->numeric()
                            ->prefix('Rp')
                            ->hidden(fn() => Auth::user()->hasRole('owner_tenant')),
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
        $table = $table->modifyQueryUsing(function (Builder $query) {
            $user = auth()->user();

            if ($user->hasRole('owner_tenant')) {
                $query->whereHas('orderItems.product', function ($q) use ($user) {
                    $q->where('shop_id', $user->shop_id);
                });
            }

            return $query;
        });

        return $table
            ->paginationPageOptions([5, 25, 50, 100, 250])
            ->defaultPaginationPageOption(5)
            ->defaultSort('id', direction: 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('No. Pesanan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('l, d F Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.nama_santri')
                    ->label('Nama Santri')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.nama_wali_santri')
                    ->label('Nama BIN / BINTI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        $user = auth()->user();
                        if ($user->hasRole('owner_tenant')) {
                            $tenantShopId = $user->shop_id;
                            $total = $record->orderItems->sum(function ($item) use ($tenantShopId) {
                                return $item->product?->shop_id === $tenantShopId
                                    ? $item->quantity * $item->price
                                    : 0;
                            });
                            return 'Rp ' . number_format($total, 2, ',', '.');
                        }
                        return 'Rp ' . number_format($state, 2, ',', '.');
                    }),

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
                Tables\Actions\DeleteAction::make()
                    ->modalHeading(fn($record) => 'Hapus Pesanan: ' . $record->order_number),

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
                            ->displayFormat('l, d F Y')
                            ->closeOnDateSelection()
                            ->native(false)
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('Sampai tanggal')
                            ->displayFormat('l, d F Y')
                            ->closeOnDateSelection()
                            ->native(false)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        return redirect()->route('download-rekap', [
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date']
                        ]);
                    })
                    ->visible(fn() => auth()->user()->hasRole('pengelola_web'))
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

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('orderProducts')
            ->addable(true)
            ->deletable(true)
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
