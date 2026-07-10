<?php

namespace Database\Factories;

use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'folio' => fake()->word(),
            'reporter_id' => User::factory(),
            'ticket_group' => fake()->randomElement(["SGC","GENERO","INFRAESTRUCTURA"]),
            'status_id' => Status::factory(),
            'assigned_to_id' => null,
        ];
    }
}
