<?php

namespace App\Filament\Resources;

use App\Enums\SupplierType;
use App\Filament\Clusters\ScopeOfWorkCluster;
use App\Filament\Resources\WorkCategoryResource\Pages;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\WorkCategory;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkCategoryResource extends Resource
{
    protected static ?string $model = WorkCategory::class;

    protected static ?string $cluster = ScopeOfWorkCluster::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Sub Category';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->columns(12)
                    ->schema([
                        Grid::make()
                            ->columnSpan(5)
                            ->schema([

                                Section::make('Work Category Information')
                                    ->columns(2)
                                    ->columnSpan(6)
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        Select::make('work_id')
                                            ->label('Work Category')
                                            ->relationship('work', 'name')
                                            ->required()
                                            ->preload()
                                            ->searchable()
                                            ->columnSpanFull(),
                                        TextInput::make('description')
                                            ->maxLength(255)
                                            ->default(null)
                                            ->columnSpanFull(),
                                        TextInput::make('unit')
                                            ->maxLength(255)
                                            ->default('lot'),
                                        TextInput::make('quantity')
                                            ->numeric()
                                            ->default(1),
                                        TextInput::make('amount')
                                            ->numeric()
                                            ->default(null),
                                        Toggle::make('is_default')
                                            ->label('Set default for Quotation?')
                                            ->inline(false),
                                    ]),

                            ]),
                        Grid::make()
                            ->columns(6)
                            ->columnSpan(7)
                            ->schema([
                                Section::make('Attach Raw Materials (Main Scope)')
                                    ->description(
                                        fn (Get $get) => 'You have '.count($get('materials') ?? []).' material item(s).'
                                    )
                                    ->collapsible(true)
                                    ->columnSpan(6)
                                    ->schema([
                                        TableRepeater::make('materials')
                                            ->headers([
                                                Header::make('product'),
                                                Header::make('quantity')->width('20%'),
                                            ])
                                            ->relationship()
                                            ->schema([
                                                Select::make('product_id')
                                                    ->label('Product')
                                                    ->searchable()
                                                    ->relationship(
                                                        name: 'product',
                                                    )
                                                    ->getOptionLabelFromRecordUsing(fn (Product $record) => "[{$record->unit}] - {$record->name}")
                                                    ->live()
                                                    ->required()
                                                    ->preload()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                                TextInput::make('quantity')
                                                    ->numeric(),
                                            ])
                                            ->default([])
                                            ->minItems(0),
                                    ]),

                                Section::make('Attach Sub Contractor Fee')
                                    ->description(
                                        fn (Get $get) => 'You have '.count($get('prices') ?? []).' subcontractor prices.'
                                    )
                                    ->collapsible(true)
                                    ->columnSpan(6)
                                    ->schema([
                                        TableRepeater::make('prices')
                                            ->headers([
                                                Header::make('supplier'),
                                                Header::make('price')->width('20%'),
                                            ])
                                            ->relationship()
                                            ->schema([
                                                Select::make('supplier_id')
                                                    ->label('Supplier')
                                                    ->searchable()
                                                    ->relationship(
                                                        name: 'supplier',
                                                        modifyQueryUsing: fn (Builder $query) => $query
                                                            ->where('type', SupplierType::SUBCON)
                                                    )
                                                    ->getOptionLabelFromRecordUsing(fn (Supplier $record) => "{$record->name}")
                                                    ->live()
                                                    ->required()
                                                    ->preload()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                                TextInput::make('price')
                                                    ->numeric(),
                                            ])
                                            ->default([])
                                            ->minItems(0),
                                    ]),

                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('work.name')
                    ->label('Scope of Work')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListWorkCategories::route('/'),
            'create' => Pages\CreateWorkCategory::route('/create'),
            'edit' => Pages\EditWorkCategory::route('/{record}/edit'),
        ];
    }
}
