<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestAppointments extends BaseWidget
{
    protected static ?string $heading = 'Latest Appointments';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Appointment::query()
                    ->with(['patient', 'doctorService.doctor', 'doctorService.service'])
                    ->latest('date')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable(),

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
                    ->searchable()
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
                    ->sortable(),
            ])
            ->actions([
                EditAction::make()
                    ->url(fn (Appointment $record): string => route('filament.1.resources.appointments.edit', $record)),
            ])
            ->paginated([5]);
    }
}
