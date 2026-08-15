@extends('layouts.app')
@section('title', 'Extension Officer Dashboard')
@section('content')
<div class="role-banner tone-officer" style="background-image: url('https://images.unsplash.com/photo-1499260126922-fbb24624a4e8?w=1600&q=80&auto=format&fit=crop');">
    <div class="role-banner-content">
        <span class="role-banner-eyebrow">Coverage update</span>
        <h2>Farmers are waiting on your review.</h2>
        <p>Verified soil data drives every recommendation downstream — clearing the queue below directly improves accuracy.</p>
    </div>
</div>

<h2>🧑‍🌾 Extension Officer Dashboard</h2>

<div class="card">
    <h3>Farm Profiles Awaiting Verification</h3>
    @forelse ($pendingProfiles as $profile)
        <div class="profile-row" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
            <div>
                <strong>{{ $profile->farmer->name }}</strong> — {{ $profile->zone->zone_name }}
                <div class="muted">pH {{ $profile->soil_ph }}, N {{ $profile->nitrogen }} P {{ $profile->phosphorus }} K {{ $profile->potassium }}</div>
            </div>
            <button type="button" class="btn-small" onclick="openModal('modal-verify-{{ $profile->id }}')">Review</button>
        </div>
        <div class="modal-overlay" id="modal-verify-{{ $profile->id }}">
            <div class="modal-box">
                <button type="button" class="modal-close" onclick="closeModal('modal-verify-{{ $profile->id }}')">&times;</button>
                <h3>Review Plot #{{ $profile->id }} — {{ $profile->farmer->name }}</h3>
                <p class="muted">{{ $profile->zone->zone_name }} · pH {{ $profile->soil_ph }} · N {{ $profile->nitrogen }} · P {{ $profile->phosphorus }} · K {{ $profile->potassium }}</p>
                <form method="POST" action="{{ route('officer.farm-profiles.verify', $profile->id) }}">
                    @csrf
                    <label>Decision</label>
                    <select name="status" required>
                        <option value="verified">✓ Verify</option>
                        <option value="rejected">✗ Reject</option>
                    </select>
                    <label>Notes (optional)</label>
                    <textarea name="notes" rows="2"></textarea>
                    <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Submit decision</button>
                </form>
            </div>
        </div>
    @empty
        <p class="muted">Nothing waiting on review right now.</p>
    @endforelse
</div>

<div class="card">
    <h3>Recent Farmer Feedback</h3>
    @forelse ($recentFeedback as $fb)
        <div class="profile-row">
            <strong>{{ $fb->farmer->name }}</strong> —
            {{ $fb->rating === 'helpful' ? '👍 Helpful' : '👎 Not helpful' }}
            ({{ $fb->recommendation->recommendedCrop->crop_name ?? '—' }})
            <p class="muted">{{ $fb->comment }}</p>
        </div>
    @empty
        <p class="muted">No feedback yet.</p>
    @endforelse
</div>

<h3 style="margin-top:30px;">Actions</h3>
<div class="action-grid">
    <button type="button" class="action-tile" onclick="openModal('modal-advisory')">
        <span class="action-icon">💬</span>
        <h4>Send Advisory Message</h4>
        <p>Message a specific farmer with guidance.</p>
        <span class="action-cta">Open →</span>
    </button>
    <button type="button" class="action-tile" onclick="openModal('modal-alert')">
        <span class="action-icon">🔔</span>
        <h4>Broadcast an Alert</h4>
        <p>Send a pest or weather alert to an entire zone.</p>
        <span class="action-cta">Open →</span>
    </button>
    <button type="button" class="action-tile" onclick="openModal('modal-training')">
        <span class="action-icon">📅</span>
        <h4>Schedule Training</h4>
        <p>Set up a farmer training session in a zone.</p>
        <span class="action-cta">Open →</span>
    </button>
    <a href="{{ route('officer.trends') }}" class="action-tile" style="text-decoration:none; display:flex;">
        <span class="action-icon">📊</span>
        <h4>Regional Crop Trends</h4>
        <p>See which crops are being recommended most.</p>
        <span class="action-cta">Open →</span>
    </a>
</div>

<div class="modal-overlay" id="modal-advisory">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-advisory')">&times;</button>
        <h3>💬 Send a farmer an advisory message</h3>
        <form method="POST" action="{{ route('officer.advisory.send') }}">
            @csrf
            <label>Farmer</label>
            <select name="farmer_id" required>
                <option value="" disabled selected>Choose a farmer...</option>
                @foreach ($farmers as $farmer)
                    <option value="{{ $farmer->id }}">{{ $farmer->name }} ({{ $farmer->email }})</option>
                @endforeach
            </select>
            <label>Message</label>
            <textarea name="message" rows="3" placeholder="Write your advice..." required></textarea>
            <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Send</button>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modal-alert">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-alert')">&times;</button>
        <h3>🔔 Broadcast an Alert</h3>
        <form method="POST" action="{{ route('officer.alerts.send') }}">
            @csrf
            <label>Zone</label>
            <select name="zone_id" required>
                <option value="" disabled selected>Choose a zone...</option>
                @foreach ($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->zone_name }} ({{ $zone->region }})</option>
                @endforeach
            </select>
            <label>Type</label>
            <select name="alert_type">
                <option value="pest">Pest</option>
                <option value="weather">Weather</option>
            </select>
            <label>Message</label>
            <textarea name="message" rows="3" placeholder="Write the alert..." required></textarea>
            <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Send</button>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modal-training">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-training')">&times;</button>
        <h3>📅 Schedule a Training Session</h3>
        <form method="POST" action="{{ route('officer.training.store') }}">
            @csrf
            <label>Zone</label>
            <select name="zone_id" required>
                <option value="" disabled selected>Choose a zone...</option>
                @foreach ($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->zone_name }} ({{ $zone->region }})</option>
                @endforeach
            </select>
            <label>Title</label>
            <input type="text" name="title" required>
            <label>Date &amp; time</label>
            <input type="datetime-local" name="session_date" required>
            <label>Location</label>
            <input type="text" name="location">
            <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Schedule</button>
        </form>
    </div>
</div>
@endsection
