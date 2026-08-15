@extends('layouts.app')
@section('title', 'Admins Directory')
@section('content')
<p><a href="{{ route('admin.dashboard') }}">← Back to admin dashboard</a></p>
<h2>🛡️ Admins &amp; Super Admins</h2>

<div class="card">
    <table class="data-table">
        <thead><tr><th>Name</th><th>Email</th><th>Primary role</th><th>Level</th><th></th></tr></thead>
        <tbody>
        @foreach ($admins as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ str_replace('_', ' ', $u->role) }}</td>
                <td>
                    @if ($u->is_super_admin)
                        <span class="badge-verified">Super Admin{{ $u->isFoundingSuperAdmin() ? ' (Founder)' : '' }}</span>
                    @else
                        <span class="badge-pending">Admin</span>
                    @endif
                </td>
                <td><a href="mailto:{{ $u->email }}">✉️ Email</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
