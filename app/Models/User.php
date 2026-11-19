<?php

namespace App\Models;

// 🔥 IMPORTANTE: Cambiamos Model por Authenticatable
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser // 🔥 HEREDAMOS DE AUTHENTICATABLE
{
    use HasFactory, Notifiable, HasRoles; // 🔥 AGREGAMOS NOTIFIABLE

    protected $fillable = [
        'name',
        'email',
        'password', // Agrégalo por si acaso, aunque uses Azure
        'azure_id',
        'user_type',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active;
    }

    // --- TUS RELACIONES ---

    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function campuses(): BelongsToMany
    {
        return $this->belongsToMany(Campus::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
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
