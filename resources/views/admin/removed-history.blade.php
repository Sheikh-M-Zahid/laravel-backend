@extends('layouts.app')
@section('title', 'Account History — Admin')
@section('content')
<p><a href="{{ route('admin.users.index') }}">← Back to manage users</a></p>
<h2>⚠ History for {{ $email }}</h2>
<p class="muted">Review this before approving any new registration under the same email.</p>

<div class="card">
    <h3>Previous account(s)</h3>
    @foreach ($removedAccounts as $acc)
        <div class="profile-row">
            <strong>#{{ $acc->id }} — {{ $acc->name }}</strong> ({{ $acc->role }})
            <div class="muted">Removed {{ $acc->removed_at?->format('d M Y, h:i A') }}</div>
        </div>
    @endforeach
</div>

<div class="card">
    <h3>Related admin activity</h3>
    <table class="data-table">
        <thead><tr><th>Admin</th><th>Action</th><th>Description</th><th>Date</th></tr></thead>
        <tbody>
        @forelse ($logs as $log)
            <tr>
                <td>{{ $log->admin?->name ?? '—' }}</td>
                <td>{{ str_replace('_', ' ', $log->action) }}</td>
                <td>{{ $log->description }}</td>
                <td>{{ $log->created_at?->format('d M Y, h:i A') }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="muted">No logged actions found for this account.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
