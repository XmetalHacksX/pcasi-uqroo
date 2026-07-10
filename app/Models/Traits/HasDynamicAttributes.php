<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Schema;

trait HasDynamicAttributes
{
    protected static function bootHasDynamicAttributes(): void
    {
        static::saving(function ($model) {
            $columns = Schema::getColumnListing($model->getTable());

            $extra = [];
            foreach ($model->attributes as $key => $value) {
                // Ignore real columns and standard Laravel model fields
                if (!in_array($key, $columns) && !str_starts_with($key, '_') && $key !== 'relations') {
                    $extra[$key] = $value;
                    unset($model->attributes[$key]);
                }
            }

            if (!empty($extra)) {
                $existing = $model->extra_attributes;
                if (is_string($existing)) {
                    $existing = json_decode($existing, true) ?? [];
                } elseif (!is_array($existing)) {
                    $existing = [];
                }

                $model->extra_attributes = json_encode(array_merge($existing, $extra));
            }
        });

        static::retrieved(function ($model) {
            $model->unpackExtraAttributes();
        });

        static::saved(function ($model) {
            $model->unpackExtraAttributes();
        });
    }

    public function unpackExtraAttributes(): void
    {
        $extra = $this->extra_attributes;
        if ($extra) {
            if (is_string($extra)) {
                $extra = json_decode($extra, true) ?? [];
            }
            if (is_array($extra)) {
                foreach ($extra as $key => $value) {
                    $this->attributes[$key] = $value;
                }
            }
        }
    }

    /**
     * Override isFillable to allow dynamic attributes to be mass-assigned.
     */
    public function isFillable($key): bool
    {
        return true;
    }

    /**
     * Override fillableFromArray to bypass Laravel's fillable filtering.
     */
    protected function fillableFromArray(array $attributes): array
    {
        return $attributes;
    }

    /**
     * Cast extra_attributes dynamically to array if accessed.
     */
    public function getExtraAttributesAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return $value;
    }
}
