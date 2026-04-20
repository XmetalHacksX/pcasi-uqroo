<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Ticket;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Enums\RolesEnum; // <-- Importamos tu Enum de roles

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * INTERCEPTOR: Si el usuario es Super Admin, siempre devuelve true.
     * Esto hace que ignore todas las reglas de abajo y tenga acceso total a todo.
     */
    public function before(AuthUser $authUser, string $ability): ?bool
    {
        if ($authUser->hasRole(RolesEnum::SUPER_ADMIN->value)) {
            return true;
        }

        return null;
    }

    public function viewAny(AuthUser $authUser): bool
    {
        // Devuelve true para que el menú "Mesa de Ayuda" aparezca para todos.
        // El filtrado real de qué ve cada rol ya está en TicketResource::getEloquentQuery()
        return true;
    }

    public function view(AuthUser $authUser, Ticket $ticket): bool
    {
        return true; 
    }

    public function create(AuthUser $authUser): bool
    {
        return true; 
    }

    public function update(AuthUser $authUser, Ticket $ticket): bool
    {
        return true; 
    }

    public function delete(AuthUser $authUser, Ticket $ticket): bool
    {
        return $authUser->can('delete_ticket');
    }

    public function restore(AuthUser $authUser, Ticket $ticket): bool
    {
        return $authUser->can('restore_ticket');
    }

    public function forceDelete(AuthUser $authUser, Ticket $ticket): bool
    {
        return $authUser->can('force_delete_ticket');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_ticket');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_ticket');
    }

    public function replicate(AuthUser $authUser, Ticket $ticket): bool
    {
        return $authUser->can('replicate_ticket');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_ticket');
    }
}