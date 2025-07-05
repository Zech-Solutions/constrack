<?php

namespace App\Filament\Resources\CanvassResource\Pages;

use App\Filament\Resources\CanvassResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCanvasses extends ListRecords
{
    protected static string $resource = CanvassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
