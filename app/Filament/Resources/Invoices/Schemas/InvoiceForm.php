<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Appointment;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->components([
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
                    TextInput::make('total_amount')
                        ->label('Total Amount')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(10)
                        ->columnSpan('full')
                        ->readOnlyOn('edit'),
                    Radio::make('status')
                        ->label('Payment Status')
                        ->options([
                            'unpaid' => 'Unpaid',
                            'paid' => 'Paid',
                        ])
                        ->inline()
                        ->default('unpaid')
                        ->required()
                        ->columnSpan('full'),
                ])
            ]);
    }
}
