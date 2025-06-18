<?php

namespace App\Filament\Resources\SupplierProductPriceResource\Pages;

use App\Filament\Resources\SupplierProductPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupplierProductPrice extends EditRecord
{
    protected static string $resource = SupplierProductPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
