<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Enums\QuotationItemType;
use App\Enums\QuotationStatus;
use App\Enums\WorkType;
use App\Filament\Resources\QuotationResource;
use App\Models\Product;
use App\Models\Work;
use App\Models\WorkCategory;
use Filament\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions as ComponentsActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

    public function mount($record): void
    {
        parent::mount($record);

        $this->calculateInitialValues();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Quotation Information')
                        ->schema($this->getStepBasic())
                        ->columns(3),
                    Step::make('Preliminaries')
                        ->schema($this->getStepPreliminaries())
                        ->columns(3),
                    Step::make('Main Scope')
                        ->schema($this->getStepMainWorks())
                        ->columns(3),
                    Step::make('Overview')
                        ->schema($this->getStepOverview()),
                ])
                    ->columnSpanFull(),
            ]);
    }

    public function getStepBasic(): array
    {
        return [
            Select::make('client_id')
                ->label('Clients')
                ->relationship('client', 'name')
                ->required()
                ->preload()
                ->searchable(),
            DatePicker::make('quotation_date')
                ->required(),
            TextInput::make('term')
                ->required()
                ->numeric()
                ->default(30),
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->autocomplete('off')
                ->columnSpanFull(),
            Textarea::make('description')
                ->autocomplete('off')
                ->maxLength(255)
                ->default(null),
            Textarea::make('remarks'),
            Textarea::make('completion'),
        ];
    }

    public function getStepPreliminaries(): array
    {
        return [
            Section::make('Preliminaries')
                ->schema([
                    Repeater::make('preliminaries')
                        ->label(false)
                        ->relationship(
                            name: 'quotationItems',
                            modifyQueryUsing: fn (Builder $query) => $query
                                ->where('type', QuotationItemType::PRELIMINARIES)
                        )
                        ->schema([
                            Select::make('work_id')
                                ->label('Scope')
                                ->searchable()
                                ->options(fn () => Work::optionsForSelect(WorkType::PRELIMINARIES))
                                ->live()
                                ->required()
                                ->afterStateUpdated(fn (Set $set) => $set('work_category_id', null)),

                            Select::make('work_category_id')
                                ->label('Sub Category')
                                ->searchable()
                                ->required()
                                ->options(function (Get $get) {
                                    $work_id = $get('work_id');

                                    return WorkCategory::optionsForSelect($work_id);
                                })
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            TextInput::make('total')
                                ->label('Amount')
                                ->required()
                                ->numeric()
                                ->reactive()
                                ->debounce(1000)
                                ->afterStateUpdated(fn () => $this->calculateDirectCost()),
                        ])
                        ->columns(3),
                ]),
        ];
    }

    public function getStepMainWorks(): array
    {
        return [
            Tabs::make('Scope of Works')
                ->tabs($this->getTabMainWorks())
                ->columnSpanFull(),
        ];
    }

    public function getStepOverview()
    {
        return [
            Section::make('Review Quotation')
                ->description('Check the information before saving')
                ->schema([
                    TextInput::make('client_name')
                        ->label('Client')
                        ->default(fn () => $this->record?->client?->name)
                        ->disabled(),
                    TextInput::make('quotation_date')
                        ->default(fn () => $this->record?->quotation_date)
                        ->disabled(),
                    TextInput::make('title')
                        ->default(fn () => $this->record?->title)
                        ->disabled(),
                    Textarea::make('description')
                        ->default(fn () => $this->record?->description)
                        ->disabled(),
                ]),
            Section::make('Cost Summary')
                ->description('Overview of costs including VAT')
                ->schema([
                    TextInput::make('direct_cost')
                        ->label('Direct Cost')
                        ->readOnly()
                        ->numeric()
                        ->reactive(),
                    TextInput::make('vat_cost')
                        ->label('VAT (12%)')
                        ->numeric()
                        ->reactive(),
                    TextInput::make('total_cost')
                        ->label('Total Cost (VAT Included)')
                        ->numeric()
                        ->reactive(),
                ])
                ->columns(3),

            ComponentsActions::make([
                Action::make('saveAsDraft')
                    ->label('Save as Draft')
                    ->color('danger')
                    ->icon('heroicon-m-sparkles')
                    ->outlined()
                    ->action(fn () => $this->setStatus(QuotationStatus::DRAFT)),

                Action::make('finish')
                    ->label('Finish')
                    ->color('primary')
                    ->icon('heroicon-m-sparkles')
                    ->outlined()
                    ->action(fn () => $this->setStatus(QuotationStatus::PENDING)),
            ])
                ->alignEnd(),
        ];
    }

    public function setStatus(QuotationStatus $status)
    {
        $this->record->status = $status->value;
        $this->save();

        if ($status === QuotationStatus::PENDING) {
            $this->redirect(QuotationResource::getUrl('view', ['record' => $this->record]));
        }
    }

    public function getTabMainWorks(): array
    {

        $record = $this->record;

        if (! $record) {
            return [];
        }

        $grouped = $record->quotationItems()
            ->with('work', 'product')
            ->where('type', QuotationItemType::SUB_CATEGORY)
            ->get()
            ->groupBy('work_id');

        $tabs = [];

        foreach ($grouped as $work_id => $items) {
            $tabname = $items->first()->work->name ?? 'Work';

            $tabs[] = Tab::make($tabname)
                ->badge(
                    fn (Get $get) => count($get("items_{$work_id}") ?? [])
                )->schema([
                    ...$this->getTabCategory($work_id),
                ]);
        }

        return $tabs;
    }

    public function getTabCategory($workId): array
    {
        $record = $this->record;

        $profitPercent = $record->profit_percent;

        Log::debug('Section id: '.$workId);

        return [
            Repeater::make("items_{$workId}")
                ->label(false)
                ->relationship(
                    name: 'quotationItems',
                    modifyQueryUsing: fn (Builder $query) => $query
                        ->where('work_id', $workId)
                        ->where('type', QuotationItemType::SUB_CATEGORY)
                )
                ->schema([
                    Hidden::make('work_id')
                        ->default($workId),
                    Select::make('work_category_id')
                        ->label('Category')
                        ->searchable()
                        ->options(fn () => WorkCategory::optionsForSelect($workId))
                        ->columnSpan(2)
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Set $set) => $set('work_category_id', $state)),
                    TextInput::make('quantity')
                        ->numeric()
                        ->reactive()
                        ->debounce(1000)
                        ->default(1)
                        ->afterStateUpdated(fn () => $this->calculateDirectCost()),
                    Hidden::make('unit_cost')
                        ->default(0),
                    TextInput::make('unit_price')
                        ->label('Costs')
                        ->default(0)
                        ->helperText('Total Material Cost')
                        ->readOnly()
                        ->numeric(),
                    TextInput::make('labor_fee')
                        ->numeric()
                        ->reactive()
                        ->debounce(1000)
                        ->afterStateUpdated(fn () => $this->calculateDirectCost()),
                    TextInput::make('total')
                        ->label('Total Amount')
                        ->readOnly()
                        ->numeric(),

                    Section::make('Materials')
                        ->description(
                            fn (Get $get) => 'You have '.count($get('materials') ?? []).' material item(s).'
                        )
                        ->schema([
                            Repeater::make('materials')
                                ->relationship(
                                    name: 'materials',
                                )
                                ->schema([
                                    Hidden::make('quotation_id')
                                        ->default($this->record->id),

                                    Hidden::make('work_id')
                                        ->default($workId),

                                    Hidden::make('work_category_id')
                                        ->default(function ($state, Get $get) {
                                            return $get('../../work_category_id') ?? null;
                                        }),

                                    Hidden::make('type')
                                        ->default(QuotationItemType::MATERIAL),

                                    Select::make('product_id')
                                        ->label('Materials')
                                        ->searchable()
                                        ->options(fn () => Product::optionsForSelect())
                                        ->columnSpan(2)
                                        ->required()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                    TextInput::make('quantity')
                                        ->label('Qty')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->debounce(1000)->afterStateUpdated(function ($state, Get $get, Set $set) {
                                            $unitPrice = $get('unit_price') ?? 0;
                                            $set('amount', round($state * $unitPrice, 2));
                                            $this->calculateDirectCost();
                                        }),

                                    TextInput::make('unit_cost')
                                        ->label('Cost')
                                        ->numeric()
                                        ->default(0)
                                        ->reactive()
                                        ->debounce(1000)
                                        ->afterStateUpdated(function ($state, Get $get, Set $set) use ($profitPercent) {
                                            $profit = $profitPercent ?? 0;
                                            $quantity = $get('quantity') ?? 0;
                                            $price = round($state * (1 + $profit / 100), 2);
                                            $set('unit_price', $price);
                                            $set('amount', round($quantity * $price, 2));
                                            $this->calculateDirectCost();
                                        }),

                                    TextInput::make('unit_price')
                                        ->label('Price')
                                        ->numeric()
                                        ->readOnly()
                                        ->default(0)
                                        ->helperText('Cost x Profit %'),

                                    TextInput::make('amount')
                                        ->label('Amount')
                                        ->numeric()
                                        ->default(0)
                                        ->readOnly()
                                        ->helperText('Qty x Price'),
                                ])
                                ->defaultItems(0)
                                ->columns(6),
                        ])
                        ->collapsed(),
                ])
                ->columns(6),

        ];
    }

    private function updateTotalCost(): \Closure
    {
        return function ($state, Get $get, Set $set) {
            $quantity = (float) $get('quantity');
            $unitPrice = (float) $get('unit_price');
            $laborFee = (float) $get('labor_fee');
            $set('total', round($quantity * $unitPrice + $laborFee, 2));
        };
    }

    public function calculateWorksTotal($itemWork)
    {
        $total = 0;
        $workCategories = $this->data[$itemWork];
        foreach ($workCategories as $workCategoryId => $workCategory) {
            $materialCost = collect($workCategory['materials'])->sum(fn ($item) => ($item['amount'] ?? 0));
            $workCategory['unit_cost'] = $materialCost;
            $workCategory['unit_price'] = $materialCost;
            $workCategory['total'] = ($workCategory['unit_price'] * $workCategory['quantity']) + $workCategory['labor_fee'];
            Log::debug("($workCategory[unit_price] * $workCategory[quantity]) + $workCategory[labor_fee]");
            $this->data[$itemWork][$workCategoryId] = $workCategory;
            $total += $workCategory['total'];
        }

        return $total;
    }

    public function calculateMainWorks()
    {
        $mainWorks = array_filter(array_keys($this->data), fn ($key) => str_starts_with($key, 'items_'));
        $total = 0;
        foreach ($mainWorks as $mainWorks) {
            $total += $this->calculateWorksTotal($mainWorks);
        }

        return $total;
    }

    public function calculateInitialValues(): void
    {
        $this->calculateDirectCost();
        Log::debug($this->data);
    }

    public function calculateDirectCost()
    {
        $totalPreliminaries = $this->calculatePreliminariesSum();
        $totalMainWorks = $this->calculateMainWorks();
        $directCost = $totalPreliminaries + $totalMainWorks;
        Log::debug("$directCost = $totalPreliminaries + $totalMainWorks");
        $this->data['direct_cost'] = $directCost;
        $this->data['vat_cost'] = round($directCost * ($this->data['vat_percent'] / 100), 2);
        $this->data['total_cost'] = $directCost + $this->data['vat_cost'];
    }

    public function calculatePreliminariesSum()
    {
        return collect($this->data['preliminaries'])
            ->sum(fn ($item) => ($item['total'] ?? 0));
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        $action = request()->input('action');
        Log::debug('status: '.$action);

        if ($action === 'finish') {
            $data['status'] = 'PENDNG';
        }

        return $data;
    }
}
