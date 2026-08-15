@extends('layouts.app')
@section('title', 'Farmer Dashboard')

@section('content')
<div class="role-banner" style="background-image: url('https://images.unsplash.com/photo-1549888722-bf34acd6a68c?w=1600&q=80&auto=format&fit=crop');">
    <div class="role-banner-content">
        <span class="role-banner-eyebrow">New this season</span>
        <h2>Pest detection is now live.</h2>
        <p>Upload a leaf photo from any plot and get an instant CNN-based diagnosis — click the Pest &amp; Disease Detection tile below.</p>
    </div>
</div>

<h2>🌾 Farmer Dashboard</h2>

<div class="card">
    <h3>My Farm Profiles</h3>
    @forelse ($farmProfiles as $profile)
        <div class="profile-row">
            <strong>Plot #{{ $profile->id }}</strong> — {{ $profile->land_size_acres }} acres,
            {{ $profile->zone->zone_name }} ({{ $profile->zone->region }})
            <div class="muted">
                pH {{ $profile->soil_ph }}, N {{ $profile->nitrogen }}, P {{ $profile->phosphorus }}, K {{ $profile->potassium }}
                @if($profile->verification_status === 'verified') <span class="badge-verified">✓ Verified</span>
                @elseif($profile->verification_status === 'rejected') <span class="badge-rejected">✗ Rejected</span>
                @else <span class="badge-pending">⏳ Pending review</span>
                @endif
            </div>
        </div>
    @empty
        <p class="muted">You haven't added a farm profile yet.</p>
    @endforelse
    <button type="button" class="btn-secondary" style="margin-top:12px;" onclick="openModal('modal-add-profile')">+ Add a new farm profile</button>
</div>

<h3 style="margin-top:30px;">Services</h3>
<div class="action-grid">
    <button type="button" class="action-tile" onclick="openModal('modal-crop')">
        <span class="action-icon">🌱</span>
        <h4>Crop Recommendation</h4>
        <p>Get the best crop for a plot from the Random Forest model.</p>
        <span class="action-cta">Open →</span>
    </button>
    <button type="button" class="action-tile" onclick="openModal('modal-fertilizer')">
        <span class="action-icon">🧪</span>
        <h4>Fertilizer Guidance</h4>
        <p>Get dosage for a crop recommendation you already received.</p>
        <span class="action-cta">Open →</span>
    </button>
    <button type="button" class="action-tile" onclick="openModal('modal-price')">
        <span class="action-icon">💰</span>
        <h4>Market Price Forecast</h4>
        <p>See where prices are headed over the next 3 months.</p>
        <span class="action-cta">Open →</span>
    </button>
    <button type="button" class="action-tile" onclick="openModal('modal-pest')">
        <span class="action-icon">🐛</span>
        <h4>Pest &amp; Disease Detection</h4>
        <p>Upload a leaf photo for an instant CNN-based check.</p>
        <span class="action-cta">Open →</span>
    </button>
    <button type="button" class="action-tile" onclick="openModal('modal-feedback')">
        <span class="action-icon">💬</span>
        <h4>Rate a Recommendation</h4>
        <p>Tell us (and your Extension Officer) if a result helped.</p>
        <span class="action-cta">Open →</span>
    </button>
    <a href="{{ route('farmer.history') }}" class="action-tile" style="text-decoration:none; display:flex;">
        <span class="action-icon">📜</span>
        <h4>Recommendation History</h4>
        <p>See everything you've received so far.</p>
        <span class="action-cta">Open →</span>
    </a>
</div>

<div style="margin-top:20px; display:flex; gap:12px; flex-wrap:wrap;">
    <a href="{{ route('farmer.marketplace') }}" class="btn-primary" style="text-decoration:none; text-align:center;">🛒 Browse the input marketplace</a>
    <a href="{{ route('farmer.orders') }}" class="btn-secondary" style="text-decoration:none; text-align:center;">📦 View my orders</a>
</div>

{{-- ============ MODALS ============ --}}

<div class="modal-overlay" id="modal-add-profile">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-add-profile')">&times;</button>
        <h3>Add a new farm profile</h3>
        <form method="POST" action="{{ route('farmer.farm-profiles.store') }}">
            @csrf
            <label>Climate zone</label>
            <select name="zone_id" required>
                @foreach ($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->zone_name }} ({{ $zone->region }})</option>
                @endforeach
            </select>
            <label>Land size (acres)</label>
            <input type="number" step="0.01" name="land_size_acres" required>
            <label>Location (optional)</label>
            <input type="text" name="location_text">
            <div class="grid-2">
                <div><label>Soil pH</label><input type="number" step="0.1" name="soil_ph" required></div>
                <div><label>Nitrogen (N)</label><input type="number" step="0.1" name="nitrogen" required></div>
                <div><label>Phosphorus (P)</label><input type="number" step="0.1" name="phosphorus" required></div>
                <div><label>Potassium (K)</label><input type="number" step="0.1" name="potassium" required></div>
            </div>
            <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Save profile</button>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modal-crop">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-crop')">&times;</button>
        <h3>🌱 Get a Crop Recommendation</h3>
        <form method="POST" action="{{ route('farmer.recommend.crop') }}">
            @csrf
            <label>Choose a plot</label>
            <select name="farm_profile_id" required>
                @foreach ($farmProfiles as $profile)
                    <option value="{{ $profile->id }}">Plot #{{ $profile->id }} — {{ $profile->zone->zone_name }}, pH {{ $profile->soil_ph }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Get recommendation</button>
        </form>
        @if (session('crop_result'))
            <div class="result-box">
                <strong>Best match: {{ session('crop_result')['top_crop'] }}</strong>
                <ul>
                    @foreach (session('crop_result')['recommendations'] as $r)
                        <li>{{ $r['crop'] }} — <span class="mono">{{ number_format($r['confidence'] * 100, 1) }}%</span></li>
                    @endforeach
                </ul>
                <p class="hint">Saved as recommendation #{{ session('crop_recommendation_id') }} — use this ID in Fertilizer Guidance or Rate a Recommendation.</p>
            </div>
        @endif
    </div>
