@extends('layouts.app')
@section('title', 'Smart Agri-Advisory Platform — Know what to plant before you plant it')
@section('full_width', true)

@section('content')
<section class="hero" style="background-image: url('https://images.unsplash.com/photo-1499260126922-fbb24624a4e8?w=1600&q=80&auto=format&fit=crop');">
    <div class="hero-content">
        <span class="hero-eyebrow">Soil · Weather · Market Data, Unified</span>
        <h1>Know what to<br>plant before<br>you plant it.</h1>
        <p class="lead">A role-based advisory platform for Bangladeshi farmers — combining soil readings, live weather, and historical yield data into one recommendation, verified by real Extension Officers.</p>
        <div class="hero-ctas">
            @auth
                <a href="{{ route(auth()->user()->role === 'extension_officer' ? 'officer.dashboard' : (auth()->user()->role === 'admin' ? 'admin.dashboard' : auth()->user()->role . '.dashboard')) }}" class="btn-cta">Go to my dashboard</a>
            @else
                <a href="{{ route('register') }}" class="btn-cta">Create free account</a>
                <a href="{{ route('login') }}" class="btn-cta-outline">Log in</a>
            @endauth
        </div>
    </div>
</section>

<section class="section container-wide">
    <div class="section-eyebrow">What the platform does</div>
    <h2 class="section-title">Four models, one field visit.</h2>
    <p class="section-lead">Every recommendation below is backed by a trained model in <code>ml-service/</code> and reviewed by an Extension Officer before it reaches a farmer's plan.</p>

    <div class="feature-grid">
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-crop-recommendation')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1549888722-bf34acd6a68c?w=700&q=80&auto=format&fit=crop" alt="Rice field ready for planting">
    <div class="feature-card-body">
        <span class="season-tag tag-kharif1">Kharif-1 season</span>
        <h3>Crop Recommendation</h3>
        <p>A Random Forest classifier reads soil pH, N-P-K, and weather to rank the best crop for your plot.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-fertilizer-guidance')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1651592280676-a01949b60cd7?w=700&q=80&auto=format&fit=crop" alt="Farmer holding soil">
    <div class="feature-card-body">
        <span class="season-tag tag-rabi">Rabi season</span>
        <h3>Fertilizer Guidance</h3>
        <p>Dosage blends agronomic rules with what similar farms nearby actually applied.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-market-price-forecast')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1744744041837-47d851bc9722?w=700&q=80&auto=format&fit=crop" alt="Grain at a local market">
    <div class="feature-card-body">
        <span class="season-tag tag-kharif2">Kharif-2 season</span>
        <h3>Market Price Forecast</h3>
        <p>A sequence model projects mandi prices three months ahead so you know when to sell.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-pest-amp-disease-detection')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1752381400523-816117291175?w=700&q=80&auto=format&fit=crop" alt="Close-up of a crop leaf">
    <div class="feature-card-body">
        <span class="season-tag tag-rabi">All seasons</span>
        <h3>Pest &amp; Disease Detection</h3>
        <p>Upload a leaf photo — a CNN flags likely disease so you can act before it spreads.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
    </div>
</section>

