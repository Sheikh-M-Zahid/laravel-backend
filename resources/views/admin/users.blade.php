@extends('layouts.app')
@section('title', 'Manage Users — Admin')
@section('content')
<p><a href="{{ route('admin.dashboard') }}">← Back to dashboard</a></p>
<h2>👥 Manage Users</h2>

<div class="card">
    <table class="data-table">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach ($allUsers as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.users.role', $u->id) }}" style="display:flex; gap:6px; align-items:center;">
                        @csrf
                        <select name="role" onchange="this.form.submit()" @if($u->id === auth()->id()) disabled @endif>
                            <option value="farmer" @selected($u->role==='farmer')>Farmer</option>
                            <option value="extension_officer" @selected($u->role==='extension_officer')>Extension Officer</option>
                            <option value="supplier" @selected($u->role==='supplier')>Supplier</option>
                            <option value="admin" @selected($u->role==='admin')>Admin</option>
                        </select>
                    </form>
                </td>
                <td>
                    @if ($u->status === 'active')
                        <span class="badge-verified">Active</span>
                    @elseif ($u->status === 'pending')
                        <span class="badge-pending">Pending</span>
                        @if ($priorRemovals->has($u->email))
                            <br><a href="{{ route('admin.users.removed-history', $u->email) }}" class="badge-rejected" style="text-decoration:none;">⚠ Previously removed</a>
                        @endif
                    @else
                        <span class="badge-rejected">Restricted @if($u->restricted_until) until {{ $u->restricted_until->format('d M Y') }} @endif</span>
                    @endif
                </td>
                <td style="white-space:nowrap;">
                    @if ($u->id === auth()->id())
                        <span class="muted">— (you)</span>
                    @else
                        @if ($u->status === 'pending')
                            <form method="POST" action="{{ route('admin.users.approve', $u->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-small">✓ Approve</button>
                            </form>
                        @endif
                        <button type="button" class="btn-small" onclick="openModal('modal-restrict-{{ $u->id }}')">⏳ Restrict</button>
                        <button type="button" class="btn-small" style="background:var(--danger); color:#fff;" onclick="openModal('modal-remove-{{ $u->id }}')">✗ Remove</button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@foreach ($allUsers as $u)
    @if ($u->id !== auth()->id())
        <div class="modal-overlay" id="modal-restrict-{{ $u->id }}">
            <div class="modal-box">
                <button type="button" class="modal-close" onclick="closeModal('modal-restrict-{{ $u->id }}')">&times;</button>
                <h3>⏳ Restrict {{ $u->name }}</h3>
                <p class="muted">Temporary block. Leave blank to restrict indefinitely (you lift it manually).</p>
                <form method="POST" action="{{ route('admin.users.restrict', $u->id) }}">
                    @csrf
                    <label>Number of days (optional)</label>
                    <input type="number" name="days" min="1" max="365" placeholder="e.g. 7">
                    <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Restrict</button>
                </form>
            </div>
        </div>

        <div class="modal-overlay" id="modal-remove-{{ $u->id }}">
            <div class="modal-box">
                <button type="button" class="modal-close" onclick="closeModal('modal-remove-{{ $u->id }}')">&times;</button>
                <h3>✗ Remove {{ $u->name }}</h3>
                <p class="muted">They'll be emailed. They can register again later, but a new account always needs manual approval.</p>
                <form method="POST" action="{{ route('admin.users.remove', $u->id) }}">
                    @csrf
                    <label>Reason (optional, included in their email)</label>
                    <textarea name="reason" rows="2"></textarea>
                    <button type="submit" class="btn-primary btn-block" style="margin-top:14px; background:var(--danger);">Remove account</button>
                </form>
            </div>
        </div>
    @endif
@endforeach

<div class="card">
    <h3>Removed Accounts (history)</h3>
    <table class="data-table">
        <thead><tr><th>Name</th><th>Original email</th><th>Role</th><th>Removed</th><th></th></tr></thead>
        <tbody>
        @forelse ($removedUsers as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->removed_original_email }}</td>
                <td>{{ $u->role }}</td>
                <td>{{ $u->removed_at?->format('d M Y') }}</td>
                <td><a href="{{ route('admin.users.removed-history', $u->removed_original_email) }}">View history →</a></td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">No accounts have been removed.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
