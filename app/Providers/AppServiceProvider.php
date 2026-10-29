<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Password rules for every account. Long passwords matter more than
        // symbols, and these accounts can see staff contact details.
        Password::defaults(fn () => Password::min(12)->letters()->numbers());
    }
}
