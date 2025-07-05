<?php

namespace App\Filament\Resources\RequisitionResource\Pages;

use App\Enums\RequisitionStatus;
use App\Filament\Resources\RequisitionResource;
use App\Models\Product;
use App\Models\Project;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class ApproveRequisition extends EditRecord
{
    protected static string $resource = RequisitionResource::class;

    protected function getFormActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return sprintf('Edit Requisition >> %s', $this->record->code);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('saveAndFinish')
                ->label('Save & Approve')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->before(function () {
                    $this->form->getState();
                })
                ->action(function () {

                    $this->record->status = RequisitionStatus::APPROVED;
                    $this->record->approved_by = Auth::user()->id;
                    $this->record->save();

                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

                    Notification::make()
                        ->title('Requisition marked as approved.')
                        ->success()
                        ->send();

                    return redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(12)
                    ->schema([
                        Section::make('Requisition Information')
                            ->schema($this->getBasicInfo())
                            ->columnSpan(4),

                        Section::make('Requisition Items')
                            ->schema($this->getRequisitionItems())
                            ->columnSpan(8),
                    ]),
            ]);
    }

    public function getBasicInfo()
    {
        return [
            TextInput::make('code')
                ->label('Requisition Code')
                ->maxLength(255)
                ->readOnly()
                ->columnSpanFull(),
            Select::make('project_id')
                ->relationship(
                    name: 'project',
                )
                ->getOptionLabelFromRecordUsing(fn (Project $record) => "{$record->code} [{$record->name} - {$record->client->name}]")
                ->disabled()
                ->preload()
                ->searchable()
                ->columnSpanFull(),
            DatePicker::make('requisition_date')
                ->readOnly()
                ->columnSpanFull(),
            Textarea::make('remarks')
                ->autocomplete('off')
                ->maxLength(255)
                ->default(null)
                ->columnSpanFull(),
        ];
    }

    public function getRequisitionItems()
    {
        return [
            TableRepeater::make('items')
                ->headers([
                    Header::make('Material')
                        ->width('60%'),
                    Header::make('Requested Qty'),
                    Header::make('Approve Qty'),
                ])
                ->relationship(
                    name: 'items',
                )
                ->schema([
                    Select::make('product_id')
                        ->relationship('product')
                        ->getOptionLabelFromRecordUsing(fn (Product $record) => "[{$record->unit} - {$record->code}]  {$record->name}")
                        ->required()
                        ->preload()
                        ->searchable()
                        ->columnSpan(2)
                        ->disabled()
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                    TextInput::make('requested_qty')
                        ->readOnly()
                        ->numeric(),
                    TextInput::make('quantity')
                        ->numeric()
                        ->default(0)
                        ->rule(function (\Filament\Forms\Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $requested = $get('requested_qty');

                                if ($requested !== null && $value > $requested) {
                                    $fail('Approved quantity cannot be greater than requested quantity.');
                                }
                            };
                        }),
                ])
                ->deletable(false)
                ->addable(false)
                ->defaultItems(0)
                ->columns(6),
        ];
    }
}
