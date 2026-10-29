<?php

namespace App\Models;

use App\Enums\Certification;
use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * A person with a DutyBoard account: crew member, supervisor, or administrator.
 */
#[Fillable(['name', 'username', 'password', 'role', 'certification', 'mobile_number'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, TwoFactorAuthenticatable;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => Role::class,
            'certification' => Certification::class,
            'deactivated_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function isActive(): bool
    {
        return $this->deactivated_at === null;
    }

    public function isAdministrator(): bool
    {
        return $this->role === Role::Administrator;
    }

    /** True when the person has finished setting up two-step sign-in. */
    public function hasTwoStepSignIn(): bool
    {
        return $this->two_factor_confirmed_at !== null;
    }

    /** @param Builder<User> $query */
    public function scopeActive(Builder $query): void
    {
        $query->whereNull('deactivated_at');
    }
}
