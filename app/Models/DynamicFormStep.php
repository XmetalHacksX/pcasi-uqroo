<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicFormStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynamic_form_id',
        'title',
        'description',
        'icon',
        'sort_order',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(DynamicForm::class, 'dynamic_form_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(DynamicFormField::class)->orderBy('sort_order');
    }
}
