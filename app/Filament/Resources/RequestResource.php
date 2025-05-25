<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequestResource\Pages;
use App\Filament\Resources\RequestResource\RelationManagers;
use App\Models\Request;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';
    // protected static ?int $navigationSort = ;

    protected static ?string $navigationGroup = 'Manajemen Permohonan';
    protected static ?string $navigationLabel = 'Permohonan';
    protected static ?string $pluralLabel = 'Permohonan';
    protected static ?string $slug = 'permohonan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Permohonan')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('request_number')
                            ->label('Nomor Permohonan')
                            ->required()
                            ->readOnly()
                            ->maxLength(255)
                            ->default(fn() => 'REQ-' . strtoupper(uniqid())),
                        Forms\Components\Select::make('status')
                            ->label('Status Permohonan')
                            ->native(false)
                            ->options([
                                'Menunggu Verifikasi' => 'Menunggu Verifikasi',
                                'Sedang Proses Pengadaan' => 'Sedang Proses Pengadaan',
                                'Selesai' => 'Selesai',
                                'Pengajuan Ditolak' => 'Pengajuan Ditolak',
                            ])
                            ->default('Menunggu Verifikasi')
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->label('Akun Pemohon')
                            ->relationship('user', 'name')
                            ->preload()
                            ->required()
                            ->searchable()
                            ->default(fn() => Auth::id())
                            ->reactive() // 🔁 trigger perubahan
                            ->afterStateUpdated(function (callable $set, $state) {
                                $user = \App\Models\User::find($state);
                                $set('nama_pemesan', $user?->name ?? '');
                            }),
                        Forms\Components\TextInput::make('nama_pemesan')
                            ->required()
                            ->readOnly()
                            ->maxLength(255)
                            ->default(fn() => Auth::user()->name),
                        Forms\Components\DatePicker::make('tanggal_permohonan')
                            ->native(false)
                            ->required()
                            ->default(now())
                            ->displayFormat('l, d F Y')
                            ->closeOnDateSelection(),
                        Forms\Components\DatePicker::make('deadline')
                            ->required()
                            ->native(false)
                            ->displayFormat('l, d F Y')
                            ->closeOnDateSelection(),

                        Forms\Components\TextInput::make('kelas_divisi')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nama_barang')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('jumlah_barang')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('sumber_dana')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('budget')
                            ->required()
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
                        Forms\Components\Textarea::make('tujuan')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginationPageOptions([5, 25, 50, 100, 250])
            ->defaultPaginationPageOption(5)
            ->defaultSort('id', direction: 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('request_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_permohonan')
                    ->date()
                    ->dateTime('l, d F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deadline')
                    ->date()
                    ->dateTime('l, d F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_pemesan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelas_divisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_barang')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sumber_dana')
                    ->searchable(),
                Tables\Columns\TextColumn::make('budget')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading(fn($record) => 'Hapus Permohonan: ' . $record->request_number),
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
            'index' => Pages\ListRequests::route('/'),
            'create' => Pages\CreateRequest::route('/create'),
            'edit' => Pages\EditRequest::route('/{record}/edit'),
        ];
    }
}
