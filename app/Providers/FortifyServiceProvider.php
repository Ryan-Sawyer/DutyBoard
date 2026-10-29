<?php

namespace App\Providers;

use App\Actions\Fortify\UpdateUserPassword;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

/**
 * Sign-in is handled by Laravel Fortify. This class tells Fortify which
 * screens to show and how DutyBoard checks a username and password.
 */
class FortifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Fortify::loginView(fn () => view('auth.sign-in'));
        Fortify::twoFactorChallengeView(fn () => view('auth.two-step-code'));
        Fortify::confirmPasswordView(fn () => view('auth.confirm-password'));

        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);

        // Deactivated people can't sign in, even with the right password.
        // Fortify uses this check for both sign-in and two-step sign-in.
        Fortify::authenticateUsing(function (Request $request): ?User {
            $user = User::where('username', Str::lower($request->input('username')))->first();

            if (! $user || ! Hash::check($request->input('password'), $user->password)) {
                return null;
            }

            // The password was right, so it's safe to say why sign-in failed.
            if (! $user->isActive()) {
                AuditLog::record('Blocked sign-in by a deactivated account', $user);

                throw ValidationException::withMessages([
                    'username' => 'This account has been deactivated. Ask an administrator if you think this is a mistake.',
                ]);
            }

            return $user;
        });

        // Five tries a minute per username and address, to slow down password guessing.
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
