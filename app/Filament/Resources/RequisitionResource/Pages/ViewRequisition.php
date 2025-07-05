<?php

namespace App\Filament\Resources\RequisitionResource\Pages;

use App\Filament\Resources\RequisitionResource;
use App\Models\RequisitionItem;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewRequisition extends ViewRecord
{
    protected static string $resource = RequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('reject')
                ->label('Reject')
                ->color('danger')
                ->icon('heroicon-o-check-circle')
                ->visible(fn ($record) => $record->status === 'SUBMITTED')
                ->requiresConfirmation()
                ->action(function () {

                    $this->record->status = 'REJECTED';
                    $this->record->rejected_by = Auth::user();
                    $this->record->save();

                    Notification::make()
                        ->title('Requisition marked as rejected.')
                        ->success()
                        ->send();

                    return redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),
            Actions\Action::make('approveRequest')
                ->label('Take Action')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    return redirect($this->getResource()::getUrl('approve', ['record' => $this->record]));
                }),
            Actions\DeleteAction::make()
                ->visible(fn ($record) => $record->status === 'DRAFT'),

        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make(12)
                    ->schema([
                        Section::make('Requisition Information')
                            ->schema($this->getBasicInfo())
                            ->columns(2)
                            ->columnSpan(4),

                        Section::make('Requisition Items')
                            ->schema($this->getRequisitionItems())
                            ->columnSpan(8)
                            ->extraAttributes(['class' => 'no-grid-header']),
                    ]),
            ]);
    }

    public function getBasicInfo()
    {
        return [
            TextEntry::make('code')
                ->label('Requisition Code'),
            TextEntry::make('status')
                ->label('Status'),
            TextEntry::make('project.name')
                ->label('Project')
                ->columnSpanFull(),
            TextEntry::make('requisition_date')
                ->columnSpanFull(),
            TextEntry::make('requestedBy.name')
                ->label('Requested By')
                ->columnSpanFull(),
            TextEntry::make('remarks')
                ->default(null)
                ->columnSpanFull(),
        ];
    }

    public function getRequisitionItems()
    {
        $headerIndex = 0;

        return [
            ...$this->record->items()
                ->latest()
                ->get()
                ->map(function (RequisitionItem $item) use (&$headerIndex) {
                    $headerIndex++;

                    return Grid::make(['default' => 3])
                        ->schema([
                            TextEntry::make('product')->state($item->product->name ?? '-')->hiddenLabel($headerIndex > 1 ? true : false),
                            TextEntry::make('Requested Quantity')->state($item->requested_qty ?? 0)->hiddenLabel($headerIndex > 1 ? true : false),
                            TextEntry::make('Approved Quantity')->state($item->quantity ?? 0)->hiddenLabel($headerIndex > 1 ? true : false),
                            // TextEntry::make('price')->state('₱' . number_format($price->price, 2))->hiddenLabel($headerIndex > 1 ? true : false),
                            // TextEntry::make('date')->state($price->created_at->format('M d, Y H:i A'))->hiddenLabel($headerIndex > 1 ? true : false),
                        ])
                        ->extraAttributes(['class' => 'border-b dark:border-gray-700']);
                })
                ->all(),

        ];
    }
}
