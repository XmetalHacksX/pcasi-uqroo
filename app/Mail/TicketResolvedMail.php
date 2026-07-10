<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketResolvedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Su Ticket ' . $this->ticket->folio . ' ha sido RESUELTO',
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
                    <p>Le informamos que su ticket con folio <strong>' . $this->ticket->folio . '</strong> ha sido marcado como <strong>RESUELTO</strong> por nuestra área correspondiente de atención institucional.</p>
                    <p>Agradecemos profundamente su reporte y su contribución para mantener un buen ambiente, instalaciones seguras y el correcto desarrollo de nuestra Universidad.</p>
                    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                    <p style="font-size: 12px; color: #777; text-align: center;">Este es un mensaje automático, por favor no responda a este correo.</p>
                </div>
            ',
        );
    }
}
