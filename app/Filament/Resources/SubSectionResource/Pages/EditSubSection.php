<?php

namespace App\Filament\Resources\SubSectionResource\Pages;

use App\Filament\Resources\SubSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubSection extends EditRecord
{
    protected static string $resource = SubSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
