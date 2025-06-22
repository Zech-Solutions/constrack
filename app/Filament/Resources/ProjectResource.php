<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ProjectManagementCluster;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
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
use Illuminate\Support\Str;


class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $cluster = ProjectManagementCluster::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make("Project Information")
                    ->columns(4)
                    ->schema([
                        Select::make('client_id')
                            ->label('Clients')
                            ->relationship('client', 'name')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull()
                            ->createOptionForm(
                                function () {
                                    return ClientResource::formSchema();
                                }
                            ),
                        TextInput::make('code')
                            ->label("Project Code")
                            ->maxLength(255)
                            ->required()
                            ->default(function(){
                                return sprintf("PRJ-%s", Str::upper(Str::random(6)));
                            }),
                        TextInput::make('name')
                            ->label("Title")
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(3),
                        Textarea::make('description')
                            ->label("Project Description")
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('contact_person')
                            ->label("Contact Person")
                            ->maxLength(255),
                        TextInput::make('contact_designation')
                            ->label("Contact Person Designation")
                            ->maxLength(255),
                        DatePicker::make('start_date'),
                        DatePicker::make('due_date'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_date')
                    ->date()
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
