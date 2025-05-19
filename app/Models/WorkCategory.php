<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'work_id',
        'unit',
        'quantity',
        'amount',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function materials()
    {
        return $this->hasMany(CategoryMaterial::class)->orderBy('sequence');
    }

    public function work(): BelongsTo
    {
        return $this->belongsTo(Work::class);
    }

    public static function optionsForSelect(?int $workId): array
    {
        if (!$workId) {
            return [];
        }
        return self::where('work_id', $workId)
            ->pluck('name', 'id')
            ->toArray();
    }
}
