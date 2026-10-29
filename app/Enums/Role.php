<?php

namespace App\Enums;

/**
 * What a person is allowed to do in DutyBoard.
 *
 * Each role can do everything the role before it can:
 * Crew < Supervisor < Administrator.
 */
enum Role: string
{
    /** Completes truck checks, enters mileage, views the published schedule. */
    case Crew = 'crew';

    /** Builds schedules, reviews truck checks, corrects entries, approves shift requests. */
    case Supervisor = 'supervisor';

    /** Everything a supervisor can do, plus managing accounts, units, and settings. */
    case Administrator = 'administrator';

    public function label(): string
    {
        return match ($this) {
            self::Crew => 'Crew',
            self::Supervisor => 'Supervisor',
            self::Administrator => 'Administrator',
        };
    }

    /** True when this role includes everything the given role can do. */
    public function atLeast(self $role): bool
    {
        return $this->rank() >= $role->rank();
    }

    private function rank(): int
    {
        return match ($this) {
            self::Crew => 1,
            self::Supervisor => 2,
            self::Administrator => 3,
        };
    }
}
