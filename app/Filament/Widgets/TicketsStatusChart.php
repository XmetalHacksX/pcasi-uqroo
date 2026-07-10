<?php

namespace App\Filament\Widgets;

use App\Enums\RolesEnum;
use App\Enums\StatusEnum;
use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class TicketsStatusChart extends ChartWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole([
            RolesEnum::SUPER_ADMIN->value,
            RolesEnum::ADMIN->value,
            RolesEnum::RESPONSABLE_SGC->value,
            RolesEnum::RESPONSABLE_GENERO->value,
            RolesEnum::RESPONSABLE_INFRAESTRUCTURA->value,
        ]);
    }

    public function getHeading(): string
    {
        return auth()->check() && auth()->user()->hasAnyRole([RolesEnum::SUPER_ADMIN->value, RolesEnum::ADMIN->value])
            ? 'Resumen Global de Estatus (UQROO)'
            : 'Resumen de Estatus (Mi Área)';
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Query base filtrada por rol Y por campus asignado (igual que TicketResource).
     */
    protected function getBaseQuery(): Builder
    {
        $user  = auth()->user();
        $query = Ticket::query();

        if ($user->hasAnyRole([RolesEnum::SUPER_ADMIN->value, RolesEnum::ADMIN->value])) {
            return $query;
        }

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

        return $query;
    }

    protected function getData(): array
    {
        $query = $this->getBaseQuery();

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad de Tickets',
                    'data'  => [
                        (clone $query)->where('status_id', StatusEnum::NUEVO->id())->count(),
                        (clone $query)->whereIn('status_id', [StatusEnum::ASIGNADO->id(), StatusEnum::EN_PROCESO->id()])->count(),
                        (clone $query)->whereIn('status_id', StatusEnum::closedIds())->count(),
                    ],
                    'backgroundColor' => [
                        '#ef4444', // Rojo    (Nuevos)
                        '#eab308', // Amarillo (En Atención)
                        '#22c55e', // Verde   (Cerrados)
                    ],
                ],
            ],
            'labels' => ['Nuevos', 'En Atención', 'Cerrados'],
        ];
    }
}
