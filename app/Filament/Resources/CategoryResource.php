<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationParentItem = 'Products';

    protected static ?string $navigationGroup = 'Master Data';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('parent_id')
                    ->label('Parent Category')
                    ->relationship('parent', 'name')
                    ->placeholder('Select a parent category')
                    ->nullable(),
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('slug')
                    ->label('Slugs')
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->nullable(),
                Checkbox::make('is_active')
                    ->label('Active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('is_active')
                    ->label('Active')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return $state ? 'Yes' : 'No';
                    }),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
