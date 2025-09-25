<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Filters\DateRangeFilter;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        // Fetch all users with names, roles, and emails in a single query
        $users = Cache::remember('user_filter_options', 60 * 60, function () { // Cache for 1 hour
            return User::select(['name', 'email'])->get();
        });

        $nameOptions = $users->pluck('name', 'name')->toArray();
        $emailOptions = $users->pluck('email', 'email')->toArray();

        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->url(fn (User $record): string => 'mailto:' . $record->email)
                    ->searchable()
                    ->openUrlInNewTab(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->label('Email Verified At')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (User $record) => match ($record->role) {
                        'admin' => 'danger',
                        'doctor' => 'success',
                        default => 'warning',
                    })
                    ->label('Role')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Added On')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray')
                    ->description(fn ($record) => $record->created_at->diffForHumans()),

                TextColumn::make('updated_at')
                    ->label('Updated On')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray')
                    ->description(fn ($record) => $record->created_at->diffForHumans()),

                TextColumn::make('mobile')
                    ->label('Mobile')
                    ->alignCenter()
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('birth_date')
                    ->label('Birth Date')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
            ])
            ->filters([
                SelectFilter::make('name')
                    ->label('Name')
                    ->options($nameOptions)
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'doctor' => 'Doctor',
                        'patient' => 'Patient',
                    ])
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('email')
                    ->label('Email')
                    ->options($emailOptions)
                    ->searchable()
                    ->preload()
                    ->multiple(),

                DateRangeFilter::make('created_at')
            ])
            ->recordActions([
                ViewAction::make(),
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
