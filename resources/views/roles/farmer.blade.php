@extends('layouts.app')
@section('title', 'For Farmers — Smart Agri-Advisory Platform')
@section('full_width', true)

@section('content')
<section class="hero" style="min-height:380px; background-image: url('https://images.unsplash.com/photo-1549888722-bf34acd6a68c?w=1600&q=80&auto=format&fit=crop');">
    <div class="hero-content">
        <span class="hero-eyebrow">For Farmers</span>
        <h1 style="font-size:2.4rem;">Everything you need to plan a season.</h1>
        <p class="lead">Log your soil readings once, and get crop, fertilizer, price, and pest guidance whenever you need it — reviewed by a real Extension Officer, not just an algorithm.</p>
        <div class="hero-ctas">
            <a href="{{ route('register', ['role' => 'farmer']) }}" class="btn-cta">Create a farmer account</a>
            <a href="{{ route('login') }}" class="btn-cta-outline">Already have an account? Log in</a>
        </div>
    </div>
</section>

<section class="section container-wide">
    <div class="section-eyebrow">What you get</div>
    <h2 class="section-title">Four tools, one dashboard.</h2>
    <div class="feature-grid">
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-farm-profile')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1651592280676-a01949b60cd7?w=700&q=80&auto=format&fit=crop" alt="Farm profile">
    <div class="feature-card-body">
        
        <h3>Farm Profile</h3>
        <p>Record land size, climate zone, and soil pH/N-P-K in one form — reused by every recommendation.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-crop-recommendation')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1549888722-bf34acd6a68c?w=700&q=80&auto=format&fit=crop" alt="Crop recommendation">
    <div class="feature-card-body">
        
        <h3>Crop Recommendation</h3>
        <p>A Random Forest model ranks the best crop for your soil and zone, with a confidence score.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-price-forecast')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1744744041837-47d851bc9722?w=700&q=80&auto=format&fit=crop" alt="Price forecast">
    <div class="feature-card-body">
        
        <h3>Price Forecast</h3>
        <p>See where mandi prices are headed over the next 3 months before you decide when to sell.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-pest-detection')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1752381400523-816117291175?w=700&q=80&auto=format&fit=crop" alt="Pest detection">
    <div class="feature-card-body">
        
        <h3>Pest Detection</h3>
        <p>Upload a leaf photo and get an instant CNN-based read on likely disease.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
    </div>
</section>

<section class="cta-band">
    <h2>Ready to add your first plot?</h2>
    <p>It takes about two minutes to get your first recommendation.</p>
    <a href="{{ route('register', ['role' => 'farmer']) }}" class="btn-cta" style="background:var(--wheat-gold); color:var(--ink);">Create a farmer account</a>
</section>

<div class="modal-overlay" id="modal-farm-profile">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-farm-profile')">&times;</button>
        <img src="https://images.unsplash.com/photo-1651592280676-a01949b60cd7?w=700&q=80&auto=format&fit=crop" alt="Farm profile">
        <h3>Farm Profile</h3>
        <p>Record land size, climate zone, and soil pH/N-P-K in one form — reused by every recommendation.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-crop-recommendation">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-crop-recommendation')">&times;</button>
        <img src="https://images.unsplash.com/photo-1549888722-bf34acd6a68c?w=700&q=80&auto=format&fit=crop" alt="Crop recommendation">
        <h3>Crop Recommendation</h3>
        <p>A Random Forest model ranks the best crop for your soil and zone, with a confidence score.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-price-forecast">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-price-forecast')">&times;</button>
        <img src="https://images.unsplash.com/photo-1744744041837-47d851bc9722?w=700&q=80&auto=format&fit=crop" alt="Price forecast">
        <h3>Price Forecast</h3>
        <p>See where mandi prices are headed over the next 3 months before you decide when to sell.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-pest-detection">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-pest-detection')">&times;</button>
        <img src="https://images.unsplash.com/photo-1752381400523-816117291175?w=700&q=80&auto=format&fit=crop" alt="Pest detection">
        <h3>Pest Detection</h3>
        <p>Upload a leaf photo and get an instant CNN-based read on likely disease.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
@endsection
