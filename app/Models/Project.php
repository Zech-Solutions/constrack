<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'client_id',
        'contact_person',
        'contact_designation',
        'start_date',
        'due_date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public static function optionsForSelect(?int $clientId): array
    {
        if (! $clientId) {
            return [];
        }

        return self::where('client_id', $clientId)
            ->pluck('name', 'id')
            ->toArray();
    }
}
