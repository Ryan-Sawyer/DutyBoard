@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<h1>Hello, {{ str($user->name)->before(' ') }}</h1>
<p class="sub">Signed in as {{ $user->role->label() }}.</p>

@unless ($user->hasTwoStepSignIn())
    <div class="notice warning">
        Two-step sign-in isn't set up for your account yet.
        <a href="{{ route('account') }}">Set it up on your Account page</a>.
    </div>
@endunless

<div class="card">
    <h2>Units</h2>
    @foreach ($units as $unit)
        <div class="list-item">
            <span><strong>{{ $unit->name }}</strong> <span class="hint">· {{ $unit->station->name }} · {{ $unit->description }}</span></span>
            @if ($unit->isInService())
                <span class="tag green">In service</span>
            @else
                <span class="tag red">Out of service</span>
            @endif
        </div>
    @endforeach
</div>

<div class="card">
    <h2>Coming next</h2>
    <p class="hint">Truck checks, run mileage, and the schedule are being built next. See the roadmap in the project repository.</p>
</div>
@endsection
