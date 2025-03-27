<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Products;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $modelLabel = 'Products';


    // protected static ?string $cluster = Products::class;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->columns(1)
                    ->schema([
                        TextInput::make('name')
                            ->label('Product Name')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Description')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),

                Section::make('Pricing & Inventory')
                    ->columns(2)
                    ->schema([
                        TextInput::make('price')
                            ->label('Price (₱)')
                            ->prefix('₱')
                            ->numeric()
                            ->required()
                            ->rules(['numeric', 'min:0'])
                            ->maxValue(99999999.99),

                        TextInput::make('stock')
                            ->label('Stock Quantity')
                            ->numeric()
                            ->required()
                            ->rules(['integer', 'min:0']),

                        TextInput::make('sku')
                            ->label('SKU')
                            ->unique('products', 'sku', ignoreRecord: true)
                            ->required()
                            ->maxLength(50)
                            ->columnSpanFull(),
                    ]),

                Section::make('Relationships')
                    ->columns(2)
                    ->schema([
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->required()
                            ->preload()
                            ->searchable(),

                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->relationship('supplier', 'name')
                            ->required()
                            ->preload()
                            ->searchable(),
                    ]),

                Section::make('Media & Status')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image')
                            ->label('Product Image')
                            ->image()
                            ->directory('products')
                            ->nullable(),

                        Toggle::make('is_active')
                            ->label('Active?')
                            ->inline(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->circular()
                    ->width(50)
                    ->height(50)
                    ->disk('public'),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label('Name'),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('stock')
                    ->sortable()
                    ->searchable()
                    ->label('Stock'),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduct::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
