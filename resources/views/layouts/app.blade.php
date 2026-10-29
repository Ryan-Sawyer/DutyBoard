{{-- The frame around every signed-in page: top bar, navigation, and messages. --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · DutyBoard</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="topbar">
    <a class="brand" href="{{ route('dashboard') }}">
        <span class="brand-mark" aria-hidden="true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.6" stroke-linecap="round"><rect x="3.5" y="3.5" width="17" height="17" rx="2"/><path d="M8 9h8M8 13h8M8 17h5"/></svg>
        </span>
        DutyBoard
    </a>

    <nav class="nav" aria-label="Main">
        <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>Dashboard</a>
        @can('viewAny', App\Models\User::class)
            <a href="{{ route('admin.users.index') }}" @if(request()->routeIs('admin.users.*')) aria-current="page" @endif>People</a>
        @endcan
        <a href="{{ route('account') }}" @if(request()->routeIs('account*')) aria-current="page" @endif>Account</a>
    </nav>

    <div class="who">
        {{ auth()->user()->name }}<br>
        <span>{{ auth()->user()->role->label() }} · {{ auth()->user()->certification->label() }}</span> ·
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="link-button" type="submit">Sign out</button>
        </form>
    </div>
</header>

<main>
    @include('partials.messages')
    @yield('content')
</main>
</body>
</html>
