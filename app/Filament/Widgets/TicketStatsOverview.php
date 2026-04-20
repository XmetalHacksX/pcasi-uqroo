<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class TicketStatsOverview extends BaseWidget
{
    // Función central para filtrar los datos según el rol
    protected function getBaseQuery(): Builder
    {
        $user = auth()->user();
        $query = Ticket::query();

        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return $query;
        }
        if ($user->hasRole('responsable_sgc')) {
            return $query->where('ticket_group', 'SGC');
        }
        if ($user->hasRole('responsable_genero')) {
            return $query->where('ticket_group', 'GENERO');
        }
        if ($user->hasRole('responsable_infraestructura')) {
            return $query->where('ticket_group', 'INFRAESTRUCTURA');
        }

        // Si no es admin ni encargado, es usuario normal
        return $query->where('reporter_id', $user->id);
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        // Cambiamos el título de la primera tarjeta dependiendo de quién la vea
        $tituloTotal = $user->hasAnyRole(['super_admin', 'admin', 'responsable_sgc', 'responsable_genero', 'responsable_infraestructura'])
            ? 'Total de Tickets'
            : 'Mis Reportes';

        return [
            Stat::make($tituloTotal, $this->getBaseQuery()->count())
                ->description('Registrados en el sistema')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('Requieren Atención', $this->getBaseQuery()->whereIn('status_id', [1, 2, 3])->count())
                ->description('Nuevos, Asignados o En Proceso')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Casos Cerrados', $this->getBaseQuery()->whereIn('status_id', [4, 5])->count())
                ->description('Resueltos o Cancelados')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
