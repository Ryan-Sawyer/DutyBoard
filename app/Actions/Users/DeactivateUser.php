<?php

namespace App\Actions\Users;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Turns off someone's access. They can't sign in, and any session they
 * have open ends on their next click. Their past entries stay in the
 * records, so nothing they did is lost.
 */
class DeactivateUser
{
    public function handle(User $user, User $by): void
    {
        if ($user->is($by)) {
            throw ValidationException::withMessages([
                'user' => "You can't deactivate your own account.",
            ]);
        }

        $user->forceFill(['deactivated_at' => now()])->save();

        AuditLog::record('Deactivated user', $user);
    }
}
