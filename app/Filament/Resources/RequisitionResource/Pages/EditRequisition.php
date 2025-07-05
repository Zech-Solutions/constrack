<?php

namespace App\Filament\Resources\RequisitionResource\Pages;

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

class EditRequisition extends EditRecord
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
            Actions\Action::make('save')
                ->label('Save')
                ->color('primary')
                ->icon('heroicon-o-pencil-square')
                ->action(function () {
                    $this->save();
                }),
            Actions\Action::make('saveAndFinish')
                ->label('Save & Finish')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->action(function () {

                    $this->record->status = 'SUBMITTED';
                    $this->record->save();

                    $this->save(shouldRedirect: false, shouldSendSavedNotification: false);

                    Notification::make()
                        ->title('Requisition marked as finished.')
                        ->success()
                        ->send();

                    return redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),

            Actions\DeleteAction::make(),
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
                        ->width('70%'),
                    Header::make('Quantity'),
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
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                    TextInput::make('requested_qty')
                        ->label('Qty')
                        ->numeric()
                        ->default(0)
                        ->reactive(),
                ])
                ->defaultItems(0)
                ->columns(6),
        ];
    }
}
