<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use App\Observers\TicketObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(TicketObserver::class)]
class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'reporter_id',
        'ticket_group',
        'status_id',
        'assigned_to_id', // <--- Añadido para permitir la asignación
    ];

    // Lógica para generar el Folio Automático
    protected static function booted()
    {
        static::creating(function ($ticket) {
            // Ejemplo: GEN-2026-0001
            $year = now()->year;
            $prefix = match ($ticket->ticket_group) {
                'SGC' => 'SGC',
                'GENERO' => 'GEN',
                'INFRAESTRUCTURA' => 'INFRA',
                default => 'TKT',
            };

            // Buscar el último número usado en este año y grupo
            $lastTicket = static::where('ticket_group', $ticket->ticket_group)
                ->whereYear('created_at', $year)
                ->latest('id')
                ->first();

            $number = $lastTicket ? (int) Str::afterLast($lastTicket->folio, '-') + 1 : 1;

            $ticket->folio = "{$prefix}-{$year}-" . str_pad((string)$number, 4, '0', STR_PAD_LEFT);
        });
    }

    protected function casts(): array
    {
        return [
            'id'             => 'integer',
            'reporter_id'    => 'integer',
            'status_id'      => 'integer',
            'assigned_to_id' => 'integer', // <--- Añadido al cast
        ];
    }

    /**
     * El Responsable del área que está atendiendo el ticket.
     * Se usa 'assigned_to_id' porque así se llama en tu migración.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    // El usuario que reportó el ticket
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function ticketSgcDetail(): HasOne
    {
        return $this->hasOne(TicketSgcDetail::class);
    }

    public function ticketInfraDetail(): HasOne
    {
        return $this->hasOne(TicketInfraDetail::class);
    }

    public function ticketGenderDetail(): HasOne
    {
        return $this->hasOne(TicketGenderDetail::class);
    }

    public function ticketComments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function ticketAttachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function ticketHistories(): HasMany
    {
        return $this->hasMany(TicketHistory::class);
    }
}
