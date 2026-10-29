@extends('layouts.app')
@section('title', $account->exists ? "Edit {$account->name}" : 'Add a person')

{{-- One form for both adding and editing a person. --}}

@section('content')
<p><a href="{{ route('admin.users.index') }}">← People</a></p>
<h1>{{ $account->exists ? "Edit {$account->name}" : 'Add a person' }}</h1>
<p class="sub">{{ $account->exists ? 'Change their details, role, or password.' : 'They sign in with the username and starting password you set here.' }}</p>

<form method="POST" action="{{ $account->exists ? route('admin.users.update', $account) : route('admin.users.store') }}" class="card">
    @csrf
    @if ($account->exists) @method('PUT') @endif

    <div class="two">
        <label>Full name
            <input id="name" name="name" value="{{ old('name', $account->name) }}" required>
            @error('name') <div class="field-error">{{ $message }}</div> @enderror
        </label>
        <label>Username
            <input id="username" name="username" value="{{ old('username', $account->username) }}" autocapitalize="none" required>
            @error('username') <div class="field-error">{{ $message }}</div> @enderror
        </label>
        <label>Role
            <select id="role" name="role">
                @foreach ($roles as $role)
                    <option value="{{ $role->value }}" @selected(old('role', $account->role?->value ?? 'crew') === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>
            @error('role') <div class="field-error">{{ $message }}</div> @enderror
        </label>
        <label>Certification
            <select id="certification" name="certification">
                @foreach ($certifications as $certification)
                    <option value="{{ $certification->value }}" @selected(old('certification', $account->certification?->value) === $certification->value)>{{ $certification->label() }}</option>
                @endforeach
            </select>
            @error('certification') <div class="field-error">{{ $message }}</div> @enderror
        </label>
        <label>Mobile number <span class="hint">(optional for now, used for text notifications later)</span>
            <input id="mobile_number" name="mobile_number" type="tel" value="{{ old('mobile_number', $account->mobile_number) }}" autocomplete="off">
            @error('mobile_number') <div class="field-error">{{ $message }}</div> @enderror
        </label>
    </div>

    <h2 style="margin-top:8px">{{ $account->exists ? 'Set a new password' : 'Starting password' }}</h2>
    @if ($account->exists)
        <p class="hint" style="margin-top:-6px">Leave blank to keep their current password.</p>
    @endif
    <div class="two">
        <label>Password
            <input id="password" name="password" type="password" autocomplete="new-password" @required(! $account->exists)>
        </label>
        <label>Password again
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" @required(! $account->exists)>
        </label>
    </div>
    @error('password') <div class="field-error" style="margin:-8px 0 12px">{{ $message }}</div> @enderror
    <p class="hint">At least 12 characters, with letters and numbers. Give it to them in person, not by text or email.</p>

    <button class="button primary" type="submit">{{ $account->exists ? 'Save changes' : 'Add person' }}</button>
</form>

@if ($account->exists && $account->hasTwoStepSignIn())
    <div class="card">
        <h2>Two-step sign-in</h2>
        <p class="hint">{{ $account->name }} has two-step sign-in on. If they've lost their phone and their recovery codes, turn it off so they can sign in with just their password and set it up again.</p>
        <form method="POST" action="{{ route('admin.users.reset-two-step', $account) }}">
            @csrf
            <button class="button" type="submit">Turn off two-step sign-in</button>
        </form>
    </div>
@endif
@endsection
