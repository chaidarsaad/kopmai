<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    protected static ?string $navigationLabel = 'Resume Bisnis';

    public function getHeading(): string|Htmlable
    {
        return 'Resume Bisnis';
    }
    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->native(condition: false)
                            ->closeOnDateSelection()
                            ->displayFormat('l, d F Y')
                            ->label('Tanggal Mulai')
                            ->maxDate(fn(Get $get) => $get('endDate')),
                        DatePicker::make('endDate')
                            ->label('Tanggal Akhir')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->displayFormat('l, d F Y')
                            ->minDate(fn(Get $get) => $get('startDate'))
                            ->maxDate(now()),
                    ])
                    ->columns(2),
            ]);
    }
}
