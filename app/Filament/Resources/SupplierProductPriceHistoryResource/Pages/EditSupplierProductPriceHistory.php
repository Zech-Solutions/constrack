<?php

namespace App\Filament\Resources\SupplierProductPriceHistoryResource\Pages;

use App\Filament\Resources\SupplierProductPriceHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupplierProductPriceHistory extends EditRecord
{
    protected static string $resource = SupplierProductPriceHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
