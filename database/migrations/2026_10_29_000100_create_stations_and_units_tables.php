<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Every agency has at least one station. Modeling stations from the
        // start means large departments don't need a migration later
        // (see docs/decisions/0003).
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // A unit is a truck: an ambulance, an engine, a squad.
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained();
            $table->string('name');
            $table->string('description')->nullable();

            // Out-of-service units don't appear on new truck checks or schedules.
            $table->timestamp('out_of_service_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
        Schema::dropIfExists('stations');
    }
};
