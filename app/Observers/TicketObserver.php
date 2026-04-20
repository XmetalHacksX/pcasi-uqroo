<?php

namespace App\Observers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketResolvedMail;

class TicketObserver
{
    public function updated(Ticket $ticket): void
    {
        // Revisamos si el status cambió, y si el nuevo status es 4 (Resuelto)
        if ($ticket->isDirty('status_id') && $ticket->status_id === 4) {
            if ($ticket->reporter?->email) {
                Mail::to($ticket->reporter->email)->send(new TicketResolvedMail($ticket));
            }
        }
    }
}
