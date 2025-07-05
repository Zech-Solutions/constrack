<?php

namespace App\Filament\Resources\CanvassResource\Pages;

use App\Filament\Resources\CanvassResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCanvass extends CreateRecord
{
    protected static string $resource = CanvassResource::class;

    protected function afterCreate(): void
    {
        $this->saveCanvassItems();
    }

    protected function saveCanvassItems(): void
    {
        // $profit_percent = $this->form->getState()['profit_percent'];
        // $sequence = 1;
        // $preliminaries = $this->form->getState()['preliminaries'] ?? [];
        // foreach ($preliminaries as $item) {
        //     $this->record->quotationItems()->create([
        //         'work_id' => $item['work_id'],
        //         'work_category_id' => $item['work_category_id'],
        //         'type' => QuotationItemType::PRELIMINARIES,
        //         'quantity' => 1,
        //         'unit_cost' => 0,
        //         'unit_price' => 0,
        //         'total' => $item['total'],
        //         'sequence' => $sequence++,
        //     ]);
        // }

        // $works = $this->form->getState()['works'] ?? [];
        // foreach ($works as $item) {
        //     $details = $this->record->quotationItems()->create([
        //         'work_id' => $item['work_id'],
        //         'work_category_id' => $item['work_category_id'],
        //         'type' => QuotationItemType::SUB_CATEGORY,
        //         // 'product_id' => $item['product_id'],
        //         'quantity' => 1,
        //         'unit_cost' => 1,
        //         'unit_price' => 1,
        //         'sequence' => $sequence++,
        //     ]);

        //     $categoryMaterials = CategoryMaterial::query()
        //         ->where('work_category_id', $item['work_category_id'])
        //         ->get();

        //     $categoryMaterials->each(function (CategoryMaterial $categoryMaterial) use ($item, $details) {
        //         $this->record->quotationItems()->create([
        //             'work_id' => $item['work_id'],
        //             'work_category_id' => $item['work_category_id'],
        //             'parent_id' => $details->id,
        //             'type' => QuotationItemType::MATERIAL,
        //             'product_id' => $categoryMaterial['product_id'],
        //             'quantity' => $categoryMaterial['quantity'],
        //             'unit_cost' => 0,
        //             'unit_price' => 0,
        //         ]);
        //     });
        // }
    }
}
