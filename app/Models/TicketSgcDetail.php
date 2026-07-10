<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketSgcDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_type',
        'reported_person_name',
        'area_type',
        'department_id',
        'subdepartment_id',
        'academic_division_id',
        'educational_program_id',
        'classification',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'ticket_id' => 'integer',
            'educational_program_id' => 'integer',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    // --- RELACIONES CON LOS CATÁLOGOS ---
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
}

