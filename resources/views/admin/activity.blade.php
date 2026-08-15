@extends('layouts.app')
@section('title', 'Activity History — Admin')
@section('content')
<p><a href="{{ route('admin.dashboard') }}">← Back to dashboard</a></p>
<h2>🗒️ Admin Activity History</h2>
<p class="muted">Every action taken by every admin, most recent first.</p>

<div class="card">
    <table class="data-table">
        <thead><tr><th>Admin</th><th>Action</th><th>Description</th><th>Date</th></tr></thead>
        <tbody>
        @forelse ($logs as $log)
            <tr>
                <td>{{ $log->admin?->name ?? '—' }}</td>
                <td>{{ str_replace('_', ' ', $log->action) }}</td>
                <td>
                    {{ $log->description }}
                    @if ($log->subject_user_id && $log->subjectUser)
                        <br><span class="muted">Subject: {{ $log->subjectUser->name }} (#{{ $log->subject_user_id }})</span>
                    @endif
                </td>
                <td>{{ $log->created_at?->format('d M Y, h:i A') }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="muted">No admin activity logged yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $logs->links() }}
</div>
@endsection
