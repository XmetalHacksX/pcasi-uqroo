<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use App\Models\Ticket;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab; // Respetando tu importación original
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Enums\StatusEnum;
use App\Enums\RolesEnum;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $user = Auth::user();

        // ── SUPER ADMIN Y ADMIN ──
        if ($user->hasAnyRole([RolesEnum::SUPER_ADMIN->value, RolesEnum::ADMIN->value])) {
            return [
                'todos' => Tab::make('Visión Global')
                    ->badge(fn() => TicketResource::getEloquentQuery()->count())
                    ->modifyQueryUsing(fn(Builder $query) => $query),

                'pendientes_global' => Tab::make('Atención Requerida')
                    ->badge(
                        fn() => TicketResource::getEloquentQuery()
                            ->whereNull('assigned_to_id')
                            ->where('status_id', StatusEnum::NUEVO->id())
                            ->count()
                    )
                    ->modifyQueryUsing(
                        fn(Builder $query) => $query
                            ->whereNull('assigned_to_id')
                            ->where('status_id', StatusEnum::NUEVO->id())
                    ),

                'en_proceso_global' => Tab::make('En Proceso Global')
                    ->badge(
                        fn() => TicketResource::getEloquentQuery()
                            ->whereNotNull('assigned_to_id')
                            ->whereNotIn('status_id', StatusEnum::closedIds()) // Excluye Resuelto y Cancelado
                            ->count()
                    )
                    ->modifyQueryUsing(
                        fn(Builder $query) => $query
                            ->whereNotNull('assigned_to_id')
                            ->whereNotIn('status_id', StatusEnum::closedIds())
                    ),
            ];
        }

        // ── RESPONSABLES DE ÁREA ──
        if ($user->hasAnyRole([
            RolesEnum::RESPONSABLE_SGC->value,
            RolesEnum::RESPONSABLE_GENERO->value,
            RolesEnum::RESPONSABLE_INFRAESTRUCTURA->value
        ])) {
            return [
                'bandeja_entrada' => Tab::make('Bandeja de Entrada')
                    ->badge(
                        fn() => TicketResource::getEloquentQuery()
                            ->whereNull('assigned_to_id')
                            ->where('status_id', StatusEnum::NUEVO->id()) // Solo los Nuevos
                            ->count()
                    )
                    ->modifyQueryUsing(
                        fn(Builder $query) => $query
                            ->whereNull('assigned_to_id')
                            ->where('status_id', StatusEnum::NUEVO->id())
                    ),

                'mis_atenciones' => Tab::make('Mis Tickets en Proceso')
                    ->badge(
                        fn() => TicketResource::getEloquentQuery()
                            ->where('assigned_to_id', $user->id)
                            ->whereNotIn('status_id', StatusEnum::closedIds()) // Asignados y En Proceso
                            ->count()
                    )
                    ->modifyQueryUsing(
                        fn(Builder $query) => $query
                            ->where('assigned_to_id', $user->id)
                            ->whereNotIn('status_id', StatusEnum::closedIds())
                    ),

                'mis_resueltos' => Tab::make('Mi Historial')
                    ->badge(
                        fn() => TicketResource::getEloquentQuery()
                            ->where('assigned_to_id', $user->id)
                            ->whereIn('status_id', StatusEnum::closedIds()) // Resueltos o Cancelados
                            ->count()
                    )
                    ->modifyQueryUsing(
                        fn(Builder $query) => $query
                            ->where('assigned_to_id', $user->id)
                            ->whereIn('status_id', StatusEnum::closedIds())
                    ),
            ];
        }

        // ── USUARIO NORMAL ──
        return [
            'todos' => Tab::make('Todos mis reportes')
                ->badge(
                    fn() => TicketResource::getEloquentQuery()
                        ->where('reporter_id', $user->id)
                        ->count()
                )
                ->modifyQueryUsing(fn(Builder $query) => $query->where('reporter_id', $user->id)),

            'activos' => Tab::make('Activos')
                ->badge(
                    fn() => TicketResource::getEloquentQuery()
                        ->where('reporter_id', $user->id)
                        ->whereNotIn('status_id', StatusEnum::closedIds()) // Todo lo que no esté cerrado
                        ->count()
                )
                ->modifyQueryUsing(
                    fn(Builder $query) => $query
                        ->where('reporter_id', $user->id)
                        ->whereNotIn('status_id', StatusEnum::closedIds())
                ),

            'cerrados' => Tab::make('Cerrados / Resueltos')
                ->badge(
                    fn() => TicketResource::getEloquentQuery()
                        ->where('reporter_id', $user->id)
                        ->whereIn('status_id', StatusEnum::closedIds()) // Solo los finalizados
                        ->count()
                )
                ->modifyQueryUsing(
                    fn(Builder $query) => $query
                        ->where('reporter_id', $user->id)
                        ->whereIn('status_id', StatusEnum::closedIds())
                ),
        ];
    }
}