<section class="role-strip">
    <div class="section container-wide">
        <div class="section-eyebrow">Built for four roles</div>
        <h2 class="section-title">Everyone on the platform has a job to do.</h2>
        <p class="section-lead">Role-based access means a farmer never sees admin tools, and a supplier never sees another supplier's orders.</p>

        @php
            $roleMeta = [
                'farmer' => ['icon' => '🌾', 'label' => 'Farmer', 'desc' => 'Logs soil data, gets crop/fertilizer/price recommendations, orders inputs.', 'class' => ''],
                'extension_officer' => ['icon' => '🧑‍🌾', 'label' => 'Extension Officer', 'desc' => 'Verifies farm data, reviews ML output, sends advisories and alerts.', 'class' => 'officer'],
                'supplier' => ['icon' => '🏪', 'label' => 'Supplier', 'desc' => 'Lists seed and fertilizer, tracks demand forecasts, fulfils orders.', 'class' => 'supplier'],
                'admin' => ['icon' => '🛡️', 'label' => 'Admin', 'desc' => 'Approves accounts, manages reference data, retrains models.', 'class' => 'admin'],
            ];
            $myRole = auth()->check() ? auth()->user()->role : null;
            $hasAdminAccess = auth()->check() && (auth()->user()->is_admin || auth()->user()->is_super_admin);
        @endphp
        <div class="role-cards">
            @foreach ($roleMeta as $roleKey => $meta)
                <div class="role-card {{ $meta['class'] }}">
                    <span class="role-icon">{{ $meta['icon'] }}</span>
                    <h4>{{ $meta['label'] }}</h4>
                    <p>{{ $meta['desc'] }}</p>

                    @if ($roleKey === 'admin' && $hasAdminAccess)
                        {{-- Granted admin/super-admin access (possibly alongside a different primary role) --}}
                        <a href="{{ route('admin.dashboard') }}" class="btn-primary" style="text-decoration:none; display:inline-block; margin-top:8px;">Enter →</a>
                        @if (auth()->user()->is_super_admin)
                            <br><span class="badge-verified" style="margin-top:6px; display:inline-block;">Super Admin</span>
                        @endif
                    @elseif ($myRole === $roleKey)
                        {{-- Already registered as this role and logged in --}}
                        <a href="{{ route($roleKey === 'extension_officer' ? 'officer.dashboard' : ($roleKey === 'admin' ? 'admin.dashboard' : $roleKey . '.dashboard')) }}" class="btn-primary" style="text-decoration:none; display:inline-block; margin-top:8px;">Enter →</a>
                    @elseif ($myRole && $roleKey !== 'admin')
                        {{-- Logged in as a different role: offer to sign out and register a second account --}}
                        <form method="POST" action="{{ route('logout') }}" style="margin-top:8px;">
                            @csrf
                            <input type="hidden" name="join_as" value="{{ $roleKey }}">
                            <button type="submit" class="btn-secondary btn-block">Join as {{ $meta['label'] }}</button>
                        </form>
                    @elseif ($myRole && $roleKey === 'admin')
                        {{-- Admin accounts aren't self-registered -- apply after signing up as another role --}}
                        <form method="POST" action="{{ route('apply-admin') }}" style="margin-top:8px;">
                            @csrf
                            @if (auth()->user()->admin_application_status === 'pending')
                                <button type="submit" class="btn-secondary btn-block" disabled>Application pending…</button>
                            @else
                                <button type="submit" class="btn-secondary btn-block">Apply to become admin</button>
                            @endif
                        </form>
                    @elseif ($roleKey === 'admin')
                        <a href="{{ route('roles.show', $roleKey) }}">See admin tools →</a>
                    @else
                        <a href="{{ route('register', ['role' => $roleKey]) }}" class="btn-secondary" style="text-decoration:none; display:inline-block; margin-top:8px;">Join as {{ $meta['label'] }}</a>
                        <br><a href="{{ route('roles.show', $roleKey) }}" style="font-size:0.85rem;">See what {{ strtolower($meta['label']) }}s get →</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section container-wide">
    <div class="card" style="text-align:center; padding:36px;">
        <h3 style="margin-top:0;">Curious what the models can actually predict?</h3>
        <p class="muted">See the exact crop list, price-forecast coverage, and pest/disease classes each trained model supports right now.</p>
        <a href="{{ route('predictions') }}" class="btn-primary" style="text-decoration:none; display:inline-block; margin-top:6px;">📋 View model prediction list</a>
    </div>
</section>

@unless (auth()->check())
<section class="cta-band">
    <h2>Ready to plan your next season?</h2>
    <p>It takes about two minutes to add a farm profile and get your first recommendation.</p>
    <a href="{{ route('register') }}" class="btn-cta" style="background:var(--wheat-gold); color:var(--ink);">Create free account</a>
</section>
@endunless

<div class="modal-overlay" id="modal-crop-recommendation">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-crop-recommendation')">&times;</button>
        <img src="https://images.unsplash.com/photo-1549888722-bf34acd6a68c?w=700&q=80&auto=format&fit=crop" alt="Rice field ready for planting">
        <h3>Crop Recommendation</h3>
        <p>A Random Forest classifier reads soil pH, N-P-K, and weather to rank the best crop for your plot.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-fertilizer-guidance">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-fertilizer-guidance')">&times;</button>
        <img src="https://images.unsplash.com/photo-1651592280676-a01949b60cd7?w=700&q=80&auto=format&fit=crop" alt="Farmer holding soil">
        <h3>Fertilizer Guidance</h3>
        <p>Dosage blends agronomic rules with what similar farms nearby actually applied.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-market-price-forecast">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-market-price-forecast')">&times;</button>
        <img src="https://images.unsplash.com/photo-1744744041837-47d851bc9722?w=700&q=80&auto=format&fit=crop" alt="Grain at a local market">
        <h3>Market Price Forecast</h3>
        <p>A sequence model projects mandi prices three months ahead so you know when to sell.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-pest-amp-disease-detection">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-pest-amp-disease-detection')">&times;</button>
        <img src="https://images.unsplash.com/photo-1752381400523-816117291175?w=700&q=80&auto=format&fit=crop" alt="Close-up of a crop leaf">
        <h3>Pest &amp; Disease Detection</h3>
        <p>Upload a leaf photo — a CNN flags likely disease so you can act before it spreads.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
@endsection
