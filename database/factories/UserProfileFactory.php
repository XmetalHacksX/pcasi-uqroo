<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'phone' => fake()->phoneNumber(),
            'age' => fake()->numberBetween(-10000, 10000),
            'gender' => fake()->word(),
            'sex' => fake()->word(),
            'vulnerable_group' => fake()->word(),
        ];
    }
}
