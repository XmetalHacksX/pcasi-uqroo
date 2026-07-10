<?php

namespace App\Filament\Widgets;

use App\Enums\RolesEnum;
use App\Enums\StatusEnum;
use App\Models\Ticket;
use Filament\Widgets\ChartWidget;

class TicketsByTypeChart extends ChartWidget
{
    protected static ?int $sort = 3; // Se mostrará a un lado o debajo de la primera gráfica

    // Filtro de seguridad: SOLO visible para los directivos/administradores
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole([RolesEnum::SUPER_ADMIN->value, RolesEnum::ADMIN->value]);
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
                        Ticket::where('ticket_group', 'SGC')->where('status_id', StatusEnum::NUEVO->id())->count(),
                        Ticket::where('ticket_group', 'INFRAESTRUCTURA')->where('status_id', StatusEnum::NUEVO->id())->count(),
                        Ticket::where('ticket_group', 'GENERO')->where('status_id', StatusEnum::NUEVO->id())->count(),
                    ],
                    'backgroundColor' => '#ef4444', // Rojo
                ],
                [
                    'label' => 'En Atención',
                    'data' => [
                        Ticket::where('ticket_group', 'SGC')->whereIn('status_id', [StatusEnum::ASIGNADO->id(), StatusEnum::EN_PROCESO->id()])->count(),
                        Ticket::where('ticket_group', 'INFRAESTRUCTURA')->whereIn('status_id', [StatusEnum::ASIGNADO->id(), StatusEnum::EN_PROCESO->id()])->count(),
                        Ticket::where('ticket_group', 'GENERO')->whereIn('status_id', [StatusEnum::ASIGNADO->id(), StatusEnum::EN_PROCESO->id()])->count(),
                    ],
                    'backgroundColor' => '#eab308', // Amarillo
                ],
                [
                    'label' => 'Cerrados',
                    'data' => [
                        Ticket::where('ticket_group', 'SGC')->whereIn('status_id', StatusEnum::closedIds())->count(),
                        Ticket::where('ticket_group', 'INFRAESTRUCTURA')->whereIn('status_id', StatusEnum::closedIds())->count(),
                        Ticket::where('ticket_group', 'GENERO')->whereIn('status_id', StatusEnum::closedIds())->count(),
                    ],
                    'backgroundColor' => '#22c55e', // Verde
                ],
            ],
            // Estas son las categorías (X axis) basadas en los tipos de tu imagen
            'labels' => ['SGC', 'Infraestructura', 'Género'],
        ];
    }
}
