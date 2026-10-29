<?php

namespace App\Enums;

/**
 * A person's EMS certification level.
 *
 * The schedule uses it to decide who can fill a seat. A seat that needs
 * "EMT or higher" can be filled by an EMT, an AEMT, or a Paramedic.
 */
enum Certification: string
{
    case Emt = 'emt';
    case Aemt = 'aemt';
    case Paramedic = 'paramedic';

    public function label(): string
    {
        return match ($this) {
            self::Emt => 'EMT',
            self::Aemt => 'AEMT',
            self::Paramedic => 'Paramedic',
        };
    }

    /** True when this certification meets or exceeds the one a seat requires. */
    public function meets(self $required): bool
    {
        return $this->rank() >= $required->rank();
    }

    private function rank(): int
    {
        return match ($this) {
            self::Emt => 1,
            self::Aemt => 2,
            self::Paramedic => 3,
        };
    }
}
