<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'unit',
        'code',
        'product_category_id',
    ];

    /**
     * Get the associated tenant account
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public static function optionsForSelect(): array
    {
        return self::query()->pluck('name', 'id')->toArray();
    }

    public function supplierPrices()
    {
        return $this->hasMany(SupplierProductPrice::class);
    }

    public function supplierPricesHistory()
    {
        return $this->hasMany(SupplierProductPriceHistory::class);
    }
}
