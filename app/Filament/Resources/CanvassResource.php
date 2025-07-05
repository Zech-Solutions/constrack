<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ProcurementManagementCluster;
use App\Filament\Resources\CanvassResource\Pages;
use App\Models\Canvass;
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

class CanvassResource extends Resource
{
    protected static ?string $model = Canvass::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $cluster = ProcurementManagementCluster::class;

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Canvassing Information')
                    ->columns(4)
                    ->schema([
                        TextInput::make('code')
                            ->label('Canvass Code')
                            ->maxLength(255)
                            ->required()
                            ->default(function () {
                                return sprintf('REQ-%s', Str::upper(Str::random(7)));
                            }),
                        Select::make('requisition_id')
                            ->relationship(
                                name: 'requisition',
                            )
                            ->getOptionLabelFromRecordUsing(fn (Requisition $record) => "{$record->code} [{$record->project->name}]")
                            ->required()
                            ->preload()
                            ->searchable()
                            ->columnSpan(2),
                        Forms\Components\DatePicker::make('canvass_date')
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
                Tables\Columns\TextColumn::make('requisition_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('canvassed_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rejected')
                    ->numeric()
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
            'index' => Pages\ListCanvasses::route('/'),
            'create' => Pages\CreateCanvass::route('/create'),
            'edit' => Pages\EditCanvass::route('/{record}/edit'),
        ];
    }
}
