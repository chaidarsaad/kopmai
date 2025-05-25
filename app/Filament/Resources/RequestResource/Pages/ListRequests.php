<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use App\Models\Request;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListRequests extends ListRecords
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Permohonan')
                ->modalHeading('Buat Permohonan'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Menunggu Verifikasi' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', 'Menunggu Verifikasi');
                })
                ->badge(Request::where('status', 'Menunggu Verifikasi')->count()),
            'Sedang Proses Pengadaan' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', 'Sedang Proses Pengadaan');
                })
                ->badge(Request::where('status', 'Sedang Proses Pengadaan')->count()),
            'Selesai' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', 'Selesai');
                })
                ->badge(Request::where('status', 'Selesai')->count()),
            'Pengajuan Ditolak' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', 'Pengajuan Ditolak');
                })
                ->badge(Request::where('status', 'Pengajuan Ditolak')->count()),
            'Semua' => Tab::make()
                ->badge(Request::count()),
        ];
    }
}
