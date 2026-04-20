<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AcademicDivision;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcademicDivisionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AcademicDivision');
    }

    public function view(AuthUser $authUser, AcademicDivision $academicDivision): bool
    {
        return $authUser->can('View:AcademicDivision');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AcademicDivision');
    }

    public function update(AuthUser $authUser, AcademicDivision $academicDivision): bool
    {
        return $authUser->can('Update:AcademicDivision');
    }

    public function delete(AuthUser $authUser, AcademicDivision $academicDivision): bool
    {
        return $authUser->can('Delete:AcademicDivision');
    }

    public function restore(AuthUser $authUser, AcademicDivision $academicDivision): bool
    {
        return $authUser->can('Restore:AcademicDivision');
    }

    public function forceDelete(AuthUser $authUser, AcademicDivision $academicDivision): bool
    {
        return $authUser->can('ForceDelete:AcademicDivision');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AcademicDivision');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AcademicDivision');
    }

    public function replicate(AuthUser $authUser, AcademicDivision $academicDivision): bool
    {
        return $authUser->can('Replicate:AcademicDivision');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AcademicDivision');
    }

}