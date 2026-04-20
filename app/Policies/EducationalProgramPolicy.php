<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EducationalProgram;
use Illuminate\Auth\Access\HandlesAuthorization;

class EducationalProgramPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EducationalProgram');
    }

    public function view(AuthUser $authUser, EducationalProgram $educationalProgram): bool
    {
        return $authUser->can('View:EducationalProgram');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EducationalProgram');
    }

    public function update(AuthUser $authUser, EducationalProgram $educationalProgram): bool
    {
        return $authUser->can('Update:EducationalProgram');
    }

    public function delete(AuthUser $authUser, EducationalProgram $educationalProgram): bool
    {
        return $authUser->can('Delete:EducationalProgram');
    }

    public function restore(AuthUser $authUser, EducationalProgram $educationalProgram): bool
    {
        return $authUser->can('Restore:EducationalProgram');
    }

    public function forceDelete(AuthUser $authUser, EducationalProgram $educationalProgram): bool
    {
        return $authUser->can('ForceDelete:EducationalProgram');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EducationalProgram');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EducationalProgram');
    }

    public function replicate(AuthUser $authUser, EducationalProgram $educationalProgram): bool
    {
        return $authUser->can('Replicate:EducationalProgram');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EducationalProgram');
    }

}