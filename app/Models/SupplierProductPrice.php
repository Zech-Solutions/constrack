<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierProductPrice extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'supplier_id', 'price', 'date', 'previous_price'];

    protected $casts = [
        'price' => 'decimal:2',
        'previous_price' => 'decimal:2',
        'date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function histories()
    {
        return $this->hasMany(SupplierProductPriceHistory::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->date)) {
                $model->date = now()->toDateTimeString();
            }
        });

        static::created(function ($model) {
            SupplierProductPriceHistory::create([
                'product_id' => $model->product_id,
                'supplier_id' => $model->supplier_id,
                'price' => $model->price,
                'previous_price' => null,
                'date' => $model->date,
            ]);
        });

        static::updated(function ($model) {
            SupplierProductPriceHistory::create([
                'product_id' => $model->product_id,
                'supplier_id' => $model->supplier_id,
                'price' => $model->price,
                'previous_price' => $model->getOriginal('price'),
                'date' => $model->date,
            ]);
        });
    }
}
