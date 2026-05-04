<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use CodeWithDiki\ProductModule\Models\ProductSize;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductSizePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProductSize');
    }

    public function view(AuthUser $authUser, ProductSize $productSize): bool
    {
        return $authUser->can('View:ProductSize');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProductSize');
    }

    public function update(AuthUser $authUser, ProductSize $productSize): bool
    {
        return $authUser->can('Update:ProductSize');
    }

    public function delete(AuthUser $authUser, ProductSize $productSize): bool
    {
        return $authUser->can('Delete:ProductSize');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProductSize');
    }

    public function restore(AuthUser $authUser, ProductSize $productSize): bool
    {
        return $authUser->can('Restore:ProductSize');
    }

    public function forceDelete(AuthUser $authUser, ProductSize $productSize): bool
    {
        return $authUser->can('ForceDelete:ProductSize');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProductSize');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProductSize');
    }

    public function replicate(AuthUser $authUser, ProductSize $productSize): bool
    {
        return $authUser->can('Replicate:ProductSize');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProductSize');
    }

}