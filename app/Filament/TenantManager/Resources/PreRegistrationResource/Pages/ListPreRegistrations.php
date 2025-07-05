<?php

namespace App\Filament\TenantManager\Resources\PreRegistrationResource\Pages;

use App\Filament\TenantManager\Resources\PreRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPreRegistrations extends ListRecords
{
    protected static string $resource = PreRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}
