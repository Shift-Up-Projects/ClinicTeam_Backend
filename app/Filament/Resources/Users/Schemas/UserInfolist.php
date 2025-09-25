<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->description('Basic user details and contact information')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Group::make([
                                    TextEntry::make('name')
                                        ->label('Full Name')
                                        ->weight('font-bold')
                                        ->icon('heroicon-o-user'),

                                    TextEntry::make('email')
                                        ->label('Email Address')
                                        ->icon('heroicon-o-envelope')
                                        ->copyable()
                                        ->copyMessage('Copied!')
                                        ->copyMessageDuration(1500),


                                ])->columnSpan(2),
                                Group::make([
                                    TextEntry::make('mobile')
                                        ->label('Phone')
                                        ->icon('heroicon-o-phone')
                                        ->copyable(),

                                    TextEntry::make('birth_date')
                                        ->label('Date of Birth')
                                        ->date('F j, Y')
                                        ->icon('heroicon-o-cake'),
                                ])
                            ]),
                    ]),

                Tabs::make('User Details')
                    ->tabs([
                        Tabs\Tab::make('Account')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('role')
                                            ->label('Account Type')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'admin' => 'danger',
                                                'doctor' => 'primary',
                                                'patient' => 'success',
                                                default => 'gray',
                                            })
                                            ->icon(fn (string $state): string => match ($state) {
                                                'admin' => 'heroicon-o-shield-check',
                                                'doctor' => 'heroicon-o-user-group',
                                                'patient' => 'heroicon-o-user',
                                                default => 'heroicon-o-user',
                                            }),

                                        TextEntry::make('email_verified_at')
                                            ->label('Email Verification')
                                            ->badge()
                                            ->color(fn ($state): string => $state ? 'success' : 'warning')
                                            ->formatStateUsing(fn ($state) => $state ? 'Verified' : 'Unverified')
                                            ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'),

                                        TextEntry::make('created_at')
                                            ->label('Member Since')
                                            ->dateTime('M j, Y')
                                            ->icon('heroicon-o-calendar'),

                                        TextEntry::make('updated_at')
                                            ->label('Last Updated')
                                            ->since()
                                            ->icon('heroicon-o-clock'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
