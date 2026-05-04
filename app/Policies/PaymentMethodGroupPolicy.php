<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use CodeWithDiki\PaymentModule\Models\PaymentMethodGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentMethodGroupPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PaymentMethodGroup');
    }

    public function view(AuthUser $authUser, PaymentMethodGroup $paymentMethodGroup): bool
    {
        return $authUser->can('View:PaymentMethodGroup');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PaymentMethodGroup');
    }

    public function update(AuthUser $authUser, PaymentMethodGroup $paymentMethodGroup): bool
    {
        return $authUser->can('Update:PaymentMethodGroup');
    }

    public function delete(AuthUser $authUser, PaymentMethodGroup $paymentMethodGroup): bool
    {
        return $authUser->can('Delete:PaymentMethodGroup');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PaymentMethodGroup');
    }

    public function restore(AuthUser $authUser, PaymentMethodGroup $paymentMethodGroup): bool
    {
        return $authUser->can('Restore:PaymentMethodGroup');
    }

    public function forceDelete(AuthUser $authUser, PaymentMethodGroup $paymentMethodGroup): bool
    {
        return $authUser->can('ForceDelete:PaymentMethodGroup');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PaymentMethodGroup');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PaymentMethodGroup');
    }

    public function replicate(AuthUser $authUser, PaymentMethodGroup $paymentMethodGroup): bool
    {
        return $authUser->can('Replicate:PaymentMethodGroup');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PaymentMethodGroup');
    }

}