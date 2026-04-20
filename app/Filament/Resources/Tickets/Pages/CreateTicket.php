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
        
        if ($ticket->reporter?->email) {
            Mail::to($ticket->reporter->email)->send(new TicketReportMail($ticket));
        }
    }
}
