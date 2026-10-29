<?php

namespace Tests\Unit;

use App\Enums\Certification;
use App\Enums\Role;
use PHPUnit\Framework\TestCase;

/** The ranking rules the schedule will use to decide who can fill a seat. */
class CertificationTest extends TestCase
{
    public function test_higher_certifications_meet_lower_requirements(): void
    {
        $this->assertTrue(Certification::Paramedic->meets(Certification::Emt));
        $this->assertTrue(Certification::Aemt->meets(Certification::Emt));
        $this->assertTrue(Certification::Emt->meets(Certification::Emt));
    }

    public function test_lower_certifications_do_not_meet_higher_requirements(): void
    {
        $this->assertFalse(Certification::Emt->meets(Certification::Paramedic));
        $this->assertFalse(Certification::Aemt->meets(Certification::Paramedic));
    }

    public function test_roles_include_the_roles_below_them(): void
    {
        $this->assertTrue(Role::Administrator->atLeast(Role::Supervisor));
        $this->assertTrue(Role::Supervisor->atLeast(Role::Crew));
        $this->assertFalse(Role::Crew->atLeast(Role::Supervisor));
    }
}
