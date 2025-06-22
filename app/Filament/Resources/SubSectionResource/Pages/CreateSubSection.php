<?php

namespace App\Filament\Resources\SubSectionResource\Pages;

use App\Filament\Resources\SubSectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubSection extends CreateRecord
{
    protected static string $resource = SubSectionResource::class;

    // protected function afterCreate(): void
    // {
    //     $this->saveRepeaterItems();
    // }

    // protected function saveRepeaterItems(): void
    // {
    //     $items = $this->form->getState()['items'] ?? [];

    //     foreach ($items as $index => $item) {
    //         $this->record->items()->create([
    //             'product_id' => $item['product_id'],
    //             'quantity' => $item['quantity'],
    //             'sequence' => $index + 1,
    //         ]);
    //     }
    // }

}
