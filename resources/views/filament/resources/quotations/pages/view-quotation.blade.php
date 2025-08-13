<x-filament-panels::page>
    @if ($this->hasInfolist())
        {{ $this->infolist }}
    @else
        {{ $this->form }}
    @endif

    @if (count($relationManagers = $this->getRelationManagers()))
        <x-filament-panels::resources.relation-managers
            :active-manager="$this->activeRelationManager"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        />
    @endif

    <div class="overflow-x-auto rounded-lg">
        <table class="w-full text-sm border border-gray-300 rounded-lg">
            <thead class="bg-gray-200">
                <tr>
                    <th colspan="6" class="border border-gray-300 p-1 text-left">PRELIMINARIES</th>
                </tr>
                <tr>
                    <th colspan="3" class="border border-gray-300 p-1">DESCRIPTION</th>
                    <th class="border border-gray-300 p-1">QTY</th>
                    <th class="border border-gray-300 p-1">UNIT</th>
                    <th class="border border-gray-300 p-1">AMOUNT</th>  
                </tr>
            </thead>
            <tbody>
                @php
                    $countWork = 0;
                @endphp

                @foreach($groupedPreliminaries as $workId => $items)
                    @php
                        $categories = $items->groupBy('work_category_id');
                    @endphp

                    <tr class="bg-blue-100 font-semibold">
                        <td class="border border-gray-300 p-1">{{ chr(65 + $countWork++) }}</td>
                        <td colspan="5" class="border border-gray-300 p-1">
                            {{ $items->first()->work->name ?? 'Unknown Work' }}
                        </td>
                    </tr>

                    @php
                        $subtotal = 0;
                        $countCategory = 0;
                    @endphp

                    @foreach($categories as $categoryId => $categoryItems)
                        @php
                            $subCategory = $categoryItems->first();
                            $subtotal += $subCategory->total;
                            $materials = $items->where('work_category_id', $subCategory->work_category_id)
                                                ->where('type', \App\Enums\QuotationItemType::MATERIAL);
                        @endphp
                        <tr>
                            <td class="border border-gray-300 p-1"></td>
                            <td class="border border-gray-300 p-1">{{ ++$countCategory }}</td>
                            <td class="border border-gray-300 p-1">{{ $subCategory->workCategory->name }}</td>
                            <td class="border border-gray-300 p-1">{{ $subCategory->quantity }}</td>
                            <td class="border border-gray-300 p-1">{{ $subCategory->workCategory->unit }}</td>
                            <td class="border border-gray-300 p-1 text-right">{{ number_format($subCategory->total, 2) }}</td>
                        </tr>

                    @endforeach

                    <tr class="font-semibold">
                        <td colspan="5" class="border border-gray-300 p-1 text-right">Subtotal</td>
                        <td class="border border-gray-300 p-1 text-right">{{ number_format($subtotal, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="font-bold bg-gray-200">
                    <td colspan="5" class="border border-gray-300 p-1 text-right">TOTAL PRELIMINARIES</td>
                    <td class="border border-gray-300 p-1 text-right">{{ number_format($quotation->total_cost, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="overflow-x-auto rounded-lg">
        <table class="w-full text-sm border border-gray-300 rounded-lg">
            <thead class="bg-gray-200">
                <tr>
                    <th rowspan="2" colspan="3" class="border border-gray-300 p-1">DESCRIPTION</th>
                    <th rowspan="2" class="border border-gray-300 p-1">QTY</th>
                    <th rowspan="2" class="border border-gray-300 p-1">UNIT</th>
                    <th colspan="2" class="border border-gray-300 p-1">UNIT COST</th>
                    <th rowspan="2" class="border border-gray-300 p-1">AMOUNT</th>
                </tr>
                <tr>
                    <th class="border border-gray-300 p-1">MATERIAL</th>
                    <th class="border border-gray-300 p-1">LABOR</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedSubcategories as $workId => $items)
                    @php
                        $categories = $items->groupBy('work_category_id');
                    @endphp

                    <tr class="bg-blue-100 font-semibold">
                        <td class="border border-gray-300 p-1">{{ chr(65 + $countWork++) }}</td>
                        <td colspan="7" class="border border-gray-300 p-1">
                            {{ $items->first()->work->name ?? 'Unknown Work' }}
                        </td>
                    </tr>

                    @php
                        $subtotal = 0;
                        $countCategory = 0;
                    @endphp

                    @foreach($categories as $categoryId => $categoryItems)
                        @php
                            $subCategory = $categoryItems->first();
                            $subtotal += $subCategory->total;
                            $materials = $items->where('work_category_id', $subCategory->work_category_id)
                                                ->where('type', \App\Enums\QuotationItemType::MATERIAL);
                        @endphp
                        <tr>
                            <td class="border border-gray-300 p-1"></td>
                            <td class="border border-gray-300 p-1">{{ ++$countCategory }}</td>
                            <td class="border border-gray-300 p-1">{{ $subCategory->workCategory->name }}</td>
                            <td class="border border-gray-300 p-1">{{ $subCategory->quantity }}</td>
                            <td class="border border-gray-300 p-1">{{ $subCategory->workCategory->unit }}</td>
                            <td class="border border-gray-300 p-1">{{ $subCategory->unit_price }}</td>
                            <td class="border border-gray-300 p-1">{{ $subCategory->labor_fee }}</td>
                            <td class="border border-gray-300 p-1">{{ number_format($subCategory->total, 2) }}</td>
                        </tr>

                        @foreach($materials as $product)
                            <tr class="bg-gray-200">
                                <td class="border border-gray-300 p-1"></td>
                                <td class="border border-gray-300 p-1"></td>
                                <td class="border border-gray-300 p-1">{{ $product->product->name }}</td>
                                <td class="border border-gray-300 p-1">{{ $product->quantity }}</td>
                                <td class="border border-gray-300 p-1">{{ $product->product->unit }}</td>
                                <td class="border border-gray-300 p-1">{{ number_format($product->unit_price, 2) }}</td>
                                <td class="border border-gray-300 p-1"></td>
                                <td class="border border-gray-300 p-1"></td>
                            </tr>
                        @endforeach
                    @endforeach

                    <tr class="font-semibold">
                        <td colspan="7" class="border border-gray-300 p-1 text-right">Subtotal</td>
                        <td class="border border-gray-300 p-1">{{ number_format($subtotal, 2) }}</td>
                    </tr>
                @endforeach

                <tr class="font-semibold bg-gray-100">
                    <td colspan="7" class="border border-gray-300 p-1 text-right">TOTAL DIRECT COST</td>
                    <td class="border border-gray-300 p-1">{{ number_format($quotation->direct_cost, 2) }}</td>
                </tr>
                <tr class="font-semibold bg-gray-100">
                    <td colspan="7" class="border border-gray-300 p-1 text-right">VAT (12%)</td>
                    <td class="border border-gray-300 p-1">{{ number_format($quotation->vat_cost, 2) }}</td>
                </tr>
                <tr class="font-bold bg-gray-200">
                    <td colspan="7" class="border border-gray-300 p-1 text-right">TOTAL COST (VAT INCLUDED)</td>
                    <td class="border border-gray-300 p-1">{{ number_format($quotation->total_cost, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</x-filament-panels::page>