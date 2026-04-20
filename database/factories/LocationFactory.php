<?php

namespace Database\Factories;

use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'building_id' => Building::factory(),
            'name' => fake()->name(),
            'type' => fake()->word(),
        ];
    }
}
