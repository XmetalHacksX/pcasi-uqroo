<?php

namespace Database\Factories;

use App\Models\AcademicDivision;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationalProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'division_id' => AcademicDivision::factory(),
            'name' => fake()->name(),
            'academic_division_id' => AcademicDivision::factory(),
        ];
    }
}
