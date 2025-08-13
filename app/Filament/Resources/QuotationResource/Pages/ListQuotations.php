<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Enums\QuotationStatus;
use App\Filament\Resources\QuotationResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

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
        $tabs = [
            'all' => Tab::make()
                ->label('All')
                ->badge($this->getTableQuery()->count()),
        ];

        foreach (QuotationStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make()
                ->label($status->getLabel())
                ->badge(
                    $this->getTableQuery()
                        ->where('status', $status->value)
                        ->count()
                )
                ->modifyQueryUsing(fn ($query) => $query->where('status', $status->value))
                ->badgeColor($status->getColor());
        }

        return $tabs;
    }
}
