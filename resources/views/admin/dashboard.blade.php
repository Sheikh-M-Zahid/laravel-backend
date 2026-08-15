@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div class="role-banner tone-admin" style="background-image: url('https://images.unsplash.com/photo-1499260126922-fbb24624a4e8?w=1600&q=80&auto=format&fit=crop');">
    <div class="role-banner-content">
        <span class="role-banner-eyebrow">Platform health</span>
        <h2>{{ $stats['pending_approvals'] }} account(s) waiting on you.</h2>
        <p>Approvals, reference data, and model retraining all live below.</p>
    </div>
</div>

<h2>🛡️ Admin Dashboard</h2>

<div class="grid-3" style="grid-template-columns: repeat(4, 1fr);">
    <div class="stat-card"><div class="stat-num">{{ $stats['total_users'] }}</div><div>Total users</div></div>
    <div class="stat-card"><div class="stat-num">{{ $stats['farmers'] }}</div><div>Farmers</div></div>
    <div class="stat-card"><div class="stat-num">{{ $stats['officers'] }}</div><div>Officers</div></div>
    <div class="stat-card"><div class="stat-num">{{ $stats['suppliers'] }}</div><div>Suppliers</div></div>
    <div class="stat-card"><div class="stat-num">{{ $stats['pending_approvals'] }}</div><div>Pending approval</div></div>
    <div class="stat-card"><div class="stat-num">{{ $stats['total_recommendations'] }}</div><div>Recommendations</div></div>
    <div class="stat-card"><div class="stat-num">{{ $stats['total_orders'] }}</div><div>Orders</div></div>
</div>

<div class="card" style="margin-top:22px;">
    <h3>Accounts Awaiting Approval</h3>
    @forelse ($pendingUsers as $u)
        <div class="profile-row" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
            <span>
                {{ $u->name }} ({{ $u->role }}) — {{ $u->email }}
                @if ($u->hadPriorRemoval)
                    <a href="{{ route('admin.users.removed-history', $u->email) }}" class="badge-rejected" style="text-decoration:none;">⚠ Previously removed — view history</a>
                @endif
            </span>
            <form method="POST" action="{{ route('admin.users.approve', $u->id) }}">
                @csrf
                <button type="submit" class="btn-small">✓ Approve</button>
            </form>
        </div>
    @empty
        <p class="muted">No accounts waiting right now.</p>
    @endforelse
    <p style="margin-top:10px;"><a href="{{ route('admin.users.index') }}">Manage all users (restrict / remove / change role) →</a></p>
</div>

<div class="card">
    <h3>Platform &amp; Model Stats <span class="hint">(admin-only)</span></h3>
    <div class="grid-3" style="grid-template-columns: repeat(4, 1fr);">
        <div class="stat-card"><div class="stat-num">{{ $crops->count() }}</div><div>Crops modeled</div></div>
        <div class="stat-card"><div class="stat-num">{{ $zones->count() }}</div><div>Climate zones</div></div>
        <div class="stat-card"><div class="stat-num">4</div><div>Connected roles</div></div>
        <div class="stat-card"><div class="stat-num">99.3%</div><div>Crop model accuracy*</div></div>
    </div>
    <p class="hint" style="margin-top:10px;">*Random Forest test accuracy on the current training set (see ml-service/train_crop_model.py). Retrain to refresh this figure.</p>
</div>

