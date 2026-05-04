<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use CodeWithDiki\ProductModule\Models\ProductColor;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductColorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProductColor');
    }

    public function view(AuthUser $authUser, ProductColor $productColor): bool
    {
        return $authUser->can('View:ProductColor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProductColor');
    }

    public function update(AuthUser $authUser, ProductColor $productColor): bool
    {
        return $authUser->can('Update:ProductColor');
    }

    public function delete(AuthUser $authUser, ProductColor $productColor): bool
    {
        return $authUser->can('Delete:ProductColor');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProductColor');
    }

    public function restore(AuthUser $authUser, ProductColor $productColor): bool
    {
        return $authUser->can('Restore:ProductColor');
    }

    public function forceDelete(AuthUser $authUser, ProductColor $productColor): bool
    {
        return $authUser->can('ForceDelete:ProductColor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProductColor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProductColor');
    }

    public function replicate(AuthUser $authUser, ProductColor $productColor): bool
    {
        return $authUser->can('Replicate:ProductColor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProductColor');
    }

}