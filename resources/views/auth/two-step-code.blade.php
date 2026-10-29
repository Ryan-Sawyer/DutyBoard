@extends('layouts.guest')
@section('title', 'Two-step sign-in')

@section('content')
<div class="card">
    <h1>Enter your code</h1>
    <p class="sub">Open your authenticator app and enter the 6-digit code for DutyBoard.</p>

    <form method="POST" action="{{ route('two-factor.login.store') }}">
        @csrf
        <label>Code
            <input id="code" name="code" class="code-input" inputmode="numeric" autocomplete="one-time-code" maxlength="6" autofocus>
        </label>
        @error('code') <div class="field-error" style="margin:-8px 0 12px">{{ $message }}</div> @enderror
        <button class="button primary full" type="submit">Continue</button>
    </form>

    <details style="margin-top:16px">
        <summary class="hint">Lost your phone? Use a recovery code</summary>
        <form method="POST" action="{{ route('two-factor.login.store') }}" style="margin-top:12px">
            @csrf
            <label>Recovery code
                <input id="recovery_code" name="recovery_code" autocomplete="off" autocapitalize="none">
            </label>
            @error('recovery_code') <div class="field-error" style="margin:-8px 0 12px">{{ $message }}</div> @enderror
            <button class="button full" type="submit">Use recovery code</button>
        </form>
    </details>
</div>
@endsection
