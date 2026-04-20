<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketGenderDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id',
        'manifestation_type',
        'reported_person_name',
        'reported_person_type',
        'chronological_narrative',
        'extended_narrative',
        'has_evidence',
        'witnesses_details',
        'needs_psychological_support',
        'communicated_to',
        'communication_results',

        // ¡LOS NUEVOS CAMPOS QUE FALTABAN!
        'department_id',
        'subdepartment_id',
        'academic_division_id',
        'educational_program_id',
        'building_id',
        'location_id',
        'reported_person_details', // El cargo opcional que agregamos
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
            'ticket_id' => 'integer',
            'has_evidence' => 'boolean',
            'needs_psychological_support' => 'boolean',
            'communicated_to' => 'array',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    // ─── RUTAS PARA QUE FILAMENT ENCUENTRE LOS NOMBRES ───

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function subdepartment(): BelongsTo
    {
        return $this->belongsTo(Subdepartment::class);
    }

    public function academicDivision(): BelongsTo
    {
        return $this->belongsTo(AcademicDivision::class);
    }

    public function educationalProgram(): BelongsTo
    {
        return $this->belongsTo(EducationalProgram::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
