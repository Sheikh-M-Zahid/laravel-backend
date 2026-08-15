@extends('layouts.app')
@section('title', 'For Extension Officers — Smart Agri-Advisory Platform')
@section('full_width', true)

@section('content')
<section class="hero" style="min-height:380px; background-image: url('https://images.unsplash.com/photo-1499260126922-fbb24624a4e8?w=1600&q=80&auto=format&fit=crop');">
    <div class="hero-content">
        <span class="hero-eyebrow">For Extension Officers</span>
        <h1 style="font-size:2.4rem;">Be the human check on every recommendation.</h1>
        <p class="lead">Verify farmer-submitted soil data, override an ML crop suggestion when local conditions call for it, and push advisories and alerts straight to farmers in your zone.</p>
        <div class="hero-ctas">
            <a href="{{ route('register', ['role' => 'extension_officer']) }}" class="btn-cta">Apply as an officer</a>
            <a href="{{ route('login') }}" class="btn-cta-outline">Already have an account? Log in</a>
        </div>
    </div>
</section>

<section class="section container-wide">
    <div class="section-eyebrow">What you get</div>
    <h2 class="section-title">Oversight tools for your coverage area.</h2>
    <div class="feature-grid">
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-verification-queue')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1651592280676-a01949b60cd7?w=700&q=80&auto=format&fit=crop" alt="Verification queue">
    <div class="feature-card-body">
        
        <h3>Verification Queue</h3>
        <p>Review pending farm profiles and mark soil data verified or rejected with notes.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-override-recommendations')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1549888722-bf34acd6a68c?w=700&q=80&auto=format&fit=crop" alt="Override recommendation">
    <div class="feature-card-body">
        
        <h3>Override Recommendations</h3>
        <p>Log a correction when local knowledge should outrank the model's suggestion.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-alerts-amp-advisories')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1752381400523-816117291175?w=700&q=80&auto=format&fit=crop" alt="Alerts">
    <div class="feature-card-body">
        
        <h3>Alerts &amp; Advisories</h3>
        <p>Broadcast pest or weather warnings to a zone, or message a farmer directly.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-training-sessions')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1744744041837-47d851bc9722?w=700&q=80&auto=format&fit=crop" alt="Training sessions">
    <div class="feature-card-body">
        
        <h3>Training Sessions</h3>
        <p>Schedule and publish farmer training sessions for your zone.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
    </div>
</section>

<section class="cta-band">
    <h2>Ready to support your farmers?</h2>
    <p>Officer accounts are reviewed by an admin after email verification.</p>
    <a href="{{ route('register', ['role' => 'extension_officer']) }}" class="btn-cta" style="background:var(--wheat-gold); color:var(--ink);">Apply as an officer</a>
</section>

<div class="modal-overlay" id="modal-verification-queue">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-verification-queue')">&times;</button>
        <img src="https://images.unsplash.com/photo-1651592280676-a01949b60cd7?w=700&q=80&auto=format&fit=crop" alt="Verification queue">
        <h3>Verification Queue</h3>
        <p>Review pending farm profiles and mark soil data verified or rejected with notes.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-override-recommendations">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-override-recommendations')">&times;</button>
        <img src="https://images.unsplash.com/photo-1549888722-bf34acd6a68c?w=700&q=80&auto=format&fit=crop" alt="Override recommendation">
        <h3>Override Recommendations</h3>
        <p>Log a correction when local knowledge should outrank the model's suggestion.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-alerts-amp-advisories">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-alerts-amp-advisories')">&times;</button>
        <img src="https://images.unsplash.com/photo-1752381400523-816117291175?w=700&q=80&auto=format&fit=crop" alt="Alerts">
        <h3>Alerts &amp; Advisories</h3>
        <p>Broadcast pest or weather warnings to a zone, or message a farmer directly.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-training-sessions">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-training-sessions')">&times;</button>
        <img src="https://images.unsplash.com/photo-1744744041837-47d851bc9722?w=700&q=80&auto=format&fit=crop" alt="Training sessions">
        <h3>Training Sessions</h3>
        <p>Schedule and publish farmer training sessions for your zone.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
@endsection
