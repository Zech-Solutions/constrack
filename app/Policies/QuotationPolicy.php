<?php

namespace App\Policies;

use App\Enums\QuotationStatus;
use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Quotation $quotation): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['TENANT_ADMIN']) || $user->can('create quotation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quotation $quotation): bool
    {
        return ($user->hasRole(['TENANT_ADMIN']) || $user->can('update quotation')) && $quotation->status === QuotationStatus::Draft;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quotation $quotation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Quotation $quotation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Quotation $quotation): bool
    {
        return false;
    }

    /**
     * Determine if the given quotation can be approved by the user.
     */
    public function finish(User $user, Quotation $quotation): bool
    {
        // User must have permission & quotation must not be approved yet
        return ($user->hasRole(['TENANT_ADMIN']) || $user->can('finish quotations')) && $quotation->status === QuotationStatus::Draft;
    }

    /**
     * Determine if the given quotation can be approved by the user.
     */
    public function approve(User $user, Quotation $quotation): bool
    {
        // User must have permission & quotation must not be approved yet
        return ($user->hasRole(['TENANT_ADMIN']) || $user->can('approve quotations')) && $quotation->status === QuotationStatus::Pending;
    }

    /**
     * Determine if the given quotation can be approved by the user.
     */
    public function accept(User $user, Quotation $quotation): bool
    {
        // User must have permission & quotation must not be approved yet
        return ($user->hasRole(['TENANT_ADMIN']) || $user->can('accept quotations')) && $quotation->status === QuotationStatus::Reviewed;
    }
}