</div>

<div class="modal-overlay" id="modal-fertilizer">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-fertilizer')">&times;</button>
        <h3>🧪 Get Fertilizer Guidance</h3>
        <form method="POST" action="{{ route('farmer.recommend.fertilizer') }}">
            @csrf
            <label>Recommendation ID (from Crop Recommendation)</label>
            <input type="number" name="recommendation_id" placeholder="e.g. {{ session('crop_recommendation_id') ?? 1 }}" required>
            <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Get dosage</button>
        </form>
        @if (session('fertilizer_result'))
            <div class="result-box">
                <strong>{{ session('fertilizer_result')['crop'] }} (kg/acre):</strong>
                <ul>
                    @foreach (session('fertilizer_result')['recommended_dosage_kg_per_acre'] as $name => $amount)
                        <li>{{ $name }}: <span class="mono">{{ $amount }} kg</span></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

<div class="modal-overlay" id="modal-price">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-price')">&times;</button>
        <h3>💰 Market Price Forecast</h3>
        <form method="POST" action="{{ route('farmer.recommend.price') }}">
            @csrf
            <label>Crop name</label>
            <input type="text" name="crop" placeholder="e.g. rice" required>
            <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Get forecast</button>
        </form>
        @if (session('price_result'))
            <div class="result-box">
                @if (isset(session('price_result')['error']))
                    <p class="muted">{{ session('price_result')['error'] }} — try one of: rice, maize, cotton, jute, banana, mango, coffee, coconut.</p>
                @else
                    <strong>Current: <span class="mono">৳{{ session('price_result')['last_known_price_bdt_per_kg'] }}/kg</span></strong>
                    <p>Next 3 months: <span class="mono">৳{{ implode(', ৳', session('price_result')['forecast_bdt_per_kg']) }}</span></p>
                @endif
            </div>
        @endif
    </div>
</div>

<div class="modal-overlay" id="modal-pest">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-pest')">&times;</button>
        <h3>🐛 Pest &amp; Disease Detection</h3>
        <form method="POST" action="{{ route('farmer.recommend.pest') }}" enctype="multipart/form-data">
            @csrf
            <label>Choose a plot</label>
            <select name="farm_profile_id" required>
                @foreach ($farmProfiles as $profile)
                    <option value="{{ $profile->id }}">Plot #{{ $profile->id }}</option>
                @endforeach
            </select>
            <label>Upload a leaf photo</label>
            <input type="file" name="image" accept="image/*" required>
            <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Detect</button>
        </form>
        @if (session('pest_result'))
            <div class="result-box">
                @if (session('pest_result')['status'] === 'ok')
                    <strong>Detected: {{ session('pest_result')['predicted_class'] }}</strong>
                    <p>Confidence: <span class="mono">{{ number_format(session('pest_result')['confidence'] * 100, 1) }}%</span></p>
                @else
                    <p class="muted">{{ session('pest_result')['message'] }}</p>
                @endif
            </div>
        @endif
    </div>
</div>

<div class="modal-overlay" id="modal-feedback">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-feedback')">&times;</button>
        <h3>💬 Rate a Recommendation</h3>
        <form method="POST" action="{{ route('farmer.feedback.store') }}">
            @csrf
            <label>Recommendation ID</label>
            <input type="number" name="recommendation_id" required>
            <label>Was it helpful?</label>
            <select name="rating" required>
                <option value="helpful">Yes, it was helpful</option>
                <option value="unhelpful">No, it wasn't helpful</option>
            </select>
            <textarea name="comment" rows="2" placeholder="Comment (optional)"></textarea>
            <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Submit</button>
        </form>
    </div>
</div>

<script>
// Auto-reopen the relevant modal after a form submit + redirect, so the
// result appears right where the person was working instead of getting lost.
document.addEventListener('DOMContentLoaded', function () {
    @if (session('crop_result')) openModal('modal-crop'); @endif
    @if (session('fertilizer_result')) openModal('modal-fertilizer'); @endif
    @if (session('price_result')) openModal('modal-price'); @endif
    @if (session('pest_result')) openModal('modal-pest'); @endif
});
</script>
@endsection
