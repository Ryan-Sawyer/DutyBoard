<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

/** A12: the audit trail records who did what, and it can't be rewritten. */
class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_a12_entries_record_who_did_what_and_when(): void
    {
        $admin = User::factory()->administrator()->create();
        $person = User::factory()->create();

        $this->actingAs($admin);
        $entry = AuditLog::record('Deactivated user', $person);

        $this->assertSame($admin->id, $entry->user_id);
        $this->assertTrue($entry->subject->is($person));
        $this->assertNotNull($entry->created_at);
    }

    public function test_a12_entries_cannot_be_changed(): void
    {
        $entry = AuditLog::record('Something happened');

        $this->expectException(LogicException::class);
        $entry->update(['action' => 'Something else happened']);
    }

    public function test_a12_entries_cannot_be_deleted(): void
    {
        $entry = AuditLog::record('Something happened');

        $this->expectException(LogicException::class);
        $entry->delete();
    }
}
