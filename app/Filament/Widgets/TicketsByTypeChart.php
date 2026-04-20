<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;

class TicketsByTypeChart extends ChartWidget
{
    protected static ?int $sort = 3; // Se mostrará a un lado o debajo de la primera gráfica

    // Filtro de seguridad: SOLO visible para los directivos/administradores
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    public function getHeading(): string
    {
        return 'Comparativa de Estatus por Área';
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Nuevos',
                    'data' => [
                        Ticket::where('ticket_group', 'SGC')->where('status_id', 1)->count(),
                        Ticket::where('ticket_group', 'INFRAESTRUCTURA')->where('status_id', 1)->count(),
                        Ticket::where('ticket_group', 'GENERO')->where('status_id', 1)->count(),
                    ],
                    'backgroundColor' => '#ef4444', // Rojo
                ],
                [
                    'label' => 'En Atención',
                    'data' => [
                        Ticket::where('ticket_group', 'SGC')->whereIn('status_id', [2, 3])->count(),
                        Ticket::where('ticket_group', 'INFRAESTRUCTURA')->whereIn('status_id', [2, 3])->count(),
                        Ticket::where('ticket_group', 'GENERO')->whereIn('status_id', [2, 3])->count(),
                    ],
                    'backgroundColor' => '#eab308', // Amarillo
                ],
                [
                    'label' => 'Cerrados',
                    'data' => [
                        Ticket::where('ticket_group', 'SGC')->whereIn('status_id', [4, 5])->count(),
                        Ticket::where('ticket_group', 'INFRAESTRUCTURA')->whereIn('status_id', [4, 5])->count(),
                        Ticket::where('ticket_group', 'GENERO')->whereIn('status_id', [4, 5])->count(),
                    ],
                    'backgroundColor' => '#22c55e', // Verde
                ],
            ],
            // Estas son las categorías (X axis) basadas en los tipos de tu imagen
            'labels' => ['SGC', 'Infraestructura', 'Género'],
        ];
    }
}
