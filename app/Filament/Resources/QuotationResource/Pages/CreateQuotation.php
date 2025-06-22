<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Enums\QuotationItemType;
use App\Filament\Resources\QuotationResource;
use App\Models\CategoryMaterial;
use Filament\Resources\Pages\CreateRecord;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;

    protected function afterCreate(): void
    {
        $this->saveQuotationItems();
    }

    protected function afterUpdate(): void
    {
        $this->record->quotationItems()->delete();
        $this->saveQuotationItems();
    }

    protected function saveQuotationItems(): void
    {
        $profit_percent = $this->form->getState()['profit_percent'];
        $sequence = 1;
        $preliminaries = $this->form->getState()['preliminaries'] ?? [];
        foreach ($preliminaries as $item) {
            $this->record->quotationItems()->create([
                'work_id' => $item['work_id'],
                'work_category_id' => $item['work_category_id'],
                'type' => QuotationItemType::PRELIMINARIES,
                'quantity' => 1,
                'unit_cost' => 0,
                'unit_price' => 0,
                'total' => $item['total'],
                'sequence' => $sequence++,
            ]);
        }

        $works = $this->form->getState()['works'] ?? [];
        foreach ($works as $item) {
            $details = $this->record->quotationItems()->create([
                'work_id' => $item['work_id'],
                'work_category_id' => $item['work_category_id'],
                'type' => QuotationItemType::SUB_CATEGORY,
                // 'product_id' => $item['product_id'],
                'quantity' => 1,
                'unit_cost' => 1,
                'unit_price' => 1,
                'sequence' => $sequence++,
            ]);

            $categoryMaterials = CategoryMaterial::query()
                ->where('work_category_id', $item['work_category_id'])
                ->get();

            $categoryMaterials->each(function (CategoryMaterial $categoryMaterial) use ($item, $details) {
                $this->record->quotationItems()->create([
                    'work_id' => $item['work_id'],
                    'work_category_id' => $item['work_category_id'],
                    'parent_id' => $details->id,
                    'type' => QuotationItemType::MATERIAL,
                    'product_id' => $categoryMaterial['product_id'],
                    'quantity' => $categoryMaterial['quantity'],
                    'unit_cost' => 0,
                    'unit_price' => 0,
                ]);
            });
        }
    }

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('edit', ['record' => $this->record]);
        // Check if the user can edit this record
        // $user = Filament::auth()->user();

        // if ($user->can('update', $this->record)) {
        //     return static::$resource::getUrl('edit', ['record' => $this->record]);
        // }

        return static::$resource::getUrl('view', ['record' => $this->record]);
    }
}
