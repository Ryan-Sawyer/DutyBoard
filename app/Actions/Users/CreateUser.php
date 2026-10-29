<?php

namespace App\Actions\Users;

use App\Models\AuditLog;
use App\Models\User;

/**
 * An administrator creates an account. There's no self sign-up.
 *
 * The administrator sets a starting password and gives it to the person,
 * who should change it from their Account page after signing in.
 */
class CreateUser
{
    /** @param array{name: string, username: string, role: string, certification: string, mobile_number?: string|null, password: string} $input */
    public function handle(array $input): User
    {
        $user = User::create($input);

        AuditLog::record('Created user', $user, [
            'username' => $user->username,
            'role' => $user->role->value,
            'certification' => $user->certification->value,
        ]);

        return $user;
    }
}
