<?php

namespace App\Filament\Resources\Doctors\RelationManagers;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TimeOffsRelationManager extends RelationManager
{
    protected static string $relationship = 'timeOffs';
    protected static ?string $title = 'Time Off';
    protected static ?string $modelLabel = 'Time Off';
    protected static ?string $pluralModelLabel = 'Time Off';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DateTimePicker::make('start_at')
                    ->label('Start Time')
                    ->required()
                    ->seconds(false)
                    ->native(false)
                    ->minDate(now())
                    ->closeOnDateSelection()
                    ->displayFormat('M d, Y H:i'),

                DateTimePicker::make('end_at')
                    ->label('End Time')
                    ->required()
                    ->seconds(false)
                    ->native(false)
                    ->after('start_at')
                    ->closeOnDateSelection()
                    ->displayFormat('M d, Y H:i'),

                Textarea::make('reason')
                    ->label('Reason for Time Off')
                    ->required()
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('start_at')
                    ->label('Start Time')
                    ->dateTime('M d, Y')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('end_at')
                    ->label('End Time')
                    ->dateTime('M d, Y')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->reason),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
