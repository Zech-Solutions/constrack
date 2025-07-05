<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enums\SupplierType;
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
use Illuminate\Database\Eloquent\Builder;

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
                        ->relationship('supplierPrices')
                        ->label('Prices from Suppliers')
                        ->schema([
                            Select::make('supplier_id')
                                ->relationship(
                                    name: 'supplier',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: fn (Builder $query) => $query
                                        ->where('type', SupplierType::MATERIAL)
                                )
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
}
