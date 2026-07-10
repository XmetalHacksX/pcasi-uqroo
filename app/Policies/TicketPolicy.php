<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Ticket;
use App\Enums\RolesEnum;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole([
            RolesEnum::SUPER_ADMIN->value,
            RolesEnum::ADMIN->value,
            RolesEnum::RESPONSABLE_SGC->value,
            RolesEnum::RESPONSABLE_GENERO->value,
            RolesEnum::RESPONSABLE_INFRAESTRUCTURA->value
        ]) || $authUser->can('ViewAny:Ticket');
    }

    public function view(AuthUser $authUser, Ticket $ticket): bool
    {
        return $this->userHasAccessToTicket($authUser, $ticket);
    }

    public function create(AuthUser $authUser): bool
    {
        return true;
    }

    public function update(AuthUser $authUser, Ticket $ticket): bool
    {
        return $this->userHasAccessToTicket($authUser, $ticket);
    }

    /**
     * Valida de manera estricta si un usuario tiene acceso a un ticket
     * basado en su rol y en los campus que tiene asignados.
     */
    private function userHasAccessToTicket(AuthUser $authUser, Ticket $ticket): bool
    {
        // 1. Super Admin y Admin tienen acceso a absolutamente todo
        if ($authUser->hasAnyRole([RolesEnum::SUPER_ADMIN->value, RolesEnum::ADMIN->value])) {
            return true;
        }

        // 2. El reportador (usuario común) tiene acceso a su propio ticket
        if ($authUser->id === $ticket->reporter_id) {
            return true;
        }

        // Obtener los IDs de los campus asignados al responsable
        $campusIds = $authUser->campuses->pluck('id')->toArray();

        // 3. Validación estricta por grupo de ticket
        if ($ticket->ticket_group === 'GENERO') {
            // Solo el responsable de género de los campus asignados puede verlo
            if (!$authUser->hasRole(RolesEnum::RESPONSABLE_GENERO->value)) {
                return false;
            }

            $detail = $ticket->ticketGenderDetail;
            if (!$detail) {
                return false;
            }

            return in_array($detail->campus_id, $campusIds)
                || ($detail->department && in_array($detail->department->campus_id, $campusIds))
                || ($detail->academicDivision && in_array($detail->academicDivision->campus_id, $campusIds));
        }

        if ($ticket->ticket_group === 'INFRAESTRUCTURA') {
            // Solo el responsable de infraestructura de los campus asignados puede verlo
            if (!$authUser->hasRole(RolesEnum::RESPONSABLE_INFRAESTRUCTURA->value)) {
                return false;
            }

            $detail = $ticket->ticketInfraDetail;
            if (!$detail) {
                return false;
            }

            return in_array($detail->campus_id, $campusIds);
        }

        if ($ticket->ticket_group === 'SGC') {
            // Solo el responsable de SGC de los campus asignados puede verlo
            if (!$authUser->hasRole(RolesEnum::RESPONSABLE_SGC->value)) {
                return false;
            }

            $detail = $ticket->ticketSgcDetail;
            if (!$detail) {
                return false;
            }

            return ($detail->department && in_array($detail->department->campus_id, $campusIds))
                || ($detail->academicDivision && in_array($detail->academicDivision->campus_id, $campusIds));
        }

        return false;
    }

    public function delete(AuthUser $authUser, Ticket $ticket): bool
    {
        return $authUser->hasAnyRole([
            RolesEnum::SUPER_ADMIN->value,
            RolesEnum::ADMIN->value
        ]) || $authUser->can('Delete:Ticket');
    }

    public function restore(AuthUser $authUser, Ticket $ticket): bool
    {
        return $authUser->hasAnyRole([
            RolesEnum::SUPER_ADMIN->value,
            RolesEnum::ADMIN->value
        ]) || $authUser->can('Restore:Ticket');
    }

    public function forceDelete(AuthUser $authUser, Ticket $ticket): bool
    {
        return $authUser->hasAnyRole([
            RolesEnum::SUPER_ADMIN->value,
            RolesEnum::ADMIN->value
        ]) || $authUser->can('ForceDelete:Ticket');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole([
            RolesEnum::SUPER_ADMIN->value,
            RolesEnum::ADMIN->value
        ]) || $authUser->can('ForceDeleteAny:Ticket');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole([
            RolesEnum::SUPER_ADMIN->value,
            RolesEnum::ADMIN->value
        ]) || $authUser->can('RestoreAny:Ticket');
    }

    public function replicate(AuthUser $authUser, Ticket $ticket): bool
    {
        return $authUser->hasAnyRole([
            RolesEnum::SUPER_ADMIN->value,
            RolesEnum::ADMIN->value
        ]) || $authUser->can('Replicate:Ticket');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole([
            RolesEnum::SUPER_ADMIN->value,
            RolesEnum::ADMIN->value
        ]) || $authUser->can('Reorder:Ticket');
    }
}