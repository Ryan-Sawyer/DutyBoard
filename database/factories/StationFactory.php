<?php

namespace Database\Factories;

use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Station> */
class StationFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => 'Station '.fake()->unique()->numberBetween(1, 99)];
    }
}
