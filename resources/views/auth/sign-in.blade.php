@extends('layouts.guest')
@section('title', 'Sign in')

@section('content')
<div class="card">
    <h1>Sign in</h1>
    <p class="sub">Use the username and password your administrator gave you.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label>Username
            <input id="username" name="username" value="{{ old('username') }}" autocomplete="username" autocapitalize="none" required autofocus>
        </label>
        @error('username') <div class="field-error" style="margin:-8px 0 12px">{{ $message }}</div> @enderror

        <label>Password
            <input id="password" name="password" type="password" autocomplete="current-password" required>
        </label>

        <label class="row" style="font-weight:400">
            <input type="checkbox" name="remember" style="width:auto;margin:0"> Keep me signed in on this device
        </label>

        <button class="button primary full" type="submit">Sign in</button>
    </form>
</div>
<p class="hint">Forgot your password? Ask an administrator to set a new one.</p>
@endsection
