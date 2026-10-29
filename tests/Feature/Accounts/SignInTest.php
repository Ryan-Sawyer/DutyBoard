<?php

namespace Tests\Feature\Accounts;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

/**
 * A2: Users can sign in with a password, and with two-step sign-in when they
 * turn it on. A deactivated user can't sign in.
 */
class SignInTest extends TestCase
{
    use RefreshDatabase;

    public function test_a2_sign_in_page_loads(): void
    {
        $this->get('/login')->assertOk()->assertSee('Sign in');
    }

    public function test_a2_signed_out_visitors_are_sent_to_sign_in(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_a2_user_can_sign_in_with_username_and_password(): void
    {
        $user = User::factory()->create(['username' => 'jalvarez']);

        $this->post('/login', ['username' => 'jalvarez', 'password' => 'password'])
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_a2_username_is_not_case_sensitive(): void
    {
        $user = User::factory()->create(['username' => 'jalvarez']);

        $this->post('/login', ['username' => 'JAlvarez', 'password' => 'password']);

        $this->assertAuthenticatedAs($user);
    }

    public function test_a2_wrong_password_is_rejected(): void
    {
        User::factory()->create(['username' => 'jalvarez']);

        $this->post('/login', ['username' => 'jalvarez', 'password' => 'not-the-password'])
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_a2_deactivated_user_cannot_sign_in(): void
    {
        $user = User::factory()->deactivated()->create(['username' => 'former']);

        $this->post('/login', ['username' => 'former', 'password' => 'password'])
            ->assertSessionHasErrors(['username' => 'This account has been deactivated. Ask an administrator if you think this is a mistake.']);

        $this->assertGuest();
        $this->assertDatabaseHas('audit_log', [
            'action' => 'Blocked sign-in by a deactivated account',
            'subject_id' => $user->id,
        ]);
    }

    public function test_a2_deactivated_user_is_signed_out_on_their_next_request(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/')->assertOk();

        $user->forceFill(['deactivated_at' => now()])->save();

        $this->get('/')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_a2_sign_in_is_rate_limited_after_five_wrong_passwords(): void
    {
        User::factory()->create(['username' => 'jalvarez']);

        foreach (range(1, 5) as $attempt) {
            $this->post('/login', ['username' => 'jalvarez', 'password' => 'wrong']);
        }

        $this->post('/login', ['username' => 'jalvarez', 'password' => 'password'])->assertStatus(429);
        $this->assertGuest();
    }

    public function test_a2_user_with_two_step_sign_in_must_enter_a_code(): void
    {
        [$user] = $this->userWithTwoStepSignIn();

        $this->post('/login', ['username' => $user->username, 'password' => 'password'])
            ->assertRedirect('/two-factor-challenge');

        $this->assertGuest();
        $this->get('/two-factor-challenge')->assertOk()->assertSee('Enter your code');
    }

    public function test_a2_correct_code_completes_two_step_sign_in(): void
    {
        [$user, $secret] = $this->userWithTwoStepSignIn();
        $this->post('/login', ['username' => $user->username, 'password' => 'password']);

        $code = (new Google2FA)->getCurrentOtp($secret);
        $this->post('/two-factor-challenge', ['code' => $code])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_a2_wrong_code_is_rejected(): void
    {
        [$user] = $this->userWithTwoStepSignIn();
        $this->post('/login', ['username' => $user->username, 'password' => 'password']);

        $this->post('/two-factor-challenge', ['code' => '000000'])->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_a2_recovery_code_works_once(): void
    {
        [$user] = $this->userWithTwoStepSignIn(['recovery-one', 'recovery-two']);

        $this->post('/login', ['username' => $user->username, 'password' => 'password']);
        $this->post('/two-factor-challenge', ['recovery_code' => 'recovery-one'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        $this->post('/logout');
        $this->post('/login', ['username' => $user->username, 'password' => 'password']);
        $this->post('/two-factor-challenge', ['recovery_code' => 'recovery-one'])->assertSessionHasErrors('recovery_code');
        $this->assertGuest();
    }

    public function test_a2_person_can_turn_on_two_step_sign_in_from_their_account(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['auth.password_confirmed_at' => time()]);

        $this->get('/account/two-step')->assertOk()->assertSee('Get an authenticator app');
        $this->post('/user/two-factor-authentication')->assertRedirect();

        $user->refresh();
        $this->assertNotNull($user->two_factor_secret);
        $this->assertFalse($user->hasTwoStepSignIn(), 'Not on until they enter a code from the app.');

        $code = (new Google2FA)->getCurrentOtp(decrypt($user->two_factor_secret));
        $this->post('/user/confirmed-two-factor-authentication', ['code' => $code]);

        $this->assertTrue($user->fresh()->hasTwoStepSignIn());
    }

    public function test_a2_two_step_setup_asks_for_the_password_first(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/account/two-step')
            ->assertRedirect('/user/confirm-password');
    }

    public function test_a2_person_can_change_their_own_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/user/password', [
            'current_password' => 'password',
            'password' => 'new long password 2026',
            'password_confirmation' => 'new long password 2026',
        ])->assertSessionHasNoErrors();

        $this->post('/logout');
        $this->post('/login', ['username' => $user->username, 'password' => 'new long password 2026']);
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(AuditLog::where('action', 'Changed own password')->exists());
    }

    public function test_a2_short_passwords_are_rejected(): void
    {
        $this->actingAs(User::factory()->create())->put('/user/password', [
            'current_password' => 'password',
            'password' => 'short1',
            'password_confirmation' => 'short1',
        ])->assertSessionHasErrorsIn('updatePassword', 'password');
    }

    /**
     * A user who has finished setting up two-step sign-in.
     *
     * @param  string[]  $recoveryCodes
     * @return array{User, string} The user and their authenticator secret.
     */
    private function userWithTwoStepSignIn(array $recoveryCodes = ['recovery-one']): array
    {
        $secret = (new Google2FA)->generateSecretKey();

        $user = User::factory()->create();
        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'two_factor_confirmed_at' => now(),
        ])->save();

        return [$user, $secret];
    }
}
