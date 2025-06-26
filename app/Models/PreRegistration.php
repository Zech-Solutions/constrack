<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreRegistration extends Model
{
    use HasFactory;

    protected $table = 'preregistrations';

    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'domain_name',
        'owner_firstname',
        'owner_middlename',
        'owner_lastname',
        'owner_email',
        'address',
        'tenant_id',
    ];


    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
