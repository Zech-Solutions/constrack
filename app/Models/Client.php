<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Client extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'company',
        'tin',
        'type',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'user_id',
        'supplier_id',
        'credit_limit',
        'payment_terms',
        'notes',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    /**
     * Payment term options
     */
    public const PAYMENT_TERMS = [
        'net_15' => 'Net 15 Days',
        'net_30' => 'Net 30 Days',
        'cod' => 'Cash on Delivery',
    ];

    /**
     * Client types
     */
    public const TYPES = [
        'individual' => 'Individual',
        'business' => 'Business',
    ];

    /**
     * Get the associated tenant account
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the associated user account
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the associated supplier record
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get all products associated with this client
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    /**
     * Scope for active clients
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for business clients
     */
    public function scopeBusiness($query)
    {
        return $query->where('type', 'business');
    }

    /**
     * Get the client's full address
     */
    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]));
    }

    /**
     * Get the payment term as readable text
     */
    public function getPaymentTermTextAttribute(): string
    {
        return self::PAYMENT_TERMS[$this->payment_terms] ?? 'Unknown';
    }
}
