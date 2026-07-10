<?php

namespace Database\Factories;

use App\Models\Campus;
use App\Models\Building;
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
            'campus_id' => Campus::factory(),
            'building_id' => Building::factory(),
            'location_id' => Location::factory(),
            'issue_type' => fake()->word(),
            'missing_supplies' => '{}',
            'description' => fake()->text(),
        ];
    }
}
