<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubSection extends Model
{
    protected $fillable = [
        'name',
        'description',
        'section_id',
        'unit',
        'quantity'
    ];
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function items()
    {
        return $this->hasMany(SubsectionItem::class)->orderBy('sequence');
    }

    public static function availableForSection(int $sectionId): array
    {
        return self::where('section_id', $sectionId)
            ->pluck('name', 'id')
            ->toArray();
    }

    public static function getOptionsForForm(?int $sectionId): array
    {
        if (!$sectionId) {
            return [];
        }

        return self::availableForSection($sectionId);
    }
}
