<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Enums\QuotationItemType;
use App\Enums\WorkType;
use App\Filament\Resources\QuotationResource;
use App\Models\Product;
use App\Models\QuotationItem;
use App\Models\SubSection;
use App\Models\Work;
use App\Models\WorkCategory;
use Closure;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\{
    DatePicker,
    Hidden,
    Repeater,
    Section,
    Select,
    Tabs,
    Textarea,
    TextInput,
    Wizard,
    Wizard\Step
};
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

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

                    // ...$this->getWizardSectionSteps(),

                    Step::make('Overview')
                        ->schema([
                            Section::make("Review Quotation")
                                ->description("Check the information before saving")
                                ->schema([
                                    TextInput::make('client_name')
                                        ->label('Client')
                                        ->default(fn() => $this->record?->client?->name)
                                        ->disabled(),
                                    TextInput::make('quotation_date')
                                        ->default(fn() => $this->record?->quotation_date)
                                        ->disabled(),
                                    TextInput::make('title')
                                        ->default(fn() => $this->record?->title)
                                        ->disabled(),
                                    Textarea::make('description')
                                        ->default(fn() => $this->record?->description)
                                        ->disabled(),
                                ]),
                        ]),
                ])
                    ->submitAction(new HtmlString('
                    <div class="flex justify-between w-full mt-4">
                        <button 
                            type="submit" 
                            onclick="document.getElementById(\'actionInput\').value=\'draft\'"
                            class="filament-button filament-button-secondary"
                        >
                            Save as Draft
                        </button>
                        <button 
                            type="submit" 
                            onclick="document.getElementById(\'actionInput\').value=\'finish\'"
                            class="filament-button filament-button-success"
                        >
                            Finish Quotation
                        </button>
                        <input type="hidden" id="actionInput" name="action" value="draft" />
                    </div>
                '))
                    ->columnSpanFull()
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
            Section::make("Preliminaries")
                ->schema([
                    Repeater::make("preliminaries")
                        ->label(false)
                        ->relationship(
                            name: 'quotationItems',
                            modifyQueryUsing: fn(Builder $query) => $query
                                ->where('type', QuotationItemType::PRELIMINARIES)
                        )
                        ->schema([
                            Select::make('work_id')
                                ->label("Scope")
                                ->searchable()
                                ->options(fn() => Work::optionsForSelect(WorkType::PRELIMINARIES))
                                ->live()
                                ->required()
                                ->afterStateUpdated(fn(Set $set) => $set('work_category_id', null)),

                            Select::make('work_category_id')
                                ->label("Sub Category")
                                ->searchable()
                                ->required()
                                ->options(function (Get $get) {
                                    $work_id = $get('work_id');
                                    return WorkCategory::optionsForSelect($work_id);
                                })
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            TextInput::make('total')
                                ->label("Amount")
                                ->required()
                                ->numeric(),
                        ])
                        ->columns(3),
                ])
        ];
    }

    public function getStepMainWorks(): array
    {
        return [
            Tabs::make("Scope of Works")
                ->tabs($this->getTabMainWorks())
                ->columnSpanFull()
        ];
    }

    public function getTabMainWorks(): array
    {

        $record = $this->record;

        if (! $record) return [];

        $grouped = $record->quotationItems()
            ->with('work', 'product')
            ->where('type', QuotationItemType::SUB_CATEGORY)
            ->get()
            ->groupBy('work_id');


        $tabs = [];

        foreach ($grouped as $work_id => $items) {
            $tabname = $items->first()->work->name ?? "Work";

            $tabs[] = Tab::make($tabname)
                ->schema([
                    ...$this->getTabCategory($work_id),
                    ...$this->getTabMaterials($work_id),
                ]);
        }
        return $tabs;
    }

    public function getTabCategory($workId): array
    {
        Log::debug("Section id: " . $workId);
        return [
            Section::make("Work Categories")
                ->schema([
                    Repeater::make("items_{$workId}")
                        ->label(false)
                        ->relationship(
                            name: 'quotationItems',
                            modifyQueryUsing: fn(Builder $query) => $query
                                ->where('work_id', $workId)
                                ->where('type', QuotationItemType::SUB_CATEGORY)
                        )
                        ->schema([
                            Hidden::make('work_id')
                                ->default($workId),
                            Select::make('work_category_id')
                                ->label('Category')
                                ->searchable()
                                ->options(fn() => WorkCategory::optionsForSelect($workId))
                                ->columnSpan(2)
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            TextInput::make('quantity')
                                ->numeric()
                                ->reactive()
                                ->afterStateUpdated($this->updateTotalCost()),
                            Hidden::make('unit_cost'),
                            TextInput::make('unit_price')
                                ->label("Costs")
                                ->helperText("Total Material Cost")
                                ->readOnly()
                                ->numeric()
                                ->reactive()
                                ->afterStateUpdated($this->updateTotalCost()),
                            TextInput::make('labor_fee')
                                ->numeric()
                                ->reactive()
                                ->afterStateUpdated($this->updateTotalCost()),
                            TextInput::make('total')
                                ->label("Total Amount")
                                ->readOnly()
                                ->numeric(),
                        ])
                        ->columns(6),
                ])
        ];
    }

    public function getTabMaterials($workId): array
    {
        $record = $this->record;
        $profitPercent = $record->profit_percent;
        $subSections = [];

        $record->quotationItems()
            ->with('workCategory', 'product')
            ->where('work_id', $workId)
            ->where('type', QuotationItemType::SUB_CATEGORY)
            ->each(function (QuotationItem $quotationItem) use (&$subSections, $workId, $profitPercent) {
                $subSections[] = Section::make($quotationItem->workCategory->name)
                    ->schema([
                        Repeater::make("materials_{$workId}_{$quotationItem->work_category_id}")
                            ->label(false)
                            ->relationship(
                                name: 'quotationItems',
                                modifyQueryUsing: fn(Builder $query) => $query
                                    ->where('work_id', $workId)
                                    ->where('work_category_id', $quotationItem->work_category_id)
                                    ->whereNotNull('product_id')
                            )
                            ->schema([
                                Hidden::make('work_id')->default($workId),
                                Hidden::make('work_category_id')->default($quotationItem->work_category_id),

                                Select::make('product_id')
                                    ->label('Materials')
                                    ->searchable()
                                    ->options(fn() => Product::optionsForSelect())
                                    ->columnSpan(2)
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                TextInput::make('quantity')
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Get $get, Set $set) use ($profitPercent) {
                                        $unitPrice = $get('unit_price') ?? 0;
                                        $set('amount', round($state * $unitPrice, 2));
                                    }),

                                TextInput::make('unit_cost')
                                    ->label("Cost")
                                    ->numeric()
                                    ->default(0)
                                    ->debounce(750)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Get $get, Set $set) use ($profitPercent) {
                                        $profit = $profitPercent ?? 0;
                                        $quantity = $get('quantity') ?? 0;
                                        $price = round($state * (1 + $profit / 100), 2);
                                        $set('unit_price', $price);
                                        $set('amount', round($quantity * $price, 2));
                                    }),

                                TextInput::make('unit_price')
                                    ->label("Price")
                                    ->numeric()
                                    ->readOnly()
                                    ->default(0)
                                    ->helperText("Cost x Profit %"),

                                TextInput::make('amount')
                                    ->label("Amount")
                                    ->numeric()
                                    ->default(0)
                                    ->readOnly()
                                    ->helperText("Qty x Price"),
                            ])
                            ->afterStateUpdated(function ($state, callable $get, callable $set) use ($workId, $quotationItem) {
                                $items = $get("items_{$workId}");

                                $totalCost = collect($state)
                                    ->sum(fn($item) => ($item['amount'] ?? 0));

                                foreach ($items as $key => &$item) {
                                    if ($item['work_category_id'] == $quotationItem->work_category_id) {
                                        $item['unit_cost'] = $totalCost;
                                        $item['unit_price'] = $totalCost;
                                        $item['amount'] = $item['quantity'] * $totalCost;
                                        $item['total'] = $item['amount'] + $item['labor_fee'];
                                        break;
                                    }
                                }

                                $set("items_{$workId}", $items);
                            })
                            ->columns(6),
                    ])
                    ->collapsible()
                    ->collapsed();
            });

        return $subSections;
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

    protected function mutateFormDataBeforeSave(array $data): array
    {

        $action = request()->input('action');
        Log::debug("status: " . $action);

        if ($action === 'finish') {
            $data['status'] = 'PENDNG';
        }

        return $data;
    }
}
