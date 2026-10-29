<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Sample data is made up. It must never be loaded into a real
        // agency's database, where it would create accounts with a known password.
        if (app()->environment('production')) {
            throw new RuntimeException('Sample data can only be loaded in development. Set APP_ENV to local.');
        }

        $this->call(SampleAgencySeeder::class);
    }
}
