<?php

namespace App\Models;

use App\Enums\QuotationStatus;
use App\Jobs\GenerateQuotation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quotation extends Model
{
    protected $fillable = [
        'tenant_id',
        'client_id',
        'project_id',
        'title',
        'description',
        'quotation_date',
        'term',
        'vat_percent',
        'profit_percent',
        'labor_percent',
        'remarks',
        'completion',
        'direct_cost',
        'vat_cost',
        'total_cost',
        'status',
        'filename',
    ];

    protected $casts = [
        'status' => QuotationStatus::class,
    ];

    protected static function booted()
    {
        static::updated(function (Quotation $quotation) {
            if ($quotation->status === QuotationStatus::PENDING) {
                GenerateQuotation::dispatch($quotation);
            }
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

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function works(): BelongsToMany
    {
        return $this->belongsToMany(Work::class);
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
