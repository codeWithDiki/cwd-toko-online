<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use CodeWithDiki\TransactionModule\Models\TransactionItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TransactionItem');
    }

    public function view(AuthUser $authUser, TransactionItem $transactionItem): bool
    {
        return $authUser->can('View:TransactionItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TransactionItem');
    }

    public function update(AuthUser $authUser, TransactionItem $transactionItem): bool
    {
        return $authUser->can('Update:TransactionItem');
    }

    public function delete(AuthUser $authUser, TransactionItem $transactionItem): bool
    {
        return $authUser->can('Delete:TransactionItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TransactionItem');
    }

    public function restore(AuthUser $authUser, TransactionItem $transactionItem): bool
    {
        return $authUser->can('Restore:TransactionItem');
    }

    public function forceDelete(AuthUser $authUser, TransactionItem $transactionItem): bool
    {
        return $authUser->can('ForceDelete:TransactionItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TransactionItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TransactionItem');
    }

    public function replicate(AuthUser $authUser, TransactionItem $transactionItem): bool
    {
        return $authUser->can('Replicate:TransactionItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TransactionItem');
    }

}