<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Imports\StudentsEditImport;
use App\Imports\StudentsImport;
use App\Models\Student;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Validators\ValidationException;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Manajemen Santri';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationLabel = 'Santri';
    protected static ?string $pluralLabel = 'Santri';
    protected static ?string $slug = 'santri';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Santri')
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
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginationPageOptions([5, 25, 50, 100, 250])
            ->defaultPaginationPageOption(5)
            ->defaultSort('id', direction: 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('nomor_induk_santri')
                    ->label('Nomor Induk Santri')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_santri')
                    ->label('Nama Santri')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_wali_santri')
                    ->label('BIN / BINTI')
                    ->searchable(),
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
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->modalHeading(fn($record) => 'Hapus Santri: ' . $record->nama_santri),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make("santri")
                    ->label('Export Excel')
                    ->color('info')
                    ->url(route('export-santri')),
                Action::make('tambah.santri')
                    ->label('Tambah Santri by Excel')
                    ->color('info')
                    ->form([
                        FileUpload::make('attachment')
                            ->label('Upload Excel Santri')
                            ->required()
                            ->helperText('Pastikan file yang diunggah sesuai dengan template yang telah disediakan. Silakan klik tombol "Download Template" untuk mendapatkan template yang benar.'),
                    ])
                    ->action(function (array $data) {
                        $file = public_path('storage/' . $data['attachment']);

                        try {
                            $import = new StudentsImport();
                            Excel::import($import, $file);
                            $totalRows = $import->getRowCount();
                            Notification::make()
                                ->title('Data Santri diimpor')
                                ->body("Data Santri diimpor sebanyak {$totalRows} baris.")
                                ->success()
                                ->send();
                        } catch (ValidationException $er) {

                            $failures = $er->failures();

                            $messages = collect($failures)->map(function ($failure) {
                                $row = $failure->row();
                                $attribute = $failure->attribute();
                                $error = $failure->errors()[0];
                                $value = $failure->values()[$attribute] ?? '-';

                                return "Baris {$row}: {$error} (\"{$value}\")";
                            });
                            Notification::make()
                                ->danger()
                                ->title('Impor Gagal')
                                ->body("Terdapat kesalahan:\n" . $messages->implode("\n"))
                                ->persistent()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Mengimpor')
                                ->body($e->getMessage())
                                ->persistent()
                                ->send();
                        }
                    })
                    ->extraModalFooterActions(fn(Action $action): array => [
                        Action::make('download-template')
                            ->label('Download Template')
                            ->color('info')
                            ->url(route('template-santri'))
                            ->openUrlInNewTab(),
                    ]),
                Action::make('ubah.santri')
                    ->label('Ubah Santri by Excel')
                    ->color('info')
                    ->form([
                        FileUpload::make('attachment')
                            ->label('Upload Excel Santri')
                            ->required()
                            ->helperText('Pastikan file yang diunggah adalah file dari Download Excel Santri. Silakan klik tombol "Download Excel Santri".'),
                    ])
                    ->action(function (array $data) {
                        $file = public_path('storage/' . $data['attachment']);

                        try {
                            $import = new StudentsEditImport();
                            Excel::import($import, $file);
                            $totalRows = $import->getRowCount();
                            Notification::make()
                                ->title('Data Santri berhasil diubah')
                                ->body("Data Santri diubah sebanyak {$totalRows} baris.")
                                ->success()
                                ->send();
                        } catch (ValidationException $e) {
                            $failures = $e->failures();

                            $messages = collect($failures)->map(function ($failure) {
                                $row = $failure->row();
                                $attribute = $failure->attribute();
                                $error = $failure->errors()[0];
                                $value = $failure->values()[$attribute] ?? '-';

                                return "Baris {$row}: {$error} (\"{$value}\")";
                            });

                            Notification::make()
                                ->danger()
                                ->title('Impor Gagal')
                                ->body("Terdapat kesalahan:<br>" . $messages->implode('<br>')) // atau gunakan \n jika support
                                ->persistent()
                                ->send();
                        } catch (ValidationException $e) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Mengimpor')
                                ->body($e->getMessage())
                                ->persistent()
                                ->send();
                        }
                    })
                    ->extraModalFooterActions(fn(Action $action): array => [
                        Action::make('download-template')
                            ->label('Download Excel Santri')
                            ->color('info')
                            ->url(route('export-santri'))
                            ->openUrlInNewTab(),
                    ])

            ])
        ;
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
