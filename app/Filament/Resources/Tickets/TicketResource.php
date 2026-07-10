<?php

namespace App\Filament\Resources\Tickets;

use App\Filament\Resources\Tickets\Pages\CreateTicket;
use App\Filament\Resources\Tickets\Pages\EditTicket;
use App\Filament\Resources\Tickets\Pages\ListTickets;
use App\Filament\Resources\Tickets\Pages\ViewTicket;
use App\Filament\Resources\Tickets\Schemas\TicketForm;
use App\Filament\Resources\Tickets\Schemas\TicketInfolist;
use App\Filament\Resources\Tickets\Tables\TicketsTable;
use App\Models\Ticket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
// IMPORTANTE: Añade esta importación para la consulta
use Illuminate\Database\Eloquent\Builder;
use App\Enums\RolesEnum;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $recordTitleAttribute = 'folio';

    protected static string|\UnitEnum|null $navigationGroup = 'Mesa de Ayuda';
    protected static ?string $modelLabel = 'Ticket';
    protected static ?string $pluralModelLabel = 'Tickets';
    protected static ?int $navigationSort = 1;

    /**
     * FILTRO DE SEGURIDAD:
     * Restringe la visibilidad de tickets por rol Y por campus asignado al responsable.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['reporter', 'status']);
        $user  = auth()->user();

        // 1. Super Admin y Admin ven absolutamente todo
        if ($user->hasAnyRole([RolesEnum::SUPER_ADMIN->value, RolesEnum::ADMIN->value])) {
            return $query;
        }

        // Obtenemos los campus asignados al responsable (tabla campus_user)
        $campusIds = $user->campuses->pluck('id');

        // 2. Responsable de Infraestructura:
        //    Solo ve tickets de Infraestructura cuyo campus_id está en sus campus asignados.
        if ($user->hasRole(RolesEnum::RESPONSABLE_INFRAESTRUCTURA->value)) {
            return $query
                ->where('ticket_group', 'INFRAESTRUCTURA')
                ->whereHas('ticketInfraDetail', fn($q) => $q->whereIn('campus_id', $campusIds));
        }

        // 3. Responsable SGC:
        //    Ve tickets SGC donde el departamento o la división académica pertenece a sus campus.
        if ($user->hasRole(RolesEnum::RESPONSABLE_SGC->value)) {
            return $query
                ->where('ticket_group', 'SGC')
                ->whereHas('ticketSgcDetail', function ($q) use ($campusIds) {
                    $q->where(function ($sub) use ($campusIds) {
                        // Vía área administrativa: department -> campus_id
                        $sub->whereHas('department', fn($d) => $d->whereIn('campus_id', $campusIds))
                            // Vía área académica: academic_division -> campus_id
                            ->orWhereHas('academicDivision', fn($d) => $d->whereIn('campus_id', $campusIds));
                    });
                });
        }

        // 4. Responsable de Género:
        //    Ve tickets de Género donde el departamento o la división académica pertenece a sus campus.
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

        // 5. Usuario común: solo ve sus propios reportes
        return $query->where('reporter_id', $user->id);
    }

    public static function form(Schema $schema): Schema
    {
        return TicketForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TicketInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'view' => ViewTicket::route('/{record}'),
            'edit' => EditTicket::route('/{record}/edit'),
        ];
    }
}
