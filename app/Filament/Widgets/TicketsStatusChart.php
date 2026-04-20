<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class TicketsStatusChart extends ChartWidget
{
    protected static ?int $sort = 2; // Para que aparezca debajo de las tarjetas

    // Esta función es la que OCULTA la gráfica a los usuarios comunes
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole([
            'super_admin',
            'admin',
            'responsable_sgc',
            'responsable_genero',
            'responsable_infraestructura'
        ]);
    }

    // Título que cambia según el nivel de acceso
    public function getHeading(): string
    {
        // El auth()->check() salva a la consola de colapsar
        return auth()->check() && auth()->user()->hasAnyRole(['super_admin', 'admin'])
            ? 'Resumen Global de Estatus (UQROO)'
            : 'Resumen de Estatus (Mi Área)';
    }

    protected function getType(): string
    {
        return 'bar'; // Gráfica de barras
    }

    // Filtramos igual que en las tarjetas
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

        return $query;
    }

    protected function getData(): array
    {
        $query = $this->getBaseQuery();

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad de Tickets',
                    'data' => [
                        (clone $query)->where('status_id', 1)->count(), // Nuevos
                        (clone $query)->whereIn('status_id', [2, 3])->count(), // En Atención
                        (clone $query)->whereIn('status_id', [4, 5])->count(), // Cerrados
                    ],
                    'backgroundColor' => [
                        '#ef4444', // Rojo (Nuevos)
                        '#eab308', // Amarillo (En Atención)
                        '#22c55e', // Verde (Cerrados)
                    ],
                ],
            ],
            'labels' => ['Nuevos', 'En Atención', 'Cerrados'],
        ];
    }
}
