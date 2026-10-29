<?php

namespace App\Policies;

use App\Models\User;

/** Who can manage accounts. Only administrators can. */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrator();
    }

    public function create(User $user): bool
    {
        return $user->isAdministrator();
    }

    public function update(User $user, User $account): bool
    {
        return $user->isAdministrator();
    }
}
