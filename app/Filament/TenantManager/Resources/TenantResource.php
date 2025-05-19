<?php

namespace App\Filament\TenantManager\Resources;

use App\Filament\TenantManager\Resources\TenantResource\Pages;
use App\Filament\TenantManager\Resources\TenantResource\RelationManagers;
use App\Filament\TenantManager\Resources\TenantResource\RelationManagers\UsersRelationManager;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make("name")
                            ->label("Tenant Name")
                            ->required(),
                        TextInput::make("email")
                            ->label("Email")
                            ->email()
                            ->required(),
                        TextInput::make("contact")
                            ->label("Contact")
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("name")
                    ->label("Tenant Name")
                    ->searchable(),
                TextColumn::make("email")
                    ->label("Tenant Email")
                    ->searchable(),
                TextColumn::make("Contact")
                    ->label("Contact")
                    ->searchable()
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
            UsersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
