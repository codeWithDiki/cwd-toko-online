<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use CodeWithDiki\ProductModule\Models\ProductImage;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductImagePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProductImage');
    }

    public function view(AuthUser $authUser, ProductImage $productImage): bool
    {
        return $authUser->can('View:ProductImage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProductImage');
    }

    public function update(AuthUser $authUser, ProductImage $productImage): bool
    {
        return $authUser->can('Update:ProductImage');
    }

    public function delete(AuthUser $authUser, ProductImage $productImage): bool
    {
        return $authUser->can('Delete:ProductImage');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProductImage');
    }

    public function restore(AuthUser $authUser, ProductImage $productImage): bool
    {
        return $authUser->can('Restore:ProductImage');
    }

    public function forceDelete(AuthUser $authUser, ProductImage $productImage): bool
    {
        return $authUser->can('ForceDelete:ProductImage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProductImage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProductImage');
    }

    public function replicate(AuthUser $authUser, ProductImage $productImage): bool
    {
        return $authUser->can('Replicate:ProductImage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProductImage');
    }

}