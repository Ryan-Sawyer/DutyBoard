@extends('layouts.app')
@section('title', 'Two-step sign-in')

{{--
    Three states:
    1. Off: offer to turn it on.
    2. Turned on but not confirmed: show the QR code and ask for a code.
    3. On: show recovery codes, and let the person turn it off.
    The forms post to Laravel Fortify's two-factor routes.
--}}

@section('content')
<p><a href="{{ route('account') }}">← Account</a></p>
<h1>Two-step sign-in</h1>
<p class="sub">Protects your account if someone learns your password.</p>

@if (! $user->two_factor_secret)
    <div class="card">
        <h2>Step 1: Get an authenticator app</h2>
        <p>Install an authenticator app on your phone if you don't have one. Google Authenticator and Microsoft Authenticator are both free.</p>
        <form method="POST" action="{{ route('two-factor.enable') }}">
            @csrf
            <button class="button primary" type="submit">I have the app, continue</button>
        </form>
    </div>

@elseif (! $user->hasTwoStepSignIn())
    <div class="card">
        <h2>Step 2: Scan this code</h2>
        <p>In your authenticator app, add an account and scan this code.</p>
        <div class="qr">{!! $user->twoFactorQrCodeSvg() !!}</div>
        <p class="hint">Can't scan it? Enter this key in the app instead:<br>
            <strong style="font-family:ui-monospace,monospace">{{ decrypt($user->two_factor_secret) }}</strong></p>

        <h2 style="margin-top:20px">Step 3: Enter the code from the app</h2>
        <form method="POST" action="{{ route('two-factor.confirm') }}">
            @csrf
            <label>6-digit code
                <input id="code" name="code" class="code-input" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required style="max-width:200px">
            </label>
            @error('code', 'confirmTwoFactorAuthentication') <div class="field-error" style="margin:-8px 0 12px">{{ $message }}</div> @enderror
            <div class="row">
                <button class="button primary" type="submit">Turn on two-step sign-in</button>
                <span class="spacer"></span>
            </div>
        </form>
        <form method="POST" action="{{ route('two-factor.disable') }}" style="margin-top:12px">
            @csrf
            @method('DELETE')
            <button class="link-button" style="color:var(--muted)" type="submit">Cancel setup</button>
        </form>
    </div>

@else
    <div class="card">
        <div class="row"><h2 style="margin:0">Two-step sign-in is on</h2><span class="spacer"></span><span class="tag green">On</span></div>
        <p>If you lose your phone, sign in with one of these recovery codes instead of a code from the app. Each one works once. Write them down and keep them somewhere safe, not on your phone.</p>
        <div class="recovery-codes">
            @foreach ($user->recoveryCodes() as $code)
                <div>{{ $code }}</div>
            @endforeach
        </div>
        <div class="row" style="margin-top:14px">
            <form method="POST" action="{{ route('two-factor.regenerate-recovery-codes') }}">
                @csrf
                <button class="button small" type="submit">Make new recovery codes</button>
            </form>
            <span class="spacer"></span>
            <form method="POST" action="{{ route('two-factor.disable') }}">
                @csrf
                @method('DELETE')
                <button class="button small" type="submit">Turn off two-step sign-in</button>
            </form>
        </div>
    </div>
@endif
@endsection
