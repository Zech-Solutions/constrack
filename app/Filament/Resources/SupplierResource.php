<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-user'),

                        TextInput::make('email')
                            ->email()
                            ->unique(table: 'clients', column: 'email', ignoreRecord: true)
                            ->prefixIcon('heroicon-o-envelope')
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(20)
                            ->prefixIcon('heroicon-o-phone'),

                        TextInput::make('tin')
                            ->maxLength(20)
                            ->prefixIcon('heroicon-o-phone'),
                    ]),

                // Address Section
                Section::make('Address Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('address')
                            ->columnSpanFull()
                            ->prefixIcon('heroicon-o-map-pin'),

                        TextInput::make('city')
                            ->prefixIcon('heroicon-o-building-library'),

                        TextInput::make('state')
                            ->prefixIcon('heroicon-o-map'),

                        TextInput::make('postal_code')
                            ->prefixIcon('heroicon-o-document-text'),

                        TextInput::make('country')
                            ->prefixIcon('heroicon-o-flag'),
                    ]),
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
