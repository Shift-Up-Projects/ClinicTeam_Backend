<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        // Total Revenue
        $totalRevenue = Invoice::query()
            ->where('status', 'paid')
            ->sum('total_amount');

        // Revenue trend for the last 30 days
        $revenueData = Trend::model(Invoice::class)
            ->dateColumn('created_at') // optional if default
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->count();

        // Appointments data for the last 30 days
        $appointmentData = Trend::model(Appointment::class)
            ->dateColumn('created_at')
            ->dateAlias('date2')
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->count();

        $appointmentsLastMonth = $appointmentData->sum('aggregate');
        $appointmentsLastWeek = $appointmentData->where('date', '>=', now()->subWeek())->sum('aggregate');

        // Total patients
        $totalPatients = User::query()
            ->where('role', 'patient')
            ->count();

        // Most common service
        $mostCommonService = Service::select('name')
            ->withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->first();

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('All time revenue')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->chart(
                    $revenueData
                        ->map(fn (TrendValue $value) => $value->aggregate)
                        ->toArray()
                )
                ->chartColor('success'),

            Stat::make('New Appointments (30d)', number_format($appointmentsLastMonth))
                ->description(number_format($appointmentsLastWeek) . ' in the last 7 days')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('primary')
                ->chart(
                    $appointmentData->pluck('aggregate')->toArray()
                )
                ->chartColor('primary'),

            Stat::make('Total Patients', number_format($totalPatients))
                ->description('Active Patients')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),

            Stat::make('Most Common Service', $mostCommonService?->name ?? 'N/A')
                ->description('By number of appointments')
                ->descriptionIcon('heroicon-o-clipboard-document')
                ->color('warning'),
        ];
    }
}
