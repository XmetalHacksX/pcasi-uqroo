<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use Filament\Resources\Pages\CreateRecord;

use Illuminate\Support\Facades\Mail;
use App\Mail\TicketReportMail;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function afterCreate(): void
    {
        $ticket = $this->record;
        
        $teEscuchaEmail = config('services.uqroo.te_escucha_email');

        // Enviamos al reportador si tiene correo y con copia oculta a la institución
        if ($ticket->reporter?->email) {
            Mail::to($ticket->reporter->email)
                ->bcc($teEscuchaEmail)
                ->send(new TicketReportMail($ticket));
        } else {
            // Si el reportador no tiene correo, de todos modos enviamos la notificación al correo institucional
            if ($teEscuchaEmail) {
                Mail::to($teEscuchaEmail)->send(new TicketReportMail($ticket));
            }
        }
    }
}
