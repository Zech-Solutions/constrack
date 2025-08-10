<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quotation - {{ $quotation->code ?? 'BOQ' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 10px;
        }

        th {
            background-color: #f2f2f2;
        }

        .no-border td,
        .no-border th {
            border: none;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    <table class="no-border" width="100%">
        <tr>
            <td width="100px" valign="top">
                <img src="{{ $logo }}" style="height: 50px;" alt="Company Logo">
            </td>
            <td valign="top">
                <div style="font-size:12px;font-weight:bold;">
                    {{ $tenant->name ?? 'YWB ENGINEERING & CONSTRUCTION SERVICES'}}
                </div>
                <div style="font-size:10px;">
                    {{ $tenant->industry ?? 'GENERAL CONTRACTOR - SUPPLY - CONSULTANCY'}}
                </div>
                <div style="font-size:8px;">VAT REG. TIN: 463-891-893-000</div>
                <div style="font-size:8px;">Phase 3-B, Canetown Subd., Brgy. XIX-A, Victorias City, Negros Occidental</div>
                <div style="font-size:8px;">Tel.No.: (034) 398 5512 / Fax No: (034) 446 1885</div>
                <div style="font-size:8px;">Mobile (+63)9072786893 / (+63)9274905001</div>
                <div style="font-size:8px;">Email: ywbecs@gmail.com</div>
            </td>
        </tr>
    </table>

    {{-- Quotation Details --}}
    <div style="padding: 10px 0; font-size:10px;">
        Date: {{ \Carbon\Carbon::parse($quotation->date ?? now())->format('F j, Y') }}
    </div>
    <div style="padding: 10px 0; font-size:10px;">
        <strong>AYALA MALLS CAPITOL CENTRAL</strong><br>
        Gatuslao St., Brgy. 8, Bacolod City, Negros Occidental
    </div>
    <div style="padding: 10px 0; font-size:10px;">
        Attention: Engr. Joecelyn S. Benjamin<br>
        <span style="margin-left: 80px;">Building Engineer</span>
    </div>
    <div style="padding: 10px 0; font-size:10px;">
        Subject: {{ $quotation->title }}
    </div>
    <div style="padding: 10px 0; font-size:10px;">
        Dear Madam:<br>
        In response to your request to quote for the above subject, we are pleased to submit our offer for your consideration, subject to the terms and conditions stated hereunder:
    </div>

    {{-- Quotation Code --}}
    <table class="no-border" width="100%">
        <tr>
            <td align="right">
                <h3 style="border-top: 1px solid black; padding:5px;">{{ $quotation->code }}</h3>
            </td>
        </tr>
    </table>

    {{-- Items Table --}}
    <h2>Bill of Quantities</h2>
    <table>
        <thead>
            <tr>
                <th rowspan="2" colspan="3">DESCRIPTION</th>
                <th rowspan="2">QTY</th>
                <th rowspan="2">UNIT</th>
                <th colspan="2">UNIT COST</th>
                <th rowspan="2">AMOUNT</th>
            </tr>
            <tr>
                <th>MATERIAL</th>
                <th>LABOR</th>
            </tr>
        </thead>
        <tbody>
            @php
                $groupedItems = $quotation
                ->quotationItems()
                ->with('work', 'product') // eager load to avoid N+1 queries
                ->get()
                ->groupBy('work_id');

                $countWork = 0;
            @endphp

            @foreach($groupedItems as $workId => $items)
                @php
                    $categories = $items->groupBy('work_category_id');
                @endphp
            <tr style="background-color:#dce6f1;">
                <td>{{ chr(65 + $countWork++) }}</td>
                <td colspan="7">{{ $items->first()->work->name ?? 'Unknown Work' }}</td>
            </tr>
                @php
                    $subtotal = 0;
                    $countCategory = 0;
                @endphp
                @foreach($categories as $categoryId => $categoryItems)
                    @php
                        $subCategory = $categoryItems->first();
                        $subtotal += $subCategory->total;

                        $materials = $items->where('work_category_id', $subCategory->work_category_id)->where('type', \App\Enums\QuotationItemType::MATERIAL);
                    @endphp
                <tr>
                    <td></td>
                    <td>{{ ++$countCategory }}</td>
                    <td>{{ $subCategory->workCategory->name }}</td>
                    <td>{{ $subCategory->quantity }}</td>
                    <td>{{ $subCategory->workCategory->unit }}</td>
                    <td>{{ $subCategory->unit_price }}</td>
                    <td>{{ $subCategory->labor_fee }}</td>
                    <td>{{ number_format($subCategory->total, 2) }}</td>
                </tr>
                    @foreach($materials as $product)
                    <tr style="background-color:#d9d9d9;">
                        <td></td>
                        <td></td>
                        <td>{{ $product->product->name }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>{{ $product->product->unit }}</td>
                        <td>{{ number_format($product->unit_price, 2) }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endforeach
                @endforeach
                <tr>
                    <td colspan="7" align="right">Subtotal</td>
                    <td>{{ number_format($subtotal, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="7" align="right">TOTAL DIRECT COST</td>
                <td>{{ number_format($quotation->direct_cost, 2) }}</td>
            </tr>
            <tr>
                <td colspan="7" align="right">VAT(12%)</td>
                <td>{{ number_format($quotation->vat_cost, 2) }}</td>
            </tr>
            <tr>
                <td colspan="7" align="right">TOTAL COST (VAT INCLUDED)</td>
                <td>{{ number_format($quotation->total_cost, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {!! $quotation->remarks ?? 'No notes provided.' !!}
    </div>
    <div style="padding: 20px 0; font-size:12px;">
        Very truly yours,
    </div>
    <div style="padding: 10px 0; font-size:12px;">
        <strong>Warren G. Muñez, PME</strong><br>
        <span>Operations Manager</span>
    </div>
    {{-- Footer --}}
    <div style="margin-top: 20px; font-size: 10px;">
        Generated on {{ now()->format('F j, Y h:i A') }}
    </div>

</body>

</html>