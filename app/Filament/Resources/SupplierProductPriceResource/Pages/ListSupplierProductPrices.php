<?php

namespace App\Filament\Resources\SupplierProductPriceResource\Pages;

use App\Filament\Resources\SupplierProductPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupplierProductPrices extends ListRecords
{
    protected static string $resource = SupplierProductPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
