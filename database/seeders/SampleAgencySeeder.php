<?php

namespace Database\Seeders;

use App\Enums\Certification;
use App\Enums\Role;
use App\Models\Station;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * A small, made-up agency for development: one station, three units,
 * and a dozen staff across A, B, and C shifts.
 *
 * Every sample account's password is "dutyboard".
 * None of these people or numbers are real.
 */
class SampleAgencySeeder extends Seeder
{
    public const PASSWORD = 'dutyboard';

    public function run(): void
    {
        $station = Station::create(['name' => 'Station 1']);

        Unit::create(['station_id' => $station->id, 'name' => 'Medic 1', 'description' => 'Type I ambulance, primary']);
        Unit::create(['station_id' => $station->id, 'name' => 'Medic 2', 'description' => 'Type III ambulance, primary']);
        Unit::create(['station_id' => $station->id, 'name' => 'Medic 3', 'description' => 'Reserve, brake work pending'])
            ->forceFill(['out_of_service_at' => now()])->save();

        // [name, username, role, certification]
        $staff = [
            ['Dana Whitfield', 'dwhitfield', Role::Administrator, Certification::Paramedic],

            // A Shift
            ['Morgan Chen', 'mchen', Role::Supervisor, Certification::Paramedic],
            ['Jordan Alvarez', 'jalvarez', Role::Crew, Certification::Paramedic],
            ['Casey Brooks', 'cbrooks', Role::Crew, Certification::Emt],
            ['Riley Davis', 'rdavis', Role::Crew, Certification::Aemt],

            // B Shift
            ['Taylor Ellis', 'tellis', Role::Supervisor, Certification::Paramedic],
            ['Avery Foster', 'afoster', Role::Crew, Certification::Emt],
            ['Sam Garza', 'sgarza', Role::Crew, Certification::Paramedic],
            ['Drew Hayes', 'dhayes', Role::Crew, Certification::Emt],

            // C Shift
            ['Jamie Iverson', 'jiverson', Role::Supervisor, Certification::Paramedic],
            ['Quinn Jones', 'qjones', Role::Crew, Certification::Aemt],
            ['Reese Kim', 'rkim', Role::Crew, Certification::Paramedic],
            ['Parker Lopez', 'plopez', Role::Crew, Certification::Emt],
        ];

        foreach ($staff as [$name, $username, $role, $certification]) {
            User::create([
                'name' => $name,
                'username' => $username,
                'password' => self::PASSWORD,
                'role' => $role,
                'certification' => $certification,
            ]);
        }
    }
}
