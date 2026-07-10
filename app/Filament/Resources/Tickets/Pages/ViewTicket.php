<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('descargar_pdf')
                ->label('Descargar PDF')
                ->icon('heroicon-m-document-arrow-down')
                ->color('gray')
                ->action(function () {
                    $record = $this->record;
                    return response()->streamDownload(function () use ($record) {
                        echo \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ticket', ['ticket' => $record])->output();
                    }, 'Ticket_' . $record->folio . '.pdf');
                }),

            Action::make('enviar_por_correo')
                ->label('Enviar Email')
                ->icon('heroicon-m-envelope')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('¿Enviar comprobante PDF por correo?')
                ->modalDescription('Se despachará al correo del usuario que levantó este reporte.')
                ->visible(
                    fn() =>
                    auth()->user()->hasAnyRole([
                        \App\Enums\RolesEnum::SUPER_ADMIN->value,
                        \App\Enums\RolesEnum::ADMIN->value,
                        \App\Enums\RolesEnum::RESPONSABLE_SGC->value,
                        \App\Enums\RolesEnum::RESPONSABLE_GENERO->value,
                        \App\Enums\RolesEnum::RESPONSABLE_INFRAESTRUCTURA->value
                    ])
                )
                ->action(function () {
                    $record = $this->record;
                    if ($record->reporter?->email) {
                        \Illuminate\Support\Facades\Mail::to($record->reporter->email)
                            ->send(new \App\Mail\TicketReportMail($record));
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Correo y PDF enviados exitosamente')
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('No se pudo enviar')
                            ->body('El usuario no tiene correo registrado.')
                            ->danger()
                            ->send();
                    }
                }),

            EditAction::make(),
        ];
    }
}
