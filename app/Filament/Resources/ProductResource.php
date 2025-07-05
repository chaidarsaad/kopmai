<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Imports\ProductEditImport;
use App\Imports\ProductImport;
use App\Models\Product;
use App\Models\Store;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Support\RawJs;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-s-cube';
    protected static ?string $navigationGroup = 'Manajemen Produk';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $pluralLabel = 'Produk';
    protected static ?string $slug = 'produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informasi Dasar')
                            ->collapsible()
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label('Kategori')
                                    ->relationship('category', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->required(),
                                Forms\Components\Select::make('shop_id')
                                    ->label('Tenant')
                                    ->relationship('shop', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->disabled(fn() => Auth::user()->hasRole('owner_tenant')) // ⛔ Nonaktifkan jika owner_tenant
                                    ->default(fn() => Auth::user()->hasRole('owner_tenant') ? Auth::user()->shop_id : null) // ✅ Auto set jika owner_tenant
                                    ->required(), // tambahkan jika memang wajib
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
                        Forms\Components\Section::make('Harga')
                            ->collapsible()
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->label('Harga Jual Kopmai')
                                    ->mask(
                                        RawJs::make(<<<'JS'
                                    $input => {
                                        let number = $input.replace(/[^\d]/g, '');
                                        if (number === '') return '';
                                        return new Intl.NumberFormat('id-ID').format(Number(number));
                                    }
                                JS)
                                    )
                                    ->stripCharacters([',', '.'])
                                    ->numeric()
                                    ->prefix('Rp'),
                                Forms\Components\TextInput::make('buying_price')
                                    ->required()
                                    ->label('Harga Beli Kopmai')
                                    ->mask(
                                        RawJs::make(<<<'JS'
                                    $input => {
                                        let number = $input.replace(/[^\d]/g, '');
                                        if (number === '') return '';
                                        return new Intl.NumberFormat('id-ID').format(Number(number));
                                    }
                                JS)
                                    )
                                    ->stripCharacters([',', '.'])
                                    ->numeric()
                                    ->prefix('Rp'),
                                // Forms\Components\TextInput::make('stock')
                                //     ->visible(fn() => auth()->user()->hasRole('pengelola_web'))
                                //     ->label('Stok Produk')
                                //     ->helperText('Boleh kosong')
                                //     ->numeric(),
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
                        // Forms\Components\TextInput::make('image')
                        //     ->label('Gambar Produk')
                        //     ->columnSpanFull(),
                        Forms\Components\FileUpload::make('images')
                            ->label('Gambar Produk')
                            ->multiple()
                            ->required()
                            ->image()
                            ->downloadable(fn() => !Auth::user()->hasRole('owner_tenant'))
                            ->openable()
                            ->reorderable()
                            ->acceptedFileTypes(['image/png'])
                            ->maxSize(2048)
                            ->columnSpanFull()
                            ->helperText(function () {
                                $store = Store::first();
                                $waUrl = 'https://wa.me/' . ($store?->whatsapp ?? '6281234567890');

                                return new HtmlString("
                                    Dimensi gambar: 500px x 500px<br>
                                    Background transparan<br>
                                    Fokus ke produknya<br>
                                    Format gambar: PNG dengan ukuran maksimal 2MB<br>
                                    Gambar bisa lebih dari 1, gambar pertama (urutan paling atas) akan dijadikan thumbnail, silahkan klik dan tahan lalu pindahkan keatas untuk mengubah posisinya<br>
                                    Jika merasa kesulitan, segera hubungi Admin —
                                    <a href=\"{$waUrl}\" target=\"_blank\" style=\"color: #1c64f2; text-decoration: underline;\">
                                        klik di sini untuk chat via WhatsApp
                                    </a>. InsyaAllah nanti dibantu prosesnya.
                                ");
                            })
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();

        return $table
            ->modifyQueryUsing(function (Builder $query) use ($user) {
                if ($user->hasRole('owner_tenant')) {
                    // Tampilkan hanya produk dari tenant yang dimiliki oleh user
                    $query->where('shop_id', $user->shop_id);
                }

                return $query;
            })
            ->infinite()
            ->paginationPageOptions([5, 25, 50, 100, 250])
            ->defaultPaginationPageOption(5)
            ->defaultSort('id', direction: 'desc')
            ->columns([
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktifkan produk?')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->circular()
                    ->getStateUsing(function ($record) {
                        $images = $record->images;

                        if (is_array($images) && !empty($images)) {
                            $reversed = array_reverse($images);
                            return asset('storage/' . $reversed[0]);
                        }

                        if (!empty($record->image)) {
                            return "https://drive.google.com/thumbnail?id={$record->image}&sz=w1000";
                        }

                        return asset('image/no-pictures.png');
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shop.name')
                    ->label('Tenant')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->searchable()
                    ->label('Harga Jual')
                    ->sortable(),
                Tables\Columns\TextColumn::make('buying_price')
                    ->money('IDR')
                    ->searchable()
                    ->label('Harga Beli')
                    ->sortable(),
                // Tables\Columns\TextColumn::make('laba')
                //     ->money('IDR')
                //     ->searchable()
                //     ->sortable()
                //     ->visible(fn() => auth()->user()->hasRole('pengelola_web')),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable()
                    ->visible(fn() => auth()->user()->hasRole('pengelola_web'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('shop_id')
                    ->relationship('shop', 'name')
                    ->label('Tenant')
                    ->preload()
                    ->searchable()
                    ->multiple()
                    ->visible(fn() => !auth()->user()->hasRole('owner_tenant')),
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Kategori')
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
                // Action::make("Template")
                //     ->label('Download Template Excel Produk')
                //     ->color('info')
                //     ->url(route('download-template')),
                // Action::make('importProducts')
                //     ->color('info')
                //     ->label('Import Produk by Template')
                //     ->form([
                //         FileUpload::make('attachment')
                //             ->label('Upload Template Produk')
                //     ])
                //     ->action(function (array $data) {
                //         $file = public_path('storage/' . $data['attachment']);

                //         try {
                //             $import = new ProductImport();
                //             Excel::import($import, $file);
                //             $totalRows = $import->getRowCount();
                //             Notification::make()
                //                 ->title('Produk diimpor')
                //                 ->body("Produk diimpor sebanyak {$totalRows} baris.")
                //                 ->success()
                //                 ->send();
                //         } catch (\Exception $e) {
                //             Notification::make()
                //                 ->danger()
                //                 ->title('Produk gagal diimpor')
                //                 ->body($e->getMessage())
                //                 ->send();
                //         }
                //     }),
                // Action::make("Download Data Update Masal")
                //     ->label('Download Data Update Masal')
                //     ->url(route('download-data')),
                // Action::make('importProductsMasal')
                //     ->label('Import Update Masal')
                //     ->form([
                //         FileUpload::make('attachment')
                //             ->label('Upload Excel Produk')
                //     ])
                //     ->action(function (array $data) {
                //         $file = public_path('storage/' . $data['attachment']);

                //         try {
                //             $import = new ProductEditImport();
                //             Excel::import($import, $file);
                //             $totalRows = $import->getRowCount();
                //             Notification::make()
                //                 ->title('Update masal sukses')
                //                 ->body("Produk diedit sebanyak {$totalRows} baris.")
                //                 ->success()
                //                 ->send();
                //         } catch (\Exception $e) {
                //             Notification::make()
                //                 ->danger()
                //                 ->title('Update masal gagal')
                //                 ->body($e->getMessage())
                //                 ->send();
                //         }
                //     }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Ubah Produk'),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading(fn($record) => "Hapus Produk: {$record->name}"),

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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
