<?php

namespace App\Models;

use App\Enums\QuotationItemType;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'work_id',
        'work_category_id',
        'parent_id',
        'product_id',
        'supplier_id',
        'unit_cost',
        'unit_price',
        'amount',
        'labor_fee',
        'total',
        'quantity',
        'type',
    ];

    protected $casts = [
        'type' => QuotationItemType::class,
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function workCategory()
    {
        return $this->belongsTo(WorkCategory::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function materials()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public static function options($quotationId = null): array
    {
        return self::query()
            ->where('quotation_id', $quotationId)
            ->where('type', QuotationItemType::SUB_CATEGORY)
            ->with('workCategory')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->work_category_id => $item->workCategory->name];
            })
            ->toArray();
    }
}