<h3 style="margin-top:30px;">Actions</h3>
<div class="action-grid">
    <button type="button" class="action-tile" onclick="openModal('modal-zone')">
        <span class="action-icon">🗺️</span>
        <h4>Add Climate Zone</h4>
        <p>Currently {{ $zones->count() }} zones: {{ $zones->pluck('zone_name')->join(', ') }}</p>
        <span class="action-cta">Open →</span>
    </button>
    <button type="button" class="action-tile" onclick="openModal('modal-crop')">
        <span class="action-icon">🌾</span>
        <h4>Add Crop</h4>
        <p>Currently {{ $crops->count() }} crops in the reference data.</p>
        <span class="action-cta">Open →</span>
    </button>
    <button type="button" class="action-tile" onclick="openModal('modal-retrain')">
        <span class="action-icon">🔄</span>
        <h4>Retrain an ML Model</h4>
        <p>Queue a retraining job for any of the four models.</p>
        <span class="action-cta">Open →</span>
    </button>
    <form method="POST" action="{{ route('admin.backup.trigger') }}" class="action-tile" style="align-items:flex-start;">
        @csrf
        <span class="action-icon">💾</span>
        <h4>Database Backup</h4>
        <p>Record a backup job.</p>
        <button type="submit" class="action-cta" style="background:none; border:none; cursor:pointer; padding:0;">Run now →</button>
    </form>
    <a href="{{ route('admin.analytics') }}" class="action-tile" style="text-decoration:none; display:flex;">
        <span class="action-icon">📊</span>
        <h4>Platform Analytics</h4>
        <p>Crop distribution and historical snapshots.</p>
        <span class="action-cta">Open →</span>
    </a>
    <a href="{{ route('admin.users.index') }}" class="action-tile" style="text-decoration:none; display:flex;">
        <span class="action-icon">👥</span>
        <h4>Manage Users</h4>
        <p>Approve, restrict, or remove any account.</p>
        <span class="action-cta">Open →</span>
    </a>
    <a href="{{ route('admin.activity') }}" class="action-tile" style="text-decoration:none; display:flex;">
        <span class="action-icon">🗒️</span>
        <h4>Activity History</h4>
        <p>Every action every admin has taken.</p>
        <span class="action-cta">Open →</span>
    </a>
    <a href="{{ route('admins.directory') }}" class="action-tile" style="text-decoration:none; display:flex;">
        <span class="action-icon">🛡️</span>
        <h4>Admins Directory</h4>
        <p>See who's an admin/super admin, email them.</p>
        <span class="action-cta">Open →</span>
    </a>
    @if (auth()->user()->is_super_admin)
        <a href="{{ route('super-admin.dashboard') }}" class="action-tile" style="text-decoration:none; display:flex;">
            <span class="action-icon">👑</span>
            <h4>Super Admin</h4>
            <p>Review admin applications &amp; Super Admin nominations.</p>
            <span class="action-cta">Open →</span>
        </a>
    @endif
</div>

<div class="modal-overlay" id="modal-zone">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-zone')">&times;</button>
        <h3>🗺️ Add a Climate Zone</h3>
        <form method="POST" action="{{ route('admin.zones.store') }}">
            @csrf
            <label>Zone name</label><input type="text" name="zone_name" required>
            <label>Region</label><input type="text" name="region">
            <label>Description</label><textarea name="description" rows="2"></textarea>
            <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Add zone</button>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modal-crop">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-crop')">&times;</button>
        <h3>🌾 Add a Crop</h3>
        <form method="POST" action="{{ route('admin.crops.store') }}">
            @csrf
            <label>Crop name</label><input type="text" name="crop_name" required>
            <label>Season</label>
            <select name="season" required>
                <option value="Kharif-1">Kharif-1</option>
                <option value="Kharif-2">Kharif-2</option>
                <option value="Rabi">Rabi</option>
            </select>
            <label>Description</label><textarea name="description" rows="2"></textarea>
            <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Add crop</button>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modal-retrain">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-retrain')">&times;</button>
        <h3>🔄 Retrain an ML Model</h3>
        <form method="POST" action="{{ route('admin.retrain.trigger') }}">
            @csrf
            <label>Model</label>
            <select name="model_name">
                <option value="crop_rf">Crop Random Forest</option>
                <option value="fertilizer_rule">Fertilizer Rule/kNN</option>
                <option value="price_lstm">Price Forecast</option>
                <option value="disease_cnn">Disease CNN</option>
            </select>
            <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Queue retraining</button>
        </form>
    </div>
</div>
@endsection
