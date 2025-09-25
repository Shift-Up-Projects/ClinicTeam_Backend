<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Monthly Revenue';
    protected static ?int $sort = 1;
    protected ?string $pollingInterval = '60s';
    protected static bool $isLazy = true;
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];


    protected function getData(): array
    {
        $trend = Trend::query(
            Invoice::query()
                ->where('status', 'paid')
        )
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear()
            )
            ->perMonth()
            ->sum('total_amount');
        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $trend->map(fn($item) => $item->aggregate)->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $trend->map(fn($item) => (new Carbon($item->date))->format('M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
