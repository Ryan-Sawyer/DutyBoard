{{-- The frame for pages you see before signing in. --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · DutyBoard</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<main class="narrow">
    <div class="row" style="margin-bottom:20px">
        <span class="brand-mark" aria-hidden="true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.6" stroke-linecap="round"><rect x="3.5" y="3.5" width="17" height="17" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/></svg>
        </span>
        <strong style="font-size:20px">DutyBoard</strong>
    </div>
    @include('partials.messages')
    @yield('content')
</main>
</body>
</html>
