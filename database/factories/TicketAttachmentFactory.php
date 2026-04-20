<?php

namespace Database\Factories;

use App\Models\;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'comment_id' => TicketComment::factory(),
            'user_id' => ::factory(),
            'file_path' => fake()->word(),
            'file_name' => fake()->word(),
            'ticket_comment_id' => TicketComment::factory(),
        ];
    }
}
