<?php

namespace App\Actions\Users;

use App\Models\AuditLog;
use App\Models\User;

/**
 * An administrator turns off someone's two-step sign-in, for example when
 * they've lost their phone and their recovery codes. They sign in with their
 * password next time and can set it up again from their Account page.
 */
class ResetTwoStepSignIn
{
    public function handle(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        AuditLog::record('Turned off two-step sign-in for user', $user);
    }
}
