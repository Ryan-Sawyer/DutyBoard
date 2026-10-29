<?php

namespace Tests\Feature\Accounts;

use App\Enums\Certification;
use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * A1: An Administrator can create users with each role, and each role can
 * do only what the Users and Roles table allows.
 *
 * A12 (in part): changes to accounts are recorded in the audit trail.
 */
class PeopleAdministrationTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{Role}> */
    public static function everyRole(): array
    {
        return array_combine(
            array_map(fn (Role $r) => $r->label(), Role::cases()),
            array_map(fn (Role $r) => [$r], Role::cases()),
        );
    }

    #[DataProvider('everyRole')]
    public function test_a1_administrator_can_create_a_user_with_each_role(Role $role): void
    {
        $admin = User::factory()->administrator()->create();

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Pat Example',
            'username' => 'PExample',
            'role' => $role->value,
            'certification' => Certification::Aemt->value,
            'password' => 'starting password 2026',
            'password_confirmation' => 'starting password 2026',
        ])->assertRedirect('/admin/users')->assertSessionHasNoErrors();

        $person = User::where('username', 'pexample')->firstOrFail();
        $this->assertSame($role, $person->role);
        $this->assertSame(Certification::Aemt, $person->certification);

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $admin->id,
            'action' => 'Created user',
            'subject_id' => $person->id,
        ]);
    }

    public function test_a1_new_person_can_sign_in_with_their_starting_password(): void
    {
        $admin = User::factory()->administrator()->create();
        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Pat Example', 'username' => 'pexample',
            'role' => 'crew', 'certification' => 'emt',
            'password' => 'starting password 2026',
            'password_confirmation' => 'starting password 2026',
        ]);
        $this->post('/logout');

        $this->post('/login', ['username' => 'pexample', 'password' => 'starting password 2026']);

        $this->assertAuthenticatedAs(User::where('username', 'pexample')->first());
    }

    public function test_a1_usernames_must_be_unique(): void
    {
        $admin = User::factory()->administrator()->create();
        User::factory()->create(['username' => 'jalvarez']);

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Someone Else', 'username' => 'JALVAREZ',
            'role' => 'crew', 'certification' => 'emt',
            'password' => 'starting password 2026',
            'password_confirmation' => 'starting password 2026',
        ])->assertSessionHasErrors(['username' => 'Someone already has that username.']);
    }

    /** @return array<string, array{string}> */
    public static function nonAdministratorRoles(): array
    {
        return ['Crew' => ['crew'], 'Supervisor' => ['supervisor']];
    }

    #[DataProvider('nonAdministratorRoles')]
    public function test_a1_only_administrators_can_manage_people(string $role): void
    {
        $person = User::factory()->create(['role' => $role]);
        $other = User::factory()->create();

        $this->actingAs($person);
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/users/create')->assertForbidden();
        $this->get("/admin/users/{$other->id}/edit")->assertForbidden();
        $this->post('/admin/users', ['name' => 'X'])->assertForbidden();
        $this->post("/admin/users/{$other->id}/deactivate")->assertForbidden();

        $this->assertTrue($other->fresh()->isActive());
    }

    #[DataProvider('nonAdministratorRoles')]
    public function test_a1_people_link_is_hidden_from_non_administrators(string $role): void
    {
        $this->actingAs(User::factory()->create(['role' => $role]))
            ->get('/')->assertOk()->assertDontSee('/admin/users');
    }

    public function test_a1_administrator_can_change_role_and_certification(): void
    {
        $admin = User::factory()->administrator()->create();
        $person = User::factory()->create();

        $this->actingAs($admin)->put("/admin/users/{$person->id}", [
            'name' => $person->name, 'username' => $person->username,
            'role' => 'supervisor', 'certification' => 'paramedic',
        ])->assertSessionHasNoErrors();

        $person->refresh();
        $this->assertSame(Role::Supervisor, $person->role);
        $this->assertSame(Certification::Paramedic, $person->certification);

        $entry = AuditLog::where('action', 'Updated user')->firstOrFail();
        $this->assertEqualsCanonicalizing(['role', 'certification'], $entry->details['changed']);
        $this->assertFalse($entry->details['password_reset']);
    }

    public function test_a1_administrator_can_set_a_new_password_without_it_being_logged(): void
    {
        $admin = User::factory()->administrator()->create();
        $person = User::factory()->create();

        $this->actingAs($admin)->put("/admin/users/{$person->id}", [
            'name' => $person->name, 'username' => $person->username,
            'role' => 'crew', 'certification' => 'emt',
            'password' => 'replacement password 9',
            'password_confirmation' => 'replacement password 9',
        ])->assertSessionHasNoErrors();

        $entry = AuditLog::where('action', 'Updated user')->firstOrFail();
        $this->assertTrue($entry->details['password_reset']);
        $this->assertStringNotContainsString('replacement', json_encode($entry->details));

        $this->post('/logout');
        $this->post('/login', ['username' => $person->username, 'password' => 'replacement password 9']);
        $this->assertAuthenticatedAs($person);
    }

    public function test_a1_blank_password_on_edit_keeps_the_current_one(): void
    {
        $admin = User::factory()->administrator()->create();
        $person = User::factory()->create();

        $this->actingAs($admin)->put("/admin/users/{$person->id}", [
            'name' => 'New Name', 'username' => $person->username,
            'role' => 'crew', 'certification' => 'emt',
            'password' => '', 'password_confirmation' => '',
        ])->assertSessionHasNoErrors();

        $this->post('/logout');
        $this->post('/login', ['username' => $person->username, 'password' => 'password']);
        $this->assertAuthenticatedAs($person);
    }

    public function test_a1_administrator_can_deactivate_and_reactivate_a_person(): void
    {
        $admin = User::factory()->administrator()->create();
        $person = User::factory()->create();

        $this->actingAs($admin)->post("/admin/users/{$person->id}/deactivate");
        $this->assertFalse($person->fresh()->isActive());

        $this->post("/admin/users/{$person->id}/reactivate");
        $this->assertTrue($person->fresh()->isActive());

        $this->assertSame(
            ['Deactivated user', 'Reactivated user'],
            AuditLog::where('subject_id', $person->id)->orderBy('id')->pluck('action')->all(),
        );
    }

    public function test_a1_administrator_cannot_deactivate_themselves(): void
    {
        $admin = User::factory()->administrator()->create();

        $this->actingAs($admin)->post("/admin/users/{$admin->id}/deactivate")
            ->assertSessionHasErrors('user');

        $this->assertTrue($admin->fresh()->isActive());
    }

    public function test_a1_administrator_cannot_remove_their_own_administrator_role(): void
    {
        $admin = User::factory()->administrator()->create();

        $this->actingAs($admin)->put("/admin/users/{$admin->id}", [
            'name' => $admin->name, 'username' => $admin->username,
            'role' => 'crew', 'certification' => 'emt',
        ])->assertSessionHasErrors('role');

        $this->assertTrue($admin->fresh()->isAdministrator());
    }

    public function test_a1_administrator_can_turn_off_someones_two_step_sign_in(): void
    {
        $admin = User::factory()->administrator()->create();
        $person = User::factory()->create();
        $person->forceFill([
            'two_factor_secret' => encrypt('SECRET'),
            'two_factor_recovery_codes' => encrypt('[]'),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->actingAs($admin)->post("/admin/users/{$person->id}/reset-two-step");

        $this->assertFalse($person->fresh()->hasTwoStepSignIn());
        $this->assertDatabaseHas('audit_log', ['action' => 'Turned off two-step sign-in for user', 'subject_id' => $person->id]);
    }

    public function test_a1_people_list_shows_everyone(): void
    {
        $admin = User::factory()->administrator()->create(['name' => 'Dana Whitfield']);
        User::factory()->create(['name' => 'Jordan Alvarez']);
        User::factory()->deactivated()->create(['name' => 'Former Medic']);

        $this->actingAs($admin)->get('/admin/users')
            ->assertOk()
            ->assertSeeInOrder(['Dana Whitfield', 'Jordan Alvarez', 'Former Medic'])
            ->assertSee('Deactivated');
    }
}
