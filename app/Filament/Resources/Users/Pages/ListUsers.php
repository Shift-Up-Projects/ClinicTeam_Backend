<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            "All" => Tab::make('All'),
            "Verified" => Tab::make('Verified')->modifyQueryUsing(fn ($query) => $query->whereNotNull('email_verified_at')),
            "Unverified" => Tab::make('Unverified')->modifyQueryUsing(fn ($query) => $query->whereNull('email_verified_at')),
        ];
    }
}
