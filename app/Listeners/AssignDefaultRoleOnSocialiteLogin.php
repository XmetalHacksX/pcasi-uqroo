<?php

namespace App\Listeners;

use App\Models\User;
use DutchCodingCompany\FilamentSocialite\Events\Registered;
use App\Enums\RolesEnum;

class AssignDefaultRoleOnSocialiteLogin
{
    public function handle(Registered $event): void
    {
        /** @var User $user */
        $user = $event->socialiteUser->getUser();

        // Solo asigna el rol si el usuario no tiene ninguno aún
        // Así no sobreescribe roles que ya tenga asignados (admin, responsable, etc.)
        if ($user->roles->isEmpty()) {
            $user->assignRole(RolesEnum::USUARIO->value);
        }
    }
}
