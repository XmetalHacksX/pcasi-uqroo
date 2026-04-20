<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Subdepartment;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubdepartmentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Subdepartment');
    }

    public function view(AuthUser $authUser, Subdepartment $subdepartment): bool
    {
        return $authUser->can('View:Subdepartment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Subdepartment');
    }

    public function update(AuthUser $authUser, Subdepartment $subdepartment): bool
    {
        return $authUser->can('Update:Subdepartment');
    }

    public function delete(AuthUser $authUser, Subdepartment $subdepartment): bool
    {
        return $authUser->can('Delete:Subdepartment');
    }

    public function restore(AuthUser $authUser, Subdepartment $subdepartment): bool
    {
        return $authUser->can('Restore:Subdepartment');
    }

    public function forceDelete(AuthUser $authUser, Subdepartment $subdepartment): bool
    {
        return $authUser->can('ForceDelete:Subdepartment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Subdepartment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Subdepartment');
    }

    public function replicate(AuthUser $authUser, Subdepartment $subdepartment): bool
    {
        return $authUser->can('Replicate:Subdepartment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Subdepartment');
    }

}