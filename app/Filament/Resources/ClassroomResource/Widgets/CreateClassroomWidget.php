<?php

namespace App\Filament\Resources\ClassroomResource\Widgets;

use App\Models\Classroom;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;

class CreateClassroomWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.resources.classroom-resource.widgets.create-classroom-widget';
    protected int|string|array $columnSpan = 'full';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => '',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Tambah Kelas')
                    ->collapsible()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kelas')
                            ->required()
                            ->maxLength(255),
                    ])
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $validatedData = $this->form->validate();

        $existingClassroom = Classroom::where('name', $validatedData['data']['name'])->first();

        if ($existingClassroom) {
            throw ValidationException::withMessages([
                'name' => ['Nama kelas sudah ada!'],
            ]);
        }

        Classroom::create([
            'name' => $validatedData['data']['name'],
        ]);

        $this->form->fill(['name' => '']);
        $this->reset('data');

        $this->dispatch('classroom-created');

        Notification::make()
            ->title('Kelas berhasil dibuat!')
            ->success()
            ->send();
    }
}
