<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'azure_id' => fake()->word(),
            'user_type' => fake()->word(),
            'is_active' => fake()->boolean(),
            'remember_token' => fake()->uuid(),
        ];
    }
}
