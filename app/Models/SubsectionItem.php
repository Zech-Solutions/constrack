<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubsectionItem extends Model
{
    protected $fillable = [
        'sub_section_id',
        'product_id',
        'quantity',
        'sequence',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
