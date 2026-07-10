<?php

namespace App\Filament\Widgets;

use App\Enums\RolesEnum;
use App\Enums\StatusEnum;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class TicketStatsOverview extends BaseWidget
{
    /**
     * Construye la query base filtrada por rol Y por campus asignado.
     */
    protected function getBaseQuery(): Builder
    {
        $user  = auth()->user();
        $query = Ticket::query();

        // Admin y Super Admin ven todo
        if ($user->hasAnyRole([RolesEnum::SUPER_ADMIN->value, RolesEnum::ADMIN->value])) {
            return $query;
        }

        // Obtenemos los campus asignados al responsable
        $campusIds = $user->campuses->pluck('id');

        if ($user->hasRole(RolesEnum::RESPONSABLE_INFRAESTRUCTURA->value)) {
            return $query
                ->where('ticket_group', 'INFRAESTRUCTURA')
                ->whereHas('ticketInfraDetail', fn($q) => $q->whereIn('campus_id', $campusIds));
        }

        if ($user->hasRole(RolesEnum::RESPONSABLE_SGC->value)) {
            return $query
                ->where('ticket_group', 'SGC')
                ->whereHas('ticketSgcDetail', function ($q) use ($campusIds) {
                    $q->where(function ($sub) use ($campusIds) {
                        $sub->whereHas('department', fn($d) => $d->whereIn('campus_id', $campusIds))
                            ->orWhereHas('academicDivision', fn($d) => $d->whereIn('campus_id', $campusIds));
                    });
                });
        }

        if ($user->hasRole(RolesEnum::RESPONSABLE_GENERO->value)) {
            return $query
                ->where('ticket_group', 'GENERO')
                ->whereHas('ticketGenderDetail', function ($q) use ($campusIds) {
                    $q->where(function ($sub) use ($campusIds) {
                        $sub->whereHas('department', fn($d) => $d->whereIn('campus_id', $campusIds))
                            ->orWhereHas('academicDivision', fn($d) => $d->whereIn('campus_id', $campusIds))
                            ->orWhereIn('campus_id', $campusIds);
                    });
                });
        }

        // Usuario normal: solo sus propios reportes
        return $query->where('reporter_id', $user->id);
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        $tituloTotal = $user->hasAnyRole([
            RolesEnum::SUPER_ADMIN->value,
            RolesEnum::ADMIN->value,
            RolesEnum::RESPONSABLE_SGC->value,
            RolesEnum::RESPONSABLE_GENERO->value,
            RolesEnum::RESPONSABLE_INFRAESTRUCTURA->value,
        ]) ? 'Total de Tickets' : 'Mis Reportes';

        return [
            Stat::make($tituloTotal, $this->getBaseQuery()->count())
                ->description('Registrados en el sistema')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('Requieren Atención', $this->getBaseQuery()->whereIn('status_id', StatusEnum::openIds())->count())
                ->description('Nuevos, Asignados o En Proceso')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Casos Cerrados', $this->getBaseQuery()->whereIn('status_id', StatusEnum::closedIds())->count())
                ->description('Resueltos o Cancelados')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
