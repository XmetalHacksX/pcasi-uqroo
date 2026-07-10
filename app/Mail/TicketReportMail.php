<?php

namespace App\Mail;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación y Reporte de Ticket ' . $this->ticket->folio,
            replyTo: [
                new \Illuminate\Mail\Mailables\Address(config('services.uqroo.te_escucha_email'), 'Mesa de Ayuda UAEQROO'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '
                <div style="font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-top: 5px solid #054c31; border-radius: 8px;">
                    <h2 style="color: #054c31; text-align: center;">Universidad Autónoma del Estado de Quintana Roo</h2>
                    <p>Estimado/a <strong>' . ($this->ticket->reporter->name ?? 'Usuario') . '</strong>,</p>
                    <p>Le informamos que su reporte ha sido registrado y puede darle seguimiento en el Sistema Institucional de Mesa de Ayuda.</p>
                    <p><strong>Folio:</strong> ' . $this->ticket->folio . '<br>
                    <strong>Clasificación:</strong> ' . $this->ticket->ticket_group . '</p>
                    <p>Adjunto a este correo encontrará un documento PDF con el resumen oficial de su reporte para su archivo personal.</p>
                    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                    <p style="font-size: 12px; color: #777; text-align: center;">Este es un mensaje automático, por favor no responda a este correo.</p>
                </div>
            ',
        );
    }

    public function attachments(): array
    {
        // Generamos el PDF on-the-fly a partir de la vista
        $pdf = Pdf::loadView('pdf.ticket', ['ticket' => $this->ticket]);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'Ticket_' . $this->ticket->folio . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
