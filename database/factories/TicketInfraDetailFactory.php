<?php

namespace Database\Factories;

use App\Models\;
use App\Models\Location;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketInfraDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'campus_id' => ::factory(),
            'building_id' => ::factory(),
            'location_id' => Location::factory(),
            'issue_type' => fake()->word(),
            'missing_supplies' => '{}',
            'description' => fake()->text(),
        ];
    }
}
