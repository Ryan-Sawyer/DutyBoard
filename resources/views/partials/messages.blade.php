{{-- Success messages set with ->with('status', '...'). Fortify also sets a few status codes. --}}
@if (session('status'))
    @php
        $fortifyMessages = [
            'password-updated' => 'Your password is changed.',
            'two-factor-authentication-enabled' => 'Scan the code with your authenticator app, then enter the 6-digit code it shows.',
            'two-factor-authentication-confirmed' => 'Two-step sign-in is on.',
            'two-factor-authentication-disabled' => 'Two-step sign-in is off.',
            'recovery-codes-generated' => 'New recovery codes are ready. The old ones no longer work.',
        ];
    @endphp
    <div class="notice success" role="status">{{ $fortifyMessages[session('status')] ?? session('status') }}</div>
@endif
