@extends('layouts.app')
@section('title', 'What can the models predict? — Smart Agri-Advisory Platform')
@section('content')
<h2>📋 What our models can predict</h2>
<p class="muted">Pulled live from the ML service, so this list always matches what's actually trained — not a hand-written guess.</p>

@if (!empty($capabilities['unavailable']))
    <div class="alert alert-error" style="margin-bottom:20px;">
        The ML service isn't reachable right now, so this is the last-known list rather than a live one. Try again shortly.
    </div>
@endif

<div class="card">
    <h3>🌱 Crop Recommendation</h3>
    <p class="muted">Random Forest classifier — ranks the best crop for a plot from soil pH, N-P-K, rainfall, temperature and humidity.</p>
    <div class="tag-list">
        @forelse ($capabilities['crop_recommendation'] ?? [] as $crop)
            <span class="badge-pending" style="text-transform:capitalize;">{{ $crop }}</span>
        @empty
            <span class="muted">No trained crops found.</span>
        @endforelse
    </div>
</div>

<div class="card">
    <h3>💰 Market Price Forecast</h3>
    <p class="muted">Sequence model — projects mandi price 3 months ahead. Only these crops have a trained price history right now:</p>
    <div class="tag-list">
        @forelse ($capabilities['price_forecast'] ?? [] as $crop)
            <span class="badge-verified" style="text-transform:capitalize;">{{ $crop }}</span>
        @empty
            <span class="muted">No trained crops found.</span>
        @endforelse
    </div>
</div>

<div class="card">
    <h3>🐛 Pest &amp; Disease Detection</h3>
    @php $pest = $capabilities['pest_detection'] ?? ['trained' => false, 'classes' => []]; @endphp
    <p class="muted">
        @if (($pest['trained'] ?? false))
            CNN image classifier — currently trained to recognise:
        @else
            Not yet trained on real labeled images — this is the candidate class list the CNN will use once training data is added:
        @endif
    </p>
    <div class="tag-list">
        @forelse ($pest['classes'] ?? [] as $class)
            <span class="badge-rejected" style="text-transform:none;">{{ str_replace('_', ' ', $class) }}</span>
        @empty
            <span class="muted">No classes found.</span>
        @endforelse
    </div>
</div>

<p style="margin-top:24px;"><a href="{{ route('hub') }}">← Back to roles</a></p>
@endsection
