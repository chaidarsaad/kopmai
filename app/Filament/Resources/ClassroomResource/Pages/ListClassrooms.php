<?php

namespace App\Filament\Resources\ClassroomResource\Pages;

use App\Filament\Resources\ClassroomResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\On;

class ListClassrooms extends ListRecords
{
    protected static string $resource = ClassroomResource::class;

    #[On('classroom-created')]
    public function refresh()
    {
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ClassroomResource\Widgets\CreateClassroomWidget::class,
        ];
    }
}
