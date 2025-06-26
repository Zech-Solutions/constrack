<?php

namespace App\Filament\TenantManager\Resources\PreRegistrationResource\Pages;

use App\Filament\TenantManager\Resources\PreRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPreRegistration extends EditRecord
{
    protected static string $resource = PreRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
