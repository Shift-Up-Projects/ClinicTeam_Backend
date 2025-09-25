<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\DoctorService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Schema;
use App\Rules\ValidAppointmentTime;
use Closure;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Patient & Doctor & Service')->components([
                    Select::make('patient_id')
                        ->relationship('patient', 'name')
                        ->preload()
                        ->required(),
                    Select::make('doctor_service_id')
                        ->label('Doctor & Service')
                        ->options(
                            DoctorService::with(['doctor.user', 'service'])
                                ->get()
                                ->mapWithKeys(fn ($doctorService) => [
                                    $doctorService->id => sprintf(
                                        'Dr. %s - %s ',
                                        $doctorService->doctor->user->name,
                                        $doctorService->service->name,
                                    )
                                ])
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live(),
                ])->columnSpanFull(),
                Section::make('Date & Status & Notes')->components([
                    Grid::make()
                        ->columns(2)
                        ->schema([
                            DatePicker::make('date')
                                ->required()
                                ->native(false)
                                ->displayFormat('M d, Y')
                                ->minDate(now()),
                            Select::make('status')
                                ->options([
                                    'pending' => 'Pending',
                                    'confirmed' => 'Confirmed',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                    'no_show' => 'No Show',
                                ])
                                ->default('pending')
                                ->required(),
                            Fieldset::make('Time')
                                    ->schema([
                                        TimePicker::make('start_at')
                                            ->seconds(false)
                                            ->minutesStep(15)
                                            ->required()
                                            ->rules([
                                                'required',
                                                function ($get) {
                                                    return function (string $attribute, $value, Closure $fail) use ($get) {
                                                        $doctorServiceId = $get('doctor_service_id');
                                                        $date = $get('date');

                                                        if (!$doctorServiceId || !$date) {
                                                            return;
                                                        }

                                                        $rule = new ValidAppointmentTime($doctorServiceId, $date);
                                                        $rule->validate($attribute, $value, $fail);
                                                    };
                                                },
                                            ]),
                                        TimePicker::make('end_at')
                                            ->seconds(false)
                                            ->minutesStep(15)
                                            ->after('start_at')
                                            ->required()
                                            ->rules([
                                                'required',
                                                'after:start_at',
                                            ]),
                                    ])->columnSpanFull(),
                        ]),
                    Textarea::make('notes')
                        ->columnSpanFull(),
                ])->columnSpanFull(),
            ]);
    }
}
