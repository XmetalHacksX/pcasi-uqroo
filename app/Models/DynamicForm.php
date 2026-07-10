<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'description',
    ];

    public function steps(): HasMany
    {
        return $this->hasMany(DynamicFormStep::class)->orderBy('sort_order');
    }

    public function fields()
    {
        return $this->hasManyThrough(DynamicFormField::class, DynamicFormStep::class);
    }
}
