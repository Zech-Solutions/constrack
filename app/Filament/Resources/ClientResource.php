<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->unique(table: 'clients', column: 'email', ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),

                        Select::make('type')
                            ->options([
                                'individual' => 'Individual',
                                'business' => 'Business',
                            ])
                            ->required()
                            ->live(),
                    ]),

                // Conditional Company Fields
                Section::make('Company Details')
                    ->columns(2)
                    ->visible(fn(Get $get) => $get('type') === 'business')
                    ->schema([
                        TextInput::make('company')
                            ->requiredWith('type')
                            ->maxLength(255),

                        TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                    ]),

                // Address Section
                Section::make('Address Information')
                    ->columns(2)
                    ->schema([
                        Textarea::make('address')
                            ->columnSpanFull(),
                        TextInput::make('city'),
                        TextInput::make('state'),
                        TextInput::make('postal_code'),
                        TextInput::make('country'),
                    ]),

                // Financial Section
                Section::make('Financial Settings')
                    ->schema([
                        TextInput::make('credit_limit')
                            ->numeric()
                            ->prefix('₱'),

                        Select::make('payment_terms')
                            ->options([
                                'net_15' => 'Net 15 Days',
                                'net_30' => 'Net 30 Days',
                                'cod' => 'Cash on Delivery',
                            ])
                            ->default('net_30'),
                    ])
                    ->columns(2),

                // Status Section
                Section::make('Status')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active client')
                            ->default(true),

                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Client Name')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Client $record) => $record->company),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->iconPosition('after'),

                TextColumn::make('phone')
                    ->searchable()
                    ->icon('heroicon-o-phone')
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
                    ]),

                TextColumn::make('credit_limit')
                    ->label('Credit')
                    ->money('PHP')
                    ->alignEnd()
                    ->sortable(),

                BadgeColumn::make('payment_terms')
                    ->label('Terms')
                    ->colors([
                        'warning' => 'net_15',
                        'success' => 'net_30',
                        'danger' => 'cod',
                    ]),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                TextColumn::make('created_at')
                    ->label('Since')
                    ->dateTime('M d, Y')
                    ->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
