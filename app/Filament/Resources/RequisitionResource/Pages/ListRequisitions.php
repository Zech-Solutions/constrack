<?php

namespace App\Filament\Resources\RequisitionResource\Pages;

use App\Enums\RequisitionStatus;
use App\Filament\Resources\RequisitionResource;
use App\Models\Requisition;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListRequisitions extends ListRecords
{
    protected static string $resource = RequisitionResource::class;

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
                ->badge(Requisition::count()),
            'draft' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', RequisitionStatus::DRAFT))
                ->badge(Requisition::where('status', RequisitionStatus::DRAFT)->count()),
            'submitted' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', RequisitionStatus::SUBMITTED))
                ->badge(Requisition::where('status', RequisitionStatus::SUBMITTED)->count()),
            'approved' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', RequisitionStatus::APPROVED))
                ->badge(Requisition::where('status', RequisitionStatus::APPROVED)->count()),
            'rejected' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', RequisitionStatus::REJECTED))
                ->badge(Requisition::where('status', RequisitionStatus::REJECTED)->count()),
        ];
    }
}
