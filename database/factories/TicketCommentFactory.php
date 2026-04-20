<?php

namespace Database\Factories;

use App\Models\;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => ::factory(),
            'body' => fake()->text(),
            'is_internal' => fake()->boolean(),
        ];
    }
}
