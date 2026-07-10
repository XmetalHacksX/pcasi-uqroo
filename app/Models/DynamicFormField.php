<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynamicFormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_form_step_id',
        'name',
        'label',
        'type',
        'options',
        'placeholder',
        'helper_text',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(DynamicFormStep::class, 'dynamic_form_step_id');
    }
}
