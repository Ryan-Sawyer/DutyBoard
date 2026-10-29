<?php

namespace App\Actions\Users;

use App\Models\AuditLog;
use App\Models\User;

/** Gives a deactivated person their access back. */
class ReactivateUser
{
    public function handle(User $user): void
    {
        $user->forceFill(['deactivated_at' => null])->save();

        AuditLog::record('Reactivated user', $user);
    }
}
