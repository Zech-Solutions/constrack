<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkPriceHistory extends Model
{
    protected $fillable = ['work_category_id', 'supplier_id', 'price', 'date', 'previous_price'];

    protected $casts = [
        'price' => 'decimal:2',
        'previous_price' => 'decimal:2',
        'date' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
