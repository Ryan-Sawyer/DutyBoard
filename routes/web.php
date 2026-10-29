<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Sign-in, sign-out, two-step sign-in, and password changes are routed by
// Laravel Fortify. Run `php artisan route:list` to see them all.

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/account', [AccountController::class, 'show'])->name('account');
    Route::get('/account/two-step', [AccountController::class, 'twoStep'])
        ->middleware('password.confirm')
        ->name('account.two-step');

    // Admin → People. UserController checks that the person is an administrator.
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::post('users/{user}/reactivate', [UserController::class, 'reactivate'])->name('users.reactivate');
        Route::post('users/{user}/reset-two-step', [UserController::class, 'resetTwoStep'])->name('users.reset-two-step');
    });
});
