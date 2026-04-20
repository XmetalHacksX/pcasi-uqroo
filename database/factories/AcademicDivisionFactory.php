<?php

namespace Database\Factories;

use App\Models\Campus;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicDivisionFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'campus_id' => Campus::factory(),
            'name' => fake()->name(),
            'acronym' => fake()->word(),
        ];
    }
}
