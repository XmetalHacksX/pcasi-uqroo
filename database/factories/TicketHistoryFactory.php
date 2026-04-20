<?php

namespace Database\Factories;

use App\Models\;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => ::factory(),
            'action' => fake()->word(),
            'old_value' => fake()->word(),
            'new_value' => fake()->word(),
        ];
    }
}
