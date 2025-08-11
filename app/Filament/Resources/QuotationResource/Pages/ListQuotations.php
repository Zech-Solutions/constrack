<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Enums\QuotationStatus;
use App\Filament\Resources\QuotationResource;
use App\Models\Quotation;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListQuotations extends ListRecords
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->badge(Quotation::count()),
            'draft' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', QuotationStatus::DRAFT))
                ->badge(Quotation::where('status', QuotationStatus::DRAFT)->count()),
            'pending' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', QuotationStatus::PENDING))
                ->badge(Quotation::where('status', QuotationStatus::PENDING)->count()),
            'approved' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', QuotationStatus::APPROVED))
                ->badge(Quotation::where('status', QuotationStatus::APPROVED)->count()),
            'rejected' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', QuotationStatus::APPROVED))
                ->badge(Quotation::where('status', QuotationStatus::APPROVED)->count()),
        ];
    }
}
