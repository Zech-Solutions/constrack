<?php

namespace App\Filament\Resources;

use App\Enums\RequisitionStatus;
use App\Filament\Clusters\ProcurementManagementCluster;
use App\Filament\Resources\RequisitionResource\Pages;
use App\Models\Project;
use App\Models\Requisition;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RequisitionResource extends Resource
{
    protected static ?string $model = Requisition::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $cluster = ProcurementManagementCluster::class;

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Requisition Information')
                    ->columns(4)
                    ->schema([
                        TextInput::make('code')
                            ->label('Requisition Code')
                            ->maxLength(255)
                            ->required()
                            ->default(function () {
                                return sprintf('REQ-%s', Str::upper(Str::random(7)));
                            }),
                        Select::make('project_id')
                            ->relationship(
                                name: 'project',
                            )
                            ->getOptionLabelFromRecordUsing(fn (Project $record) => "{$record->code} [{$record->name} - {$record->client->name}]")
                            ->required()
                            ->preload()
                            ->searchable()
                            ->columnSpan(2),
                        Forms\Components\DatePicker::make('requisition_date')
                            ->required(),
                        Forms\Components\Textarea::make('remarks')
                            ->autocomplete('off')
                            ->maxLength(255)
                            ->default(null)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('requisition_date')
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.client.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('requestedBy.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('approvedBy.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rejectedBy.name')
                    ->sortable(),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->visible(fn ($record) => $record->status === RequisitionStatus::DRAFT),
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
            'index' => Pages\ListRequisitions::route('/'),
            'create' => Pages\CreateRequisition::route('/create'),
            'edit' => Pages\EditRequisition::route('/{record}/edit'),
            'view' => Pages\ViewRequisition::route('/{record}'),
            'approve' => Pages\ApproveRequisition::route('/{record}/approve'),
        ];
    }
}
