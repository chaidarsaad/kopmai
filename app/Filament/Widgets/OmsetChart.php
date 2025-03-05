<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Transaction;
use Carbon\Carbon;

class OmsetChart extends ChartWidget
{
    protected static ?string $heading = 'Omset';
    protected static ?int $sort = 4;
    protected static string $color = 'success';
    public ?string $filter = 'week';

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $dateRange = match ($activeFilter) {
            'week' => [
                'start' => date('Y-m-d 00:00:00', strtotime('monday this week')),
                'end' => date('Y-m-d 23:59:59', strtotime('sunday this week')),
                'period' => 'perDay',
                'format' => 'Y-m-d',
                'label' => 'd M',
                'step' => '1 day',
            ],
            'month' => [
                'start' => date('Y-m-01 00:00:00'),
                'end' => date('Y-m-t 23:59:59'),
                'period' => 'perDay',
                'format' => 'Y-m-d',
                'label' => 'd M',
                'step' => '1 day',
            ],
            'year' => [
                'start' => date('Y-01-01 00:00:00'),
                'end' => date('Y-12-31 23:59:59'),
                'period' => 'perMonth',
                'format' => 'Y-m',
                'label' => 'M Y',
                'step' => '1 month',
            ],
        };

        $transactions = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get()
            ->groupBy(fn($transaction) => date($dateRange['format'], strtotime($transaction->created_at)));

        $timePoints = [];
        $currentTime = strtotime($dateRange['start']);

        while ($currentTime <= strtotime($dateRange['end'])) {
            $key = date($dateRange['format'], $currentTime);
            $timePoints[$key] = 0;
            $currentTime = strtotime("+{$dateRange['step']}", $currentTime);
        }

        $lastKey = date($dateRange['format'], strtotime($dateRange['end']));
        if (!isset($timePoints[$lastKey])) {
            $timePoints[$lastKey] = 0;
        }

        foreach ($transactions as $key => $group) {
            $timePoints[$key] = $group->sum('total_amount');
        }

        $labels = array_map(fn($key) => date($dateRange['label'], strtotime($key)), array_keys($timePoints));

        return [
            'datasets' => [
                [
                    'label' => 'Omset ' . $this->getFilters()[$activeFilter],
                    'data' => array_values($timePoints),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Minggu ini',
            'month' => 'Bulan ini',
            'year' => 'Tahun ini',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
