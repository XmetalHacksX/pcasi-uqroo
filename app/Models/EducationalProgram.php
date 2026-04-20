<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducationalProgram extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'academic_division_id',
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
            'division_id' => 'integer',
            'academic_division_id' => 'integer',
        ];
    }

    public function academicDivision(): BelongsTo
    {
        return $this->belongsTo(AcademicDivision::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(AcademicDivision::class);
    }
}
