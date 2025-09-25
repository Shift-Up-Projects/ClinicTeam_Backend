<?php

namespace App\Filament\Resources\MedicalRecords\Schemas;

use App\Models\Appointment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MedicalRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Appointment Information')
                    ->description('Select the appointment this medical record belongs to')
                    ->schema([
                        Select::make('appointment_id')
                            ->label('Appointment')
                            ->placeholder('Search for an appointment...')
                            ->searchable()
                            ->options(
                                Appointment::query()
                                    ->with(['patient', 'doctorService.service', 'doctorService.doctor.user'])
                                    ->whereDoesntHave('medicalRecord')
                                    ->get()
                                    ->mapWithKeys(fn ($appointment) => [
                                        $appointment->id => sprintf(
                                            '%s - %s - Dr. %s - %s - %s',
                                            $appointment->date->format('M d, Y'),
                                            $appointment->patient->name,
                                            $appointment->doctorService->doctor->user->name,
                                            $appointment->doctorService->service->name,
                                            str($appointment->status)->title()
                                        )
                                    ])
                            )
                            ->required()
                            ->helperText('Search by patient name, doctor name, or date'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Medical Notes')
                    ->description('Enter the medical details and observations')
                    ->schema([
                        Textarea::make('notes')
                            ->required()
                            ->minLength(10)
                            ->maxLength(5000)
                            ->rows(3)
                            ->helperText('Be specific about symptoms, diagnosis, and treatment')
                            ->hintIconTooltip('Detailed medical observations and notes'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ])
            ->columns(1);
    }
}
