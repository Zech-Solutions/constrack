<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Enums\QuotationItemType;
use App\Filament\Actions\AcceptQuotationAction;
use App\Filament\Actions\ApproveQuotationAction;
use App\Filament\Resources\QuotationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewQuotation extends ViewRecord
{
    protected static string $view = 'filament.resources.quotations.pages.view-quotation';
    protected static string $resource = QuotationResource::class;

    public function getTitle(): string
    {
        return sprintf('View Quotation >> %s', $this->record->code);
    }

    public function mount($record): void
    {
        parent::mount($record);

        // Eager load related data to prevent N+1 queries
        $this->record->load('quotationItems.work', 'quotationItems.product', 'quotationItems.workCategory');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Quotation Information')
                ->schema([
                    TextEntry::make('status')->badge(),
                    TextEntry::make('client.name'),
                    TextEntry::make('quotation_date'),
                    TextEntry::make('direct_cost'),
                    TextEntry::make('vat_cost'),
                    TextEntry::make('total_cost'),
                    TextEntry::make('term'),
                    TextEntry::make('vat_percent'),
                    TextEntry::make('profit_percent'),
                    TextEntry::make('labor_percent'),
                ])
                ->columns(4),
        ]);
    }

    protected function getViewData(): array
    {
        // Group directly from the eager-loaded collection to avoid extra DB calls
        $groupedPreliminaries = $this->record
            ->quotationItems
            ->where('type', QuotationItemType::PRELIMINARIES)
            ->groupBy('work_id');

        $groupedSubcategories = $this->record
            ->quotationItems
            ->where('type', '!=', QuotationItemType::PRELIMINARIES)
            ->groupBy('work_id');

        return [
            'quotation' => $this->record,
            'groupedPreliminaries' => $groupedPreliminaries,
            'groupedSubcategories' => $groupedSubcategories,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            ApproveQuotationAction::make(),
            AcceptQuotationAction::make(),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
