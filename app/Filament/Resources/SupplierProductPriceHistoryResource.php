<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierProductPriceHistoryResource\Pages;
use App\Filament\Resources\SupplierProductPriceHistoryResource\RelationManagers;
use App\Models\SupplierProductPriceHistory;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierProductPriceHistoryResource extends Resource
{
    protected static ?string $model = SupplierProductPriceHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static ?string $label = "Product Price History";

    public static ?string $tenantOwnershipRelationshipName = null;

    protected static bool $isScopedToTenant = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->label('Product')
                    ->disabled(),

                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->label('Supplier')
                    ->disabled(),

                TextInput::make('price')
                    ->prefix('₱')
                    ->numeric()
                    ->disabled(),

                DatePicker::make('date')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('previous_price')
                    ->label('Previous Price')
                    ->money('PHP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Updated Price')
                    ->money('PHP')
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->previous_price === null) {
                            return '₱' . number_format($state, 2);
                        }

                        if ($state > $record->previous_price) {
                            return '🔼 ₱' . number_format($state, 2);
                        } elseif ($state < $record->previous_price) {
                            return '🔽 ₱' . number_format($state, 2);
                        } else {
                            return '₱' . number_format($state, 2);
                        }
                    })
                    ->color(function ($state, $record) {
                        if ($record->previous_price === null) {
                            return null;
                        }

                        return match (true) {
                            $state > $record->previous_price => 'success', // green
                            $state < $record->previous_price => 'danger',  // red
                            default => 'gray',                             // no change
                        };
                    }),


                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Logged At')
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListSupplierProductPriceHistories::route('/'),
            'create' => Pages\CreateSupplierProductPriceHistory::route('/create'),
            'edit' => Pages\EditSupplierProductPriceHistory::route('/{record}/edit'),
        ];
    }
}
