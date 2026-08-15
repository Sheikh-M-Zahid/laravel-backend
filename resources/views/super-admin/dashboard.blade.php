@extends('layouts.app')
@section('title', 'Super Admin — Smart Agri-Advisory Platform')
@section('content')
<p><a href="{{ route('admin.dashboard') }}">← Back to admin dashboard</a></p>
<h2>👑 Super Admin</h2>
<p class="muted">Founding Super Admins: {{ $founders->pluck('name')->join(', ') ?: 'none logged in yet' }} — configured in <code>config/super_admins.php</code>.</p>

<div class="card">
    <h3>Admin applications awaiting your review</h3>
    @forelse ($pendingApplications as $u)
        <div class="profile-row" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
            <span>{{ $u->name }} ({{ $u->role }}) — {{ $u->email }}</span>
            <div style="display:flex; gap:8px;">
                <form method="POST" action="{{ route('super-admin.applications.approve', $u->id) }}">
                    @csrf
                    <button type="submit" class="btn-small">✓ Approve</button>
                </form>
                <form method="POST" action="{{ route('super-admin.applications.reject', $u->id) }}">
                    @csrf
                    <button type="submit" class="btn-small" style="background:var(--danger); color:#fff;">✗ Reject</button>
                </form>
            </div>
        </div>
    @empty
        <p class="muted">No pending admin applications.</p>
    @endforelse
</div>

<div class="card">
    <h3>Suggest a new Super Admin</h3>
    <p class="muted">Any admin can suggest someone. It only takes effect once all 3 founding Super Admins approve.</p>
    <form method="POST" action="{{ route('super-admin.nominations.create') }}" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
        @csrf
        <div style="flex:1; min-width:220px;">
            <label>Candidate (current admins)</label>
            <select name="nominee_user_id" required>
                @forelse ($candidates as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->email }})</option>
                @empty
                    <option disabled>No eligible admins yet</option>
                @endforelse
            </select>
        </div>
        <button type="submit" class="btn-primary">Suggest</button>
    </form>
</div>

<div class="card">
    <h3>Super Admin nominations</h3>
    <table class="data-table">
        <thead><tr><th>Candidate</th><th>Suggested by</th><th>Founder approvals</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse ($nominations as $nom)
            @php
                $founderApprovals = $nom->approvals->filter(fn($a) => $a->decision === 'approve' && $a->approver?->isFoundingSuperAdmin());
                $iAlreadyVoted = $nom->approvals->contains('approver_user_id', auth()->id());
            @endphp
            <tr>
                <td>{{ $nom->nominee->name }}</td>
                <td>{{ $nom->createdBy->name }}</td>
                <td class="mono">{{ $founderApprovals->count() }}/3</td>
                <td>
                    @if ($nom->status === 'approved')
                        <span class="badge-verified">Approved</span>
                    @elseif ($nom->status === 'rejected')
                        <span class="badge-rejected">Rejected</span>
                    @else
                        <span class="badge-pending">Pending</span>
                    @endif
                </td>
                <td>
                    @if ($nom->status === 'pending' && auth()->user()->isFoundingSuperAdmin() && ! $iAlreadyVoted)
                        <form method="POST" action="{{ route('super-admin.nominations.approve', $nom->id) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-small">✓ Approve</button>
                        </form>
                        <form method="POST" action="{{ route('super-admin.nominations.reject', $nom->id) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-small" style="background:var(--danger); color:#fff;">✗ Reject</button>
                        </form>
                    @elseif ($nom->status === 'pending' && $iAlreadyVoted)
                        <span class="muted">You've voted</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">No nominations yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="card">
    <h3>All admins &amp; super admins</h3>
    <p><a href="{{ route('admins.directory') }}">Open full directory →</a></p>
</div>
@endsection
