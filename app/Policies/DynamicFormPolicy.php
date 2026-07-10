<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DynamicForm;
use App\Enums\RolesEnum;
use Illuminate\Auth\Access\HandlesAuthorization;

class DynamicFormPolicy
{
    use HandlesAuthorization;

    private function isAdmin(User $user): bool
    {
        return $user->hasAnyRole([RolesEnum::SUPER_ADMIN->value, RolesEnum::ADMIN->value]);
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, DynamicForm $dynamicForm): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, DynamicForm $dynamicForm): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, DynamicForm $dynamicForm): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, DynamicForm $dynamicForm): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, DynamicForm $dynamicForm): bool
    {
        return $this->isAdmin($user);
    }
}
