<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\Layout\Split;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->prefixIcon('heroicon-o-user')
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->prefixIcon('tabler-mail')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->required()
                    ->prefixIcon('tabler-phone')
                    ->maxLength(255),
                TextInput::make('address')
                    ->required()
                    ->prefixIcon('tabler-location')
                    ->maxLength(255),
                Select::make('category')
                    ->required()
                    ->options(fn() => \App\Models\Category::where('is_active', true)->pluck('name', 'id')->toArray()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    TextColumn::make('name')
                        ->label('Name')
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('email')
                        ->label('Email Address')
                        ->searchable()
                        ->visibleFrom('md')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('phone')
                        ->label('Phone Number')
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('address')
                        ->label('Address')
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            // 'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
