<?php

namespace App\Filament\Resources;

use App\Enums\QuotationStatus;
use App\Enums\WorkType;
use App\Filament\Resources\QuotationResource\Pages;
use App\Models\Quotation;
use App\Models\Work;
use App\Models\WorkCategory;
use App\Models\WorkPrice;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Transaction';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Quotation Information')
                    ->columns(4)
                    ->schema([
                        TextInput::make('code')
                            ->unique(ignoreRecord: true)
                            ->label('Quotation Code')
                            ->maxLength(255)
                            ->required()
                            ->default(function () {
                                return sprintf('BOQ-%s', now()->format('mdy'));
                            }),
                        Select::make('client_id')
                            ->label('Clients')
                            ->relationship('client', 'name')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->columnSpan(2)
                            ->createOptionForm(
                                function () {
                                    return ClientResource::formSchema();
                                }
                            ),
                        Forms\Components\DatePicker::make('quotation_date')
                            ->required(),
                        Forms\Components\TextInput::make('term')
                            ->label('Terms of Payment')
                            ->required()
                            ->numeric()
                            ->default(30),
                        Forms\Components\TextInput::make('vat_percent')
                            ->required()
                            ->numeric()
                            ->default(12),
                        Forms\Components\TextInput::make('profit_percent')
                            ->required()
                            ->numeric()
                            ->default(35),
                        Forms\Components\TextInput::make('labor_percent')
                            ->required()
                            ->numeric()
                            ->default(35),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->autocomplete('off')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->autocomplete('off')
                            ->maxLength(255)
                            ->default(null)
                            ->columnSpanFull(),
                    ]),

                Section::make('Preliminaries')
                    ->schema([
                        Repeater::make('preliminaries')
                            ->schema([
                                Select::make('work_id')
                                    ->label('Scope')
                                    ->searchable()
                                    ->options(fn () => Work::optionsForSelect(WorkType::PRELIMINARIES))
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(fn (Set $set) => $set('work_category_id', null)),

                                Select::make('work_category_id')
                                    ->label('Sub Category')
                                    ->searchable()
                                    ->required()
                                    ->options(function (Get $get) {
                                        $work_id = $get('work_id');

                                        return WorkCategory::optionsForSelect($work_id);
                                    })
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                TextInput::make('total')
                                    ->label('Amount')
                                    ->required()
                                    ->numeric(),
                            ])
                            ->columns(3)
                            ->default(function () {
                                return WorkCategory::where('is_default', true)
                                    ->get()
                                    ->map(function ($category) {
                                        return [
                                            'work_id' => $category->work_id,
                                            'work_category_id' => $category->id,
                                            'total' => $category->amount,
                                        ];
                                    })
                                    ->toArray();
                            }),
                    ])
                    ->collapsible(),
                Section::make('Scope of Works')
                    ->schema([
                        Repeater::make('works')
                            ->label('Main Works')
                            ->schema([
                                Select::make('work_id')
                                    ->label('Scope')
                                    ->searchable()
                                    ->options(fn () => Work::optionsForSelect(WorkType::MAIN_SCOPE))
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(fn (Set $set) => $set('work_category_id', null)),

                                Select::make('work_category_id')
                                    ->label('Sub Category')
                                    ->searchable()
                                    ->required()
                                    ->options(function (Get $get) {
                                        $work_id = $get('work_id');

                                        return WorkCategory::optionsForSelect($work_id);
                                    })
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                Select::make('supplier_id')
                                    ->label('Subcontractor')
                                    ->searchable()
                                    ->required()
                                    ->options(function (Get $get) {
                                        $work_category_id = $get('work_category_id');

                                        return WorkPrice::supplierOptionsWithPrice($work_category_id);
                                    })
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quotation_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('term')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->getLabel())
                    ->color(fn ($state) => $state->getColor()),
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
                // Tables\Actions\EditAction::make()->visible(fn ($record) => $record->status === QuotationStatus::Draft),
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-s-cloud-arrow-down')
                    ->visible(fn (Quotation $record): bool => ! is_null($record->filename))
                    ->action(function (Quotation $record) {
                        return response()->download(storage_path('app/public/quotations/').$record->filename);
                    }),
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
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
            'view' => Pages\ViewQuotation::route('/{record}'),
        ];
    }
}
