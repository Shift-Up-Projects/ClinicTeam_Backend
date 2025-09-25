<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;

class PatientChart extends ChartWidget
{
    protected ?string $heading = 'New Patients';
    protected static ?int $sort = 2;
    protected ?string $pollingInterval = '60s';
    protected static bool $isLazy = true;
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected function getData(): array
    {
        $trend = Trend::query(
            User::query()
                ->where('role', 'patient')
        )
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear()
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'New Patients',
                    'data' => $trend->map(fn($item) => $item->aggregate)->toArray(),
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $trend->map(fn($item) => (new Carbon($item->date))->format('M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'barThickness' => 'flex',
            'maxBarThickness' => 40,
        ];
    }
}
