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
            'Pengelola Web' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereHas('roles', fn($q) => $q->where('name', 'pengelola_web')))
                ->badge(User::whereHas('roles', fn($q) => $q->where('name', 'pengelola_web'))->count()),
            'Wali' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDoesntHave('roles'))
                ->badge(User::whereDoesntHave('roles')->count()),
            'Semua' => Tab::make()
                ->badge(User::count()),
        ];
    }
}
