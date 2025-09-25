<?php

namespace App\Filament\Exports;

use App\Models\Appointment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class AppointmentExporter extends Exporter
{
    protected static ?string $model = Appointment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('patient.name'),
            ExportColumn::make('doctorService.doctor.user.name')
                ->label('Doctor'),
            ExportColumn::make('doctorService.service.name')
                ->label('Service'),
            ExportColumn::make('date'),
            ExportColumn::make('start_at'),
            ExportColumn::make('end_at'),
            ExportColumn::make('status'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your appointment export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
