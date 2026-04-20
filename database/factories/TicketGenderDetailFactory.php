<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketGenderDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'manifestation_type' => fake()->word(),
            'reported_person_name' => fake()->word(),
            'reported_person_type' => fake()->word(),
            'chronological_narrative' => fake()->text(),
            'extended_narrative' => fake()->text(),
            'has_evidence' => fake()->boolean(),
            'witnesses_details' => fake()->text(),
            'needs_psychological_support' => fake()->boolean(),
            'communicated_to' => '{}',
            'communication_results' => fake()->text(),
        ];
    }
}
