<?php

namespace App\Filament\Actions;

use App\Enums\QuotationStatus;
use App\Jobs\GenerateQuotation;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ApproveQuotationAction
{
    public static function make(): Action
    {
        return Action::make('approve')
            ->label('Marked as Reviewed')
            ->color('success')
            ->icon('heroicon-o-check')
            ->authorize('approve')
            ->requiresConfirmation()
            ->action(function ($record) {
                $record->update([
                    'status' => QuotationStatus::Reviewed,
                ]);

                GenerateQuotation::dispatch($record);

                Notification::make()
                    ->title('Quotation successfully reviewed and ready to submit for Client')
                    ->success()
                    ->send();
            });
    }
}
