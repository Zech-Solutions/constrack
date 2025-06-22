<?php

namespace App\Models;

use App\Enums\QuotationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quotation extends Model
{
    protected $fillable = [
        'tenant_id',
        'client_id',
        'title',
        'description',
        'quotation_date',
        'term',
        'vat_percent',
        'profit_percent',
        'remarks',
        'completion',
        'direct_cost',
        'vat_cost',
        'total_cost',
        'status',
    ];

    protected $casts = [
        'status' => QuotationStatus::class,
    ];

    protected static function booted()
    {
        static::creating(function ($quotation) {
            $lastNumber = self::max('id') ?? 0;
            $nextNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            $quotation->code = 'BOQ-'.$nextNumber;
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function works(): BelongsToMany
    {
        return $this->belongsToMany(Work::class);
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class);
    }

    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function groupedItemsBySection()
    {
        return $this->quotationItems()
            ->with('section')
            ->get()
            ->groupBy('section_id');
    }
}
