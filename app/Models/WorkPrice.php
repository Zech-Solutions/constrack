<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkPrice extends Model
{
    protected $fillable = ['work_category_id', 'supplier_id', 'price', 'date'];

    protected $casts = [
        'price' => 'decimal:2',
        'date' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(WorkPriceHistory::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->date)) {
                $model->date = now()->toDateTimeString();
            }
        });

        static::created(function ($model) {
            WorkPriceHistory::create([
                'work_category_id' => $model->work_category_id,
                'supplier_id' => $model->supplier_id,
                'price' => $model->price,
                'previous_price' => null,
                'date' => $model->date,
            ]);
        });

        static::updated(function ($model) {
            WorkPriceHistory::create([
                'work_category_id' => $model->work_category_id,
                'supplier_id' => $model->supplier_id,
                'price' => $model->price,
                'previous_price' => $model->getOriginal('price'),
                'date' => $model->date,
            ]);
        });
    }

    public static function supplierOptionsWithPrice($workCategoryId): array
    {
        return self::query()
            ->where('work_category_id', $workCategoryId)
            ->orderByDesc('price')
            ->get()
            ->mapWithKeys(function ($price) {
                $formattedPrice = '₱'.number_format($price->price, 2);

                return [
                    $price->supplier_id => "{$formattedPrice} – {$price->supplier->name}",
                ];
            })
            ->unique()
            ->toArray();
    }
}
