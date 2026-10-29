<?php

namespace App\Console\Commands;

use App\Actions\Users\CreateUser;
use App\Enums\Certification;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Creates the first administrator on a new install, so someone can sign in
 * and add everyone else from the People screen.
 *
 *   docker compose exec app php artisan dutyboard:create-admin
 */
class CreateAdministrator extends Command
{
    protected $signature = 'dutyboard:create-admin';

    protected $description = 'Create an administrator account (use this once on a new install)';

    public function handle(CreateUser $createUser): int
    {
        $input = [
            'name' => $this->ask('Full name'),
            'username' => strtolower((string) $this->ask('Username')),
            'certification' => $this->choice(
                'Certification',
                array_map(fn ($c) => $c->value, Certification::cases()),
                Certification::Paramedic->value,
            ),
            'password' => $this->secret('Password (at least 12 characters, letters and numbers)'),
            'role' => Role::Administrator->value,
        ];

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'alpha_dash:ascii', 'max:50', Rule::unique(User::class)],
            'password' => ['required', Password::default()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = $createUser->handle($input);
        $this->info("Created administrator {$user->name}. Sign in with username {$user->username}.");

        return self::SUCCESS;
    }
}
