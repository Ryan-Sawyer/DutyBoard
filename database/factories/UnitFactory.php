<?php

namespace Database\Factories;

use App\Models\Station;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Unit> */
class UnitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'station_id' => Station::factory(),
            'name' => 'Medic '.fake()->unique()->numberBetween(1, 99),
            'description' => 'Type I ambulance',
        ];
    }

    public function outOfService(): static
    {
        return $this->state(['out_of_service_at' => now()]);
    }
}
