@extends('layouts.app')
@section('title', 'Account')

@section('content')
<h1>Your account</h1>
<p class="sub">{{ $user->name }} · username <strong>{{ $user->username }}</strong></p>

<div class="card">
    <div class="row">
        <div>
            <h2 style="margin:0">Two-step sign-in</h2>
            <p class="hint" style="margin:4px 0 0">After your password, you'll also enter a code from an authenticator app on your phone, such as Google Authenticator or Microsoft Authenticator.</p>
        </div>
        <span class="spacer"></span>
        @if ($user->hasTwoStepSignIn())
            <span class="tag green">On</span>
        @else
            <span class="tag amber">Off</span>
        @endif
    </div>
    <p style="margin:14px 0 0">
        <a class="button dark" href="{{ route('account.two-step') }}">
            {{ $user->hasTwoStepSignIn() ? 'Manage two-step sign-in' : 'Set up two-step sign-in' }}
        </a>
    </p>
</div>

<div class="card">
    <h2>Change your password</h2>
    <form method="POST" action="{{ route('user-password.update') }}">
        @csrf
        @method('PUT')
        <label>Current password
            <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
        </label>
        @error('current_password', 'updatePassword') <div class="field-error" style="margin:-8px 0 12px">{{ $message }}</div> @enderror

        <div class="two">
            <label>New password
                <input id="password" name="password" type="password" autocomplete="new-password" required>
            </label>
            <label>New password again
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            </label>
        </div>
        @error('password', 'updatePassword') <div class="field-error" style="margin:-8px 0 12px">{{ $message }}</div> @enderror
        <p class="hint">At least 12 characters, with letters and numbers. A short phrase is easier to remember than random characters.</p>

        <button class="button dark" type="submit">Change password</button>
    </form>
</div>
@endsection
