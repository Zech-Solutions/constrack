<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Enums\QuotationItemType;
use App\Filament\Resources\QuotationResource;
use App\Models\CategoryMaterial;
use App\Models\SupplierProductPrice;
use App\Models\WorkPrice;
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
        $labor_percent = $this->form->getState()['labor_percent'];

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
            $cost = $this->getWorkPriceBySupplier($item['work_category_id'], $item['supplier_id']);
            $price = $cost * (1 + $labor_percent / 100);
            $details = $this->record->quotationItems()->create([
                'work_id' => $item['work_id'],
                'work_category_id' => $item['work_category_id'],
                'type' => QuotationItemType::SUB_CATEGORY,
                'supplier_id' => $item['supplier_id'],
                'quantity' => 1,
                'unit_cost' => $cost,
                'unit_price' => $price,
                'sequence' => $sequence++,
            ]);

            $categoryMaterials = CategoryMaterial::query()
                ->where('work_category_id', $item['work_category_id'])
                ->get();

            $categoryMaterials->each(function (CategoryMaterial $categoryMaterial) use ($item, $details, $profit_percent) {
                $prices = $this->getMaterialPriceBySupplier($categoryMaterial['product_id'], $profit_percent);
                $this->record->quotationItems()->create([
                    'work_id' => $item['work_id'],
                    'work_category_id' => $item['work_category_id'],
                    'parent_id' => $details->id,
                    'type' => QuotationItemType::MATERIAL,
                    'product_id' => $categoryMaterial['product_id'],
                    'quantity' => $categoryMaterial['quantity'],
                    'supplier_id' => $prices['supplier_id'],
                    'unit_cost' => $prices['cost'],
                    'unit_price' => $prices['price'],
                ]);
            });
        }
    }

    private function getWorkPriceBySupplier($work_category_id, $supplier_id)
    {
        $workPrice = WorkPrice::query()->where('work_category_id', $work_category_id)->where('supplier_id', $supplier_id)->first();

        if (! empty($workPrice)) {
            return (float) $workPrice->price;
        }

        return 0;
    }

    private function getMaterialPriceBySupplier($product_id, $labor_percent)
    {
        $price = SupplierProductPrice::query()
            ->where('product_id', $product_id)
            ->orderByDesc('price')
            ->first();

        if (! empty($price)) {
            return [
                'supplier_id' => $price?->supplier_id,
                'cost' => $price?->price,
                'price' => $price?->price * (1 + $labor_percent / 100),
            ];
        }

        return [
            'supplier_id' => null,
            'cost' => 0,
            'price' => 0,
        ];
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
