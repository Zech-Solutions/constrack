<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ScopeOfWorkCluster;
use App\Filament\Resources\WorkCategoryResource\Pages;
use App\Filament\Resources\WorkCategoryResource\RelationManagers;
use App\Models\Product;
use App\Models\WorkCategory;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkCategoryResource extends Resource
{
    protected static ?string $model = WorkCategory::class;

    protected static ?string $cluster = ScopeOfWorkCluster::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return "Sub Category";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make("Work Category Information")
                    ->columns(4)
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
                            ->label("Set default for Quotation?")
                            ->inline(false),
                    ]),
                Section::make("Attach Raw Materials (Main Scope)")
                    ->schema([
                        Repeater::make("materials")
                            ->relationship()
                            ->columns(3)
                            ->schema([
                                Select::make('product_id')
                                    ->label("Product")
                                    ->searchable()
                                    ->options(fn() => Product::optionsForSelect())
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            $set('unit', $product?->unit ?? null);
                                        } else {
                                            $set('unit', null);
                                        }
                                    })
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                TextInput::make('quantity')
                                    ->numeric(),
                                TextInput::make('unit')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->default(fn($state) => $state),
                            ])
                            ->default([])
                            ->minItems(0)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('work.name')
                    ->label("Scope of Work")
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
