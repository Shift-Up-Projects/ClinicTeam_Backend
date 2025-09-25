<?php

namespace App\Filament\Resources\Services\Tables;

use App\Filament\Exports\ServiceExporter;
use App\Models\Service;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('name')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->sortable()
                    ->description(fn (Service $record): string =>
                        strip_tags(Str::limit($record->description, 50))
                    )
                    ->wrap(),

                TextColumn::make('estimated_duration')
                    ->label('Duration')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' hrs')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('estimated_price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('free_services')
                    ->label('Free Services Only')
                    ->query(fn (Builder $query): Builder => $query->where('estimated_price', 0)),

                Filter::make('premium_services')
                    ->label('Premium Services Only')
                    ->query(fn (Builder $query): Builder => $query->where('estimated_price', '>', 0)),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ServiceExporter::class),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->action(fn () => \Filament\Notifications\Notification::make()
                            ->title('Services deleted')
                            ->success()
                            ->send()
                        ),
                    ExportBulkAction::make()
                        ->exporter(ServiceExporter::class),
                ]),
            ])
            ->striped()
            ->defaultSort('name')
            ->emptyStateHeading('No services found')
            ->emptyStateDescription('Create your first service to get started.');
    }
}
