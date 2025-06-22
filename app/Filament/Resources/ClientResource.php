<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $model = Client::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::formSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Client Name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Client $record) => $record->company),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->searchable()
                    ->copyable(),

                BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'individual',
                        'success' => 'business',
                    ])
                    ->icons([
                        'heroicon-o-user' => 'individual',
                        'heroicon-o-building-office' => 'business',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('credit_limit')
                    ->label('Credit')
                    ->money('PHP')
                    ->alignEnd()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                BadgeColumn::make('payment_terms')
                    ->label('Terms')
                    ->colors([
                        'warning' => 'net_15',
                        'success' => 'net_30',
                        'danger' => 'cod',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('created_at')
                    ->label('Since')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'individual' => 'Individuals',
                        'business' => 'Businesses',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Active Status'),

                SelectFilter::make('payment_terms')
                    ->options([
                        'net_15' => 'Net 15',
                        'net_30' => 'Net 30',
                        'cod' => 'COD',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function formSchema()
    {
        return [
            Section::make('Basic Information')
                ->columns(2)
                ->schema([
                    Hidden::make('tenant_id')
                        ->default(fn () => Filament::getTenant()?->id),

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
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-globe-alt'),

                    Textarea::make('address')
                        ->columnSpanFull(),
                ]),
            // Financial Section
            Section::make('Financial Settings')
                ->schema([
                    TextInput::make('credit_limit')
                        ->numeric()
                        ->prefix('₱')
                        ->prefixIcon('heroicon-o-currency-dollar'),

                    Select::make('payment_term')
                        ->options([
                            'net_15' => 'Net 15 Days',
                            'net_30' => 'Net 30 Days',
                            'cod' => 'Cash on Delivery',
                        ])
                        ->default('net_30')
                        ->prefixIcon('heroicon-o-clock'),
                ])
                ->columns(2),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
