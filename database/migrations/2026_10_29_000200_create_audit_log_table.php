<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Every create, submit, correct, approve, and delete is recorded here.
        // Rows are only ever added. See App\Models\AuditLog.
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();

            // Who did it. Null when the system did it, such as a scheduled job.
            $table->foreignId('user_id')->nullable()->constrained();

            // What they did, in plain words: "Created user", "Deactivated user".
            $table->string('action');

            // The record it was done to, if any.
            $table->nullableMorphs('subject');

            // Extra facts worth keeping, such as which fields changed.
            // Never passwords, and never patient information.
            $table->jsonb('details')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
