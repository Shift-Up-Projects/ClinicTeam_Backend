<?php

namespace App\Filament\Exports;

use App\Models\MedicalRecord;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class MedicalRecordExporter extends Exporter
{
    protected static ?string $model = MedicalRecord::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('appointment.patient.name'),
            ExportColumn::make('appointment_id'),
            ExportColumn::make('appointment.doctorService.doctor.user.name')
                ->label('Doctor'),
            ExportColumn::make('appointment.doctorService.service.name')
                ->label('Service'),
            ExportColumn::make('appointment.date')
                ->label('Date'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your medical record export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
