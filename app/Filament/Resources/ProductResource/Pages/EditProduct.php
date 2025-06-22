<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Products')
                    ->tabs([
                        $this->tabBasicInfo(),
                        $this->tabSupplierPrices(),
                        $this->tabSupplierPriceHistory(),
                    ]),
            ])
            ->columns('full');
    }

    public function tabBasicInfo()
    {
        return
            Tab::make('Basic Information')
                ->schema([
                    TextInput::make('name')
                        ->label('Product Name')
                        ->required()
                        ->maxLength(255),
                    Select::make('product_category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->required()
                        ->preload()
                        ->searchable(),
                    TextInput::make('code')
                        ->label('Product Code')
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    TextInput::make('unit')
                        ->label('Product Unit')
                        ->helperText('kg, g, m')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->label('Description')
                        ->nullable()
                        ->columnSpanFull(),
                ]);
    }

    public function tabSupplierPrices()
    {
        return
            Tab::make('Supplier Prices')
                ->schema([
                    TableRepeater::make('supplierPrices')
                        ->headers([
                            Header::make('Supplier'),
                            Header::make('Price'),
                        ])
                        ->renderHeader(false)
                        ->relationship('supplierPrices')
                        ->label('Prices from Suppliers')
                        ->schema([
                            Select::make('supplier_id')
                                ->relationship('supplier', 'name')
                                ->label('Supplier')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                            TextInput::make('price')
                                ->label('Price')
                                ->required()
                                ->numeric()
                                ->prefix('₱'),
                        ])
                        ->columnSpan('full'),
                ]);
    }

    public function tabSupplierPriceHistory()
    {
        return Tab::make('Price History')
            ->schema([
                TableRepeater::make('supplierPricesHistory')
                    ->headers([
                        Header::make('Supplier'),
                        Header::make('Current Price'),
                        Header::make('Previous Price'),
                        Header::make('Date'),
                    ])
                    ->deletable(false)
                    ->relationship('supplierPricesHistory')
                    ->label('Prices from Suppliers')
                    ->disabled()
                    ->schema([
                        TextInput::make('supplier.name')
                            ->label('Supplier')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn ($state, $record) => $record->supplier?->name ?? '-'),
                        TextInput::make('price')
                            ->label('Current Price')
                            ->numeric()
                            ->prefix('₱')
                            ->disabled(),
                        TextInput::make('previous_price')
                            ->label('Current Price')
                            ->numeric()
                            ->prefix('₱')
                            ->disabled(),
                        TextInput::make('date')
                            ->label('Date Adjusted')
                            ->prefixIcon('heroicon-m-calendar')
                            ->disabled(),
                    ])
                    ->columnSpan('full'),
            ]);
    }
}
