<?php

namespace App\Actions\Fortify;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

/** Someone changing their own password from the Account page. */
class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /** @param array<string, string> $input */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => "That isn't your current password.",
        ])->validateWithBag('updatePassword');

        $user->forceFill(['password' => $input['password']])->save();

        AuditLog::record('Changed own password', $user);
    }
}
