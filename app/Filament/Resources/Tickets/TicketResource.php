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
     * Aquí es donde restringimos que el usuario común solo vea sus tickets.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // 1. Super Admin y Admin ven todo
        if ($user->hasAnyRole([RolesEnum::SUPER_ADMIN->value, RolesEnum::ADMIN->value])) {
            return $query;
        }

        // 2. Responsables de Área (usando los nombres exactos de tu DB)
        $groups = [];
        if ($user->hasRole(RolesEnum::RESPONSABLE_SGC->value)) $groups[] = 'SGC';
        if ($user->hasRole(RolesEnum::RESPONSABLE_GENERO->value)) $groups[] = 'GENERO';
        if ($user->hasRole(RolesEnum::RESPONSABLE_INFRAESTRUCTURA->value)) $groups[] = 'INFRAESTRUCTURA';

        if (!empty($groups)) {
            return $query->whereIn('ticket_group', $groups);
        }

        // 3. Usuario común (solo ve sus propios reportes)
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
