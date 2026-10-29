<?php

namespace App\Actions\Users;

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/** An administrator changes someone's details, role, or password. */
class UpdateUser
{
    /** @param array<string, mixed> $input */
    public function handle(User $user, array $input, User $by): User
    {
        $newRole = Role::from($input['role']);

        // Administrators can't remove their own administrator role.
        // Otherwise the last administrator could lock everyone out of settings.
        if ($user->is($by) && $newRole !== Role::Administrator) {
            throw ValidationException::withMessages([
                'role' => "You can't remove your own administrator role. Ask another administrator to do it.",
            ]);
        }

        // An empty password field means "leave the password as it is".
        $passwordChanged = filled($input['password'] ?? null);
        if (! $passwordChanged) {
            unset($input['password']);
        }

        $user->fill($input);
        $changed = array_keys($user->getDirty());
        $user->save();

        // Record which fields changed, but never the password itself.
        AuditLog::record('Updated user', $user, [
            'changed' => array_values(array_diff($changed, ['password'])),
            'password_reset' => $passwordChanged,
        ]);

        return $user;
    }
}
