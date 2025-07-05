<?php

namespace App\Filament\Resources\CanvassResource\Pages;

use App\Filament\Resources\CanvassResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCanvass extends EditRecord
{
    protected static string $resource = CanvassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
