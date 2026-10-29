<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // People sign in with a username, not an email address.
            // Many crew members don't have an agency email, and DutyBoard
            // doesn't send email (see docs/decisions/0004).
            $table->string('username')->unique();
            $table->string('password');

            // App\Enums\Role and App\Enums\Certification.
            $table->string('role');
            $table->string('certification');

            // Used for text notifications in a later slice. Optional for now.
            $table->string('mobile_number')->nullable();

            // Deactivated people can't sign in, but their past entries stay.
            $table->timestamp('deactivated_at')->nullable();

            // Two-step sign-in (an authenticator app), managed by Laravel Fortify.
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        // Laravel clears pending reset tokens whenever a password changes,
        // so it expects this table to exist. DutyBoard doesn't send reset
        // links by email (docs/decisions/0004), so it normally stays empty.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
