<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Filament\Exports\AppointmentExporter;
use App\Models\Appointment;
use App\Models\User;
use App\Models\DoctorService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Appointment $record): string => $record->patient->phone ?? ''),

                TextColumn::make('doctorService.doctor.user.name')
                    ->label('Doctor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('doctorService.service.name')
                    ->label('Service')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->date('M d, Y')
                    ->sortable()
                    ->description(fn (Appointment $record): string =>
                        $record->start_at->format('h:i A') . ' - ' . $record->end_at->format('h:i A')
                    ),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'scheduled',
                        'danger' => 'cancelled',
                        'gray' => 'pending',
                        'info' => 'confirmed',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'confirmed' => 'Confirmed',
                        'pending' => 'Pending',
                    ])
                    ->multiple()
                    ->placeholder('Filter by status'),

                SelectFilter::make('doctor')
                    ->relationship('doctorService.doctor.user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->placeholder('Filter by doctor'),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(AppointmentExporter::class),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(AppointmentExporter::class),
                ]),
            ])
            ->defaultSort('date', 'asc')
            ->defaultSort('start_at', 'asc')
            ->striped()
            ->deferLoading();
    }
}
