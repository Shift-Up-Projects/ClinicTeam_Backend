<?php

namespace App\Filament\Resources\Doctors\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Phiki\Phast\Text;

class ServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'services';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->html()
                    ->wrap(),
                TextColumn::make('estimated_duration')
                    ->label('Duration')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' hrs')
                    ->sortable()
                    ->alignLeft(),

                TextColumn::make('estimated_price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable()
                    ->alignLeft(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
