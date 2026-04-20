<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
        ];
    }

    public function ticketSgcDetails(): HasMany
    {
        return $this->hasMany(TicketSgcDetail::class);
    }

    public function subdepartments()
    {
        return $this->hasMany(Subdepartment::class);
    }

    // EL PUENTE QUE FALTA
    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }
}
