<?php

namespace App\Observers;

use App\Enums\StatusEnum;
use App\Models\Ticket;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketResolvedMail;

class TicketObserver
{
    public function created(Ticket $ticket): void
    {
        $ticket->ticketHistories()->create([
            'user_id' => auth()->id() ?? $ticket->reporter_id,
            'action' => 'Creado',
            'old_value' => null,
            'new_value' => "Folio generado: {$ticket->folio}",
        ]);
    }

    public function updated(Ticket $ticket): void
    {
        // Registro de cambio de estado
        if ($ticket->wasChanged('status_id')) {
            $oldStatus = \App\Models\Status::find($ticket->getOriginal('status_id'))?->name ?? 'Desconocido';
            $newStatus = $ticket->status?->name ?? 'Desconocido';
            
            $ticket->ticketHistories()->create([
                'user_id' => auth()->id() ?? $ticket->reporter_id,
                'action' => 'Cambio de Estado',
                'old_value' => $oldStatus,
                'new_value' => $newStatus,
            ]);
        }

        // Registro de asignación
        if ($ticket->wasChanged('assigned_to_id')) {
            $oldUser = \App\Models\User::find($ticket->getOriginal('assigned_to_id'))?->name ?? 'Sin asignar';
            $newUser = $ticket->assignedTo?->name ?? 'Sin asignar';

            $ticket->ticketHistories()->create([
                'user_id' => auth()->id() ?? $ticket->reporter_id,
                'action' => 'Asignación',
                'old_value' => $oldUser,
                'new_value' => $newUser,
            ]);
        }

        // Revisamos si el status cambió, y si el nuevo status es Resuelto
        if ($ticket->wasChanged('status_id') && $ticket->status_id === StatusEnum::RESUELTO->id()) {
            if ($ticket->reporter?->email) {
                Mail::to($ticket->reporter->email)->send(new TicketResolvedMail($ticket));
            }
        }
    }
}
