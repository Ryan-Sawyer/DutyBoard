<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * A person's own account: change password and set up two-step sign-in.
 *
 * The forms post to routes provided by Laravel Fortify
 * (see `php artisan route:list --path=user`).
 */
class AccountController extends Controller
{
    public function show(Request $request): View
    {
        return view('account.show', ['user' => $request->user()]);
    }

    /**
     * Two-step sign-in setup. This page sits behind a password check
     * (the password.confirm middleware), so the forms on it can turn
     * two-step sign-in on or off without asking again.
     */
    public function twoStep(Request $request): View
    {
        return view('account.two-step', ['user' => $request->user()]);
    }
}
