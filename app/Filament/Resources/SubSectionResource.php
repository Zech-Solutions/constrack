<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubSectionResource\Pages;
use App\Models\Product;
use App\Models\SubSection;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubSectionResource extends Resource
{
    protected static ?string $model = SubSection::class;

    protected static ?string $navigationGroup = 'Preliminaries Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Subsection Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('description')
                            ->maxLength(255)
                            ->default(null),
                        Select::make('section_id')
                            ->label('Section')
                            ->relationship('section', 'name')
                            ->required()
                            ->preload()
                            ->searchable(),
                        TextInput::make('unit')
                            ->maxLength(255)
                            ->default(null),
                    ]),
                Section::make('Attach Raw Materials')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->columns(3)
                            ->schema([
                                Select::make('product_id')
                                    // ->relationship('product', 'name')
                                    ->label('Product')
                                    ->searchable()
                                    ->options(fn () => Product::optionsForSelect())
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            $set('unit', $product?->unit ?? null);
                                        } else {
                                            $set('unit', null);
                                        }
                                    }),
                                TextInput::make('quantity')
                                    ->numeric(),
                                TextInput::make('unit')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(fn ($state) => $state),
                            ])
                            ->default([])
                            ->minItems(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
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
            'index' => Pages\ListSubSections::route('/'),
            'create' => Pages\CreateSubSection::route('/create'),
            'edit' => Pages\EditSubSection::route('/{record}/edit'),
        ];
    }
}
