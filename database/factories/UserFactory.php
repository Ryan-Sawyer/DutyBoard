<?php

namespace Database\Factories;

use App\Enums\Certification;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Makes made-up users for tests and sample data.
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            // Letters and numbers only, to match the username rule.
            'username' => fake()->unique()->regexify('[a-z]{6}[0-9]{2}'),
            'password' => static::$password ??= Hash::make('password'),
            'role' => Role::Crew,
            'certification' => Certification::Emt,
            'remember_token' => Str::random(10),
        ];
    }

    public function supervisor(): static
    {
        return $this->state(['role' => Role::Supervisor]);
    }

    public function administrator(): static
    {
        return $this->state(['role' => Role::Administrator]);
    }

    public function deactivated(): static
    {
        return $this->state(['deactivated_at' => now()]);
    }
}
