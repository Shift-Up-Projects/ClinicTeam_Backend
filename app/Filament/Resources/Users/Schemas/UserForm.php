<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Heading')
                    ->tabs([
                        Tab::make('Personal Information')
                            ->schema([
                                TextInput::make('name')->required()->string(256)->label('Name'),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->string(256)
                                    ->label('Email')
                                    ->unique(ignoreRecord: true),
                                Select::make('role')
                                    ->options([
                                        'admin' => 'Admin',
                                        'patient' => 'Patient',
                                        'doctor' => 'Doctor',
                                    ])
                                    ->label('Role'),
                            ]),

                        Tab::make('Security Information')
                            ->schema([
                                TextInput::make('password')
                                    ->password()
                                    ->required()
                                    ->string(256)
                                    ->label('Password')
                                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                            ])->visibleOn('create'),

                        Tab::make('Phone and Birth date')
                            ->schema([
                                TextInput::make('mobile')
                                    ->string(256)
                                    ->label('Mobile'),
                                DatePicker::make('birth_date')
                                    ->label('Birth Date'),
                            ]),
                    ])->columnSpan('full'),
                ]);
    }
}
