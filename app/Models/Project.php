<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = [
        'tenant_id',
        'client_id',
        'quotation_id',
        'code',
        'title',
        'description',
        'client_po_date',
        'start_date',
        'due_date',
        'completed_date',
        'vat_cost',
        'direct_cost',
        'total_cost',
        'client_attachments',
        'remarks',
        'status',
    ];

    protected $casts = [
        'client_attachments' => 'array',
        'status' => ProjectStatus::class,
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
