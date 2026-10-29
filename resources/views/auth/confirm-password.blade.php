@extends('layouts.guest')
@section('title', 'Confirm your password')

@section('content')
<div class="card">
    <h1>Confirm your password</h1>
    <p class="sub">For your security, enter your password again before changing sign-in settings.</p>

    <form method="POST" action="{{ route('password.confirm.store') }}">
        @csrf
        <label>Password
            <input id="password" name="password" type="password" autocomplete="current-password" required autofocus>
        </label>
        @error('password') <div class="field-error" style="margin:-8px 0 12px">{{ $message }}</div> @enderror
        <button class="button primary full" type="submit">Confirm</button>
    </form>
</div>
@endsection
