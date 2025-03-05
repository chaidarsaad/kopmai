<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalHeading('Buat Pengguna'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Admin' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_admin', '1'))
                ->badge(User::query()->where('is_admin', '1')->count()),
            'Wali' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_admin', '0'))
                ->badge(User::query()->where('is_admin', '0')->count()),
            'Semua' => Tab::make()
                ->badge(User::count()),
        ];
    }
}
