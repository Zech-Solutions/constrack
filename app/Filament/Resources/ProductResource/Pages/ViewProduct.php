<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\SupplierProductPrice;
use App\Models\SupplierProductPriceHistory;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Product Details')->tabs([
                    $this->tabBasicInfo(),
                    $this->tabSupplierPrices(),
                    $this->tabSupplierPriceHistory(),
                ])
                    ->columnSpanFull(),
            ]);
    }

    public function tabBasicInfo()
    {
        return
            Tab::make('Basic Information')
                ->schema([
                    TextEntry::make('name')
                        ->label('Product Name'),
                    TextEntry::make('category.name')
                        ->label('Category Name'),
                    TextEntry::make('code')
                        ->label('Product code'),
                    TextEntry::make('unit')
                        ->label('Product Unit'),
                    TextEntry::make('description')
                        ->label('Description')
                        ->columnSpanFull(),
                ])
                ->columns(4);
    }

    public function tabSupplierPrices()
    {
        return Tab::make('Supplier Prices')
            ->schema([
                Grid::make()
                    ->schema([
                        TextEntry::make('header_supplier')->label('Supplier')->state(''),
                        TextEntry::make('header_price')->label('Price')->state(''),
                        TextEntry::make('header_date')->label('Date Adjusted')->state(''),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
                ...$this->record->supplierPrices()
                    ->latest()
                    ->get()
                    ->map(function (SupplierProductPrice $price) {
                        return Grid::make(['default' => 3])
                            ->schema([
                                TextEntry::make('supplier')->state($price->supplier->name ?? '-')->label(false),
                                TextEntry::make('price')->state('₱'.number_format($price->price, 2))->hiddenLabel(),
                                TextEntry::make('date')->state($price->created_at->format('M d, Y H:i A'))->hiddenLabel(),
                            ])
                            ->extraAttributes(['class' => 'border-b dark:border-gray-700']);
                    })
                    ->all(),

            ])
            ->extraAttributes(['class' => 'no-grid-header']);
    }

    public function tabSupplierPriceHistory()
    {
        return Tab::make('Price History')
            ->schema([
                Grid::make()
                    ->schema([
                        TextEntry::make('header_supplier')->label('Supplier')->state(''),
                        TextEntry::make('header_price')->label('Price')->state(''),
                        TextEntry::make('header_previous_price')->label('Previous Price')->state(''),
                        TextEntry::make('header_date')->label('Date Adjusted')->state(''),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
                ...$this->record->supplierPricesHistory()
                    ->latest()
                    ->get()
                    ->map(function (SupplierProductPriceHistory $price) {
                        return Grid::make(['default' => 4])
                            ->schema([
                                TextEntry::make('supplier')->state($price->supplier->name ?? '-')->label(false),
                                TextEntry::make('price')->state('₱'.number_format($price->price, 2))->hiddenLabel(),
                                TextEntry::make('previous_price')->state('₱'.number_format($price->previous_price, 2))->hiddenLabel(),
                                TextEntry::make('created_at')->state($price->created_at->format('M d, Y H:i A'))->hiddenLabel(),
                            ])
                            ->extraAttributes(['class' => 'border-b dark:border-gray-700']);
                    })
                    ->all(),

            ])
            ->extraAttributes(['class' => 'no-grid-header']);
    }
}
