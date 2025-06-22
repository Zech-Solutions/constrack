<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierProductPriceHistory extends Model
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
}
