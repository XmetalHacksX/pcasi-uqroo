<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketSgcDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'classification' => fake()->word(),
            'department_id' => Department::factory(),
            'description' => fake()->text(),
        ];
    }
}
