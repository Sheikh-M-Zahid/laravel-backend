@extends('layouts.app')
@section('title', 'For Admins — Smart Agri-Advisory Platform')
@section('full_width', true)

@section('content')
<section class="hero" style="min-height:380px; background-image: url('https://images.unsplash.com/photo-1499260126922-fbb24624a4e8?w=1600&q=80&auto=format&fit=crop');">
    <div class="hero-content">
        <span class="hero-eyebrow">For Admins</span>
        <h1 style="font-size:2.4rem;">Keep the whole platform running.</h1>
        <p class="lead">Approve new officer and supplier accounts, manage reference data, and trigger model retraining as new data comes in.</p>
        <div class="hero-ctas">
            <a href="{{ route('login') }}" class="btn-cta">Admin sign-in</a>
        </div>
        <p class="hint" style="color:rgba(255,255,255,0.75);">Admin accounts aren't self-registered — ask an existing admin to create or promote yours.</p>
    </div>
</section>

<section class="section container-wide">
    <div class="section-eyebrow">What you get</div>
    <h2 class="section-title">Governance tools for the whole system.</h2>
    <div class="feature-grid">
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-account-approvals')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1651592280676-a01949b60cd7?w=700&q=80&auto=format&fit=crop" alt="Approve accounts">
    <div class="feature-card-body">
        
        <h3>Account Approvals</h3>
        <p>Review and activate pending officer and supplier signups.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-reference-data')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1549888722-bf34acd6a68c?w=700&q=80&auto=format&fit=crop" alt="Reference data">
    <div class="feature-card-body">
        
        <h3>Reference Data</h3>
        <p>Manage climate zones and the crop catalogue that every model relies on.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-model-retraining')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1752381400523-816117291175?w=700&q=80&auto=format&fit=crop" alt="Model retraining">
    <div class="feature-card-body">
        
        <h3>Model Retraining</h3>
        <p>Queue a retraining job for any of the four ML models.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-platform-analytics')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1744744041837-47d851bc9722?w=700&q=80&auto=format&fit=crop" alt="Analytics">
    <div class="feature-card-body">
        
        <h3>Platform Analytics</h3>
        <p>Track active farmers, recommendation volume, and orders over time.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
    </div>
</section>

<div class="modal-overlay" id="modal-account-approvals">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-account-approvals')">&times;</button>
        <img src="https://images.unsplash.com/photo-1651592280676-a01949b60cd7?w=700&q=80&auto=format&fit=crop" alt="Approve accounts">
        <h3>Account Approvals</h3>
        <p>Review and activate pending officer and supplier signups.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-reference-data">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-reference-data')">&times;</button>
        <img src="https://images.unsplash.com/photo-1549888722-bf34acd6a68c?w=700&q=80&auto=format&fit=crop" alt="Reference data">
        <h3>Reference Data</h3>
        <p>Manage climate zones and the crop catalogue that every model relies on.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-model-retraining">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-model-retraining')">&times;</button>
        <img src="https://images.unsplash.com/photo-1752381400523-816117291175?w=700&q=80&auto=format&fit=crop" alt="Model retraining">
        <h3>Model Retraining</h3>
        <p>Queue a retraining job for any of the four ML models.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-platform-analytics">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-platform-analytics')">&times;</button>
        <img src="https://images.unsplash.com/photo-1744744041837-47d851bc9722?w=700&q=80&auto=format&fit=crop" alt="Analytics">
        <h3>Platform Analytics</h3>
        <p>Track active farmers, recommendation volume, and orders over time.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
@endsection
