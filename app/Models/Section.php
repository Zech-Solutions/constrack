<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subSections()
    {
        return $this->hasMany(SubSection::class);
    }

    public static function optionsForSelect(): array
    {
        return self::query()->pluck('name', 'id')->toArray();
    }
}
