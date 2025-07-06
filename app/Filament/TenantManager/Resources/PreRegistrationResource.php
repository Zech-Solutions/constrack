<?php

namespace App\Filament\TenantManager\Resources;

use App\Filament\TenantManager\Resources\PreRegistrationResource\Pages;
use App\Models\PreRegistration;
use App\Models\Tenant;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class PreRegistrationResource extends Resource
{
    protected static ?string $model = PreRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static bool $isScopedToTenant = false;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Pre Registered Information')
                ->description('Required fields for tenant registration.')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->placeholder('Enter name')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('email')
                        ->label('Email Address')
                        ->placeholder('example@email.com')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->suffixIcon('heroicon-o-envelope'),

                    Forms\Components\TextInput::make('contact_number')
                        ->label('Contact Number')
                        ->placeholder('09XXXXXXXXX')
                        ->tel()
                        ->suffixIcon('heroicon-o-phone'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contact_number')->label('Contact')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Signed Up At'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('convert')
                    ->label('Convert to Tenant')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (PreRegistration $record) {

                        $existingUser = User::where('email', $record->email)->first();

                        if (! $existingUser) {
                            $user = User::create([
                                'name' => $record->owner_email,
                                'email' => $record->owner_email,
                                'password' => bcrypt(strtoupper($record->owner_lastname).date('Y', strtotime($record->created_at))),
                                'role' => 'admin',
                            ]);
                        } else {
                            $user = $existingUser;
                        }

                        Tenant::create([
                            'name' => $record->name,
                            'email' => $record->email,
                            'contact' => $record->contact_number,
                        ]);

                        $record->delete();

                        Notification::make()
                            ->title('Conversion successful')
                            ->body($record->name.' has been converted to a Tenant.')
                            ->success()
                            ->send();
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
            'index' => Pages\ListPreRegistrations::route('/'),
            'create' => Pages\CreatePreRegistration::route('/create'),
            'edit' => Pages\EditPreRegistration::route('/{record}/edit'),
        ];
    }
}
