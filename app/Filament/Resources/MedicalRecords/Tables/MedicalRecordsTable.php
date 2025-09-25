<?php

namespace App\Filament\Resources\MedicalRecords\Tables;

use App\Filament\Exports\MedicalRecordExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Phiki\Phast\Text;

class MedicalRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->searchable(),
                TextColumn::make('appointment_id')
                    ->label('Appointment')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('appointment.patient.name')
                    ->label('Patient Name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('appointment.doctorService.doctor.user.name')
                    ->label('Doctor Name')
                    ->sortable(),
                TextColumn::make('appointment.date')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('notes')
                    ->label('Notes')
                    ->html()
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(MedicalRecordExporter::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(MedicalRecordExporter::class),
                ]),
            ]);
    }
}
