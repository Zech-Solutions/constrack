<?php

namespace App\Filament\Actions;

use App\Enums\ProjectStatus;
use App\Enums\QuotationStatus;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class AcceptQuotationAction
{
    public static function make(): Action
    {
        return Action::make('accept')
            ->label('Mark as Client Accepted Quotation')
            ->color('success')
            ->icon('heroicon-o-check')
            ->authorize('accept')
            ->form([
                Group::make()
                    ->schema([
                        DatePicker::make('client_po_date')
                            ->label('Client PO Date')
                            ->required(),
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required(),
                        DatePicker::make('due_date')
                            ->label('Due Date')
                            ->required(),
                        TextInput::make('direct_cost')
                            ->label('Direct Cost')
                            ->numeric()
                            ->required(),
                        TextInput::make('vat_cost')
                            ->label('Vat Cost')
                            ->numeric()
                            ->required(),
                        TextInput::make('total_cost')
                            ->label('Total Cost')
                            ->numeric()
                            ->required(),
                        Textarea::make('remarks')
                            ->label('Remarks')->rows(3)->maxLength(500)
                            ->columnSpanFull(),
                        FileUpload::make('client_attachments')
                            ->label('Client Attachments')
                            ->helperText('Upload one or more PDF files from the client.')
                            ->columnSpanFull()
                            ->directory('client-attachments')
                            ->acceptedFileTypes(['application/pdf'])
                            ->multiple()
                            ->maxSize(5120) // 5 MB per file
                            ->downloadable()
                            ->openable(),
                    ])
                    ->columns(3),
            ])
            ->fillForm(function ($record) {
                return [
                    'direct_cost' => $record->direct_cost,
                    'vat_cost' => $record->vat_cost,
                    'total_cost' => $record->total_cost,
                ];
            })
            ->action(function (array $data, $record) {

                // UPDATE QUOTATION STATUS
                $record->update([
                    'status' => QuotationStatus::Accepted,
                ]);

                // Create Project
                Project::create([
                    'tenant_id' => $record->tenant_id,
                    'client_id' => $record->client_id,
                    'quotation_id' => $record->id,
                    'code' => $record->code,
                    'title' => $record->title,
                    'description' => $record->description,
                    'client_po_date' => $data['client_po_date'],
                    'start_date' => $data['start_date'],
                    'due_date' => $data['due_date'],
                    'vat_cost' => $data['vat_cost'],
                    'direct_cost' => $data['direct_cost'],
                    'total_cost' => $data['total_cost'],
                    'client_attachments' => $data['client_attachments'],
                    'remarks' => $data['remarks'] ?? null,
                    'status' => ProjectStatus::InProgress,
                ]);

                Notification::make()
                    ->title('Quotation approved & project creation started')
                    ->success()
                    ->send();
            })
            ->modalHeading('Accept Quotation')
            ->modalSubmitActionLabel('Accept & Initiate Project');
    }
}
