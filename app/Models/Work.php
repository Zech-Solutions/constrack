<?php

namespace App\Models;

use App\Enums\WorkType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Work extends Model
{
    protected $fillable = [
        'name',
        'description',
        'scope',
    ];

    protected $casts = [
        'scope' => WorkType::class,
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public static function optionsForSelect($workType = null): array
    {
        if(!empty($workType)){
            return self::query()
                ->where("scope", $workType)
                ->pluck('name', 'id')
                ->toArray();
        }
        
        return self::query()->pluck('name', 'id')->toArray();
    }
}
