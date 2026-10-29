<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Users\CreateUser;
use App\Actions\Users\DeactivateUser;
use App\Actions\Users\ReactivateUser;
use App\Actions\Users\ResetTwoStepSignIn;
use App\Actions\Users\UpdateUser;
use App\Enums\Certification;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Admin → People: create accounts, change roles, reset passwords,
 * and deactivate people who've left.
 *
 * Only administrators can reach these screens (App\Policies\UserPolicy).
 * The rules live in App\Actions\Users; this class connects them to the screens.
 */
class UserController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        return view('admin.users.index', [
            'users' => User::orderByRaw('deactivated_at is not null')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('admin.users.form', ['account' => new User, ...$this->options()]);
    }

    public function store(UserRequest $request, CreateUser $createUser): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $user = $createUser->handle($request->validated());

        return redirect()->route('admin.users.index')
            ->with('status', "Created an account for {$user->name}. Give them their username and starting password.");
    }

    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        return view('admin.users.form', ['account' => $user, ...$this->options()]);
    }

    public function update(UserRequest $request, User $user, UpdateUser $updateUser): RedirectResponse
    {
        Gate::authorize('update', $user);

        $updateUser->handle($user, $request->validated(), $request->user());

        return redirect()->route('admin.users.index')->with('status', "Saved changes to {$user->name}.");
    }

    public function deactivate(Request $request, User $user, DeactivateUser $deactivateUser): RedirectResponse
    {
        Gate::authorize('update', $user);

        $deactivateUser->handle($user, $request->user());

        return back()->with('status', "{$user->name} is deactivated and can't sign in.");
    }

    public function reactivate(User $user, ReactivateUser $reactivateUser): RedirectResponse
    {
        Gate::authorize('update', $user);

        $reactivateUser->handle($user);

        return back()->with('status', "{$user->name} can sign in again.");
    }

    public function resetTwoStep(User $user, ResetTwoStepSignIn $resetTwoStep): RedirectResponse
    {
        Gate::authorize('update', $user);

        $resetTwoStep->handle($user);

        return back()->with('status', "Two-step sign-in is off for {$user->name}. They can set it up again from their Account page.");
    }

    /** @return array{roles: Role[], certifications: Certification[]} */
    private function options(): array
    {
        return ['roles' => Role::cases(), 'certifications' => Certification::cases()];
    }
}
