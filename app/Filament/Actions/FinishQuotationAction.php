<?php

namespace App\Filament\Actions;

use App\Enums\QuotationStatus;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class FinishQuotationAction
{
    public static function make(): Action
    {
        return Action::make('finish')
            ->label('Finish')
            ->color('success')
            ->icon('heroicon-o-check')
            ->authorize('finish')
            ->form([
                Group::make()
                    ->schema([
                        TextInput::make('direct_cost')
                            ->label('Direct Cost')
                            ->readOnly()
                            ->numeric(),
                        TextInput::make('vat_cost')
                            ->label('VAT (12%)')
                            ->readOnly()
                            ->numeric(),
                        TextInput::make('total_cost')
                            ->label('Total Cost (VAT Included)')
                            ->readOnly()
                            ->numeric(),
                    ])
                    ->columns(3),
                RichEditor::make('remarks')
                    ->label('Remarks')
                    ->helperText('This will be shown in generated BOQ')
                    ->columnSpanFull(),

            ])
            ->fillForm(function ($record) {
                return [
                    'direct_cost' => $record->direct_cost,
                    'vat_cost' => $record->vat_cost,
                    'total_cost' => $record->total_cost,
                    'remarks' => $record->remarks,
                ];
            })
            ->action(function (array $data, $record) {

                // UPDATE QUOTATION STATUS
                $record->update([
                    'remarks' => $data['remarks'],
                    'status' => QuotationStatus::Pending,
                ]);
                Notification::make()
                    ->title('Quotation is pending and subject for review')
                    ->success()
                    ->send();
            })
            ->modalHeading('Finish Quotation')
            ->modalSubmitActionLabel('Save & Finish Quotation');
    }
}
