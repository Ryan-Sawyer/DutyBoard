@extends('layouts.app')
@section('title', 'People')

@section('content')
<div class="row" style="margin-bottom:18px">
    <div>
        <h1>People</h1>
        <p class="sub" style="margin:0">Everyone with a DutyBoard account.</p>
    </div>
    <span class="spacer"></span>
    <a class="button primary" href="{{ route('admin.users.create') }}">Add a person</a>
</div>

@error('user') <div class="notice error">{{ $message }}</div> @enderror

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th class="hide-on-phone">Username</th>
                <th class="hide-on-phone">Role</th>
                <th class="hide-on-phone">Certification</th>
                <th class="hide-on-phone">Two-step</th>
                <th>Status</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $person)
                <tr @class(['inactive' => ! $person->isActive()])>
                    <td>{{ $person->name }}<div class="hint show-on-phone">{{ $person->role->label() }} · {{ $person->certification->label() }}<br>{{ $person->username }}</div></td>
                    <td class="hide-on-phone">{{ $person->username }}</td>
                    <td class="hide-on-phone">{{ $person->role->label() }}</td>
                    <td class="hide-on-phone">{{ $person->certification->label() }}</td>
                    <td class="hide-on-phone">{{ $person->hasTwoStepSignIn() ? 'On' : 'Off' }}</td>
                    <td>
                        @if ($person->isActive())
                            <span class="tag green">Active</span>
                        @else
                            <span class="tag">Deactivated</span>
                        @endif
                    </td>
                    <td class="actions">
                        <a class="button small" href="{{ route('admin.users.edit', $person) }}">Edit</a>
                        @if ($person->isNot(auth()->user()))
                            @if ($person->isActive())
                                <form method="POST" action="{{ route('admin.users.deactivate', $person) }}">
                                    @csrf
                                    <button class="button small" type="submit">Deactivate</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.reactivate', $person) }}">
                                    @csrf
                                    <button class="button small" type="submit">Reactivate</button>
                                </form>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <p class="hint">Deactivated people can't sign in. Everything they entered stays in the records.</p>
</div>
@endsection
