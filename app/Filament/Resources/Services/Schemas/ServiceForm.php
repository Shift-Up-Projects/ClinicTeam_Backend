<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Service Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->label('Service Name')
                            ->placeholder('e.g., Dental Checkup, Eye Exam')
                            ->helperText('Enter a clear and descriptive name for the service'),

                        RichEditor::make('description')
                            ->columnSpanFull()
                            ->label('Description')
                            ->maxLength(1000)
                            ->helperText('Detailed description of what the service includes')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'orderedList',
                                'bulletList',
                            ]),

                        TextInput::make('estimated_duration')
                            ->required()
                            ->numeric()
                            ->minValue(0.25)
                            ->maxValue(24)
                            ->step(0.25)
                            ->suffix('hours')
                            ->label('Estimated Duration')
                            ->helperText('Duration in hours (e.g., 0.5 for 30 minutes)'),

                        TextInput::make('estimated_price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->maxValue(1000000)
                            ->step(0.01)
                            ->label('Estimated Price')
                            ->helperText('Enter the price in USD'),
                    ])
                    ->columns(1),
            ]);
    }
}
