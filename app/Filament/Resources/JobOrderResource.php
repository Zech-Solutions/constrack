<?php

namespace App\Filament\Resources;

use App\Enums\QuotationItemType;
use App\Enums\SupplierType;
use App\Filament\Clusters\ProjectManagementCluster;
use App\Filament\Resources\JobOrderResource\Pages;
use App\Models\JobOrder;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class JobOrderResource extends Resource
{
    protected static ?string $model = JobOrder::class;

    protected static ?string $cluster = ProjectManagementCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Joborder Information')
                    ->columns(4)
                    ->schema([
                        TextInput::make('code')
                            ->label('Joborder Code')
                            ->maxLength(255)
                            ->required()
                            ->default(function () {
                                return sprintf('PRJ-%s', Str::upper(Str::random(7)));
                            }),
                        DatePicker::make('jo_date')
                            ->label('Joborder Date')
                            ->required(),
                        DatePicker::make('start_date')
                            ->required(),
                        DatePicker::make('due_date')
                            ->required(),
                        Select::make('client_id')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('project_id')
                            ->label('Project')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->live()
                            ->options(function (Get $get) {
                                $client_id = $get('client_id');

                                return Project::optionsForSelect($client_id);
                            }),
                        Select::make('quotation_id')
                            ->relationship(
                                name: 'quotation',
                                modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                    ->where('project_id', $get('project_id'))
                            )
                            ->getOptionLabelFromRecordUsing(fn (Quotation $record) => "{$record->code}")
                            ->required()
                            ->preload()
                            ->searchable(),
                        Select::make('supplier_id')
                            ->label('Subcontractor')
                            ->searchable()
                            ->relationship(
                                name: 'supplier',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query
                                    ->where('type', SupplierType::SUBCON)
                            )
                            ->preload()
                            ->required(),
                        Select::make('work_category_id')
                            ->label('Scope of Work')
                            ->options(function (Get $get) {
                                return QuotationItem::options($get('quotation_id'));
                            })
                            ->getOptionLabelFromRecordUsing(fn (QuotationItem $record) => "{$record->workCategory->name}")
                            ->required()
                            ->preload()
                            ->live()
                            ->columnSpan(2)
                            ->searchable()
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                $item = QuotationItem::query()
                                    ->where('quotation_id', $get('quotation_id'))
                                    ->where('work_category_id', $state)
                                    ->where('type', QuotationItemType::SUB_CATEGORY)
                                    ->first();

                                if ($item) {
                                    $set('labor', $item->labor_fee);
                                    $set('amount', $item->total);
                                }

                            }),
                        TextInput::make('labor')
                            ->required()
                            ->reactive()
                            ->numeric(),
                        TextInput::make('amount')
                            ->label('Billing Amount')
                            ->reactive()
                            ->required()
                            ->numeric(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jo_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completion_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
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
            'index' => Pages\ListJobOrders::route('/'),
            'create' => Pages\CreateJobOrder::route('/create'),
            'edit' => Pages\EditJobOrder::route('/{record}/edit'),
        ];
    }
}
