@extends('layouts.app')
@section('title', 'For Suppliers — Smart Agri-Advisory Platform')
@section('full_width', true)

@section('content')
<section class="hero" style="min-height:380px; background-image: url('https://images.unsplash.com/photo-1744744041837-47d851bc9722?w=1600&q=80&auto=format&fit=crop');">
    <div class="hero-content">
        <span class="hero-eyebrow">For Suppliers</span>
        <h1 style="font-size:2.4rem;">Reach farmers right when they need inputs.</h1>
        <p class="lead">List seed and fertilizer, see demand forecasts by zone, and manage orders and inquiries from one dashboard.</p>
        <div class="hero-ctas">
            <a href="{{ route('register', ['role' => 'supplier']) }}" class="btn-cta">Register a business</a>
            <a href="{{ route('login') }}" class="btn-cta-outline">Already have an account? Log in</a>
        </div>
    </div>
</section>

<section class="section container-wide">
    <div class="section-eyebrow">What you get</div>
    <h2 class="section-title">A storefront tied to real demand.</h2>
    <div class="feature-grid">
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-product-listings')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1651592280676-a01949b60cd7?w=700&q=80&auto=format&fit=crop" alt="Product listing">
    <div class="feature-card-body">
        
        <h3>Product Listings</h3>
        <p>List seed or fertilizer with price and stock — updated any time.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-demand-forecast')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1499260126922-fbb24624a4e8?w=700&q=80&auto=format&fit=crop" alt="Demand forecast">
    <div class="feature-card-body">
        
        <h3>Demand Forecast</h3>
        <p>See projected demand by zone so you can stock ahead of the season.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-order-management')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1549888722-bf34acd6a68c?w=700&q=80&auto=format&fit=crop" alt="Order management">
    <div class="feature-card-body">
        
        <h3>Order Management</h3>
        <p>Track and update farmer orders from pending through fulfilled.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
        <div class="feature-card feature-card-clickable" onclick="openModal('modal-farmer-inquiries')" role="button" tabindex="0">
    <img src="https://images.unsplash.com/photo-1752381400523-816117291175?w=700&q=80&auto=format&fit=crop" alt="Inquiries">
    <div class="feature-card-body">
        
        <h3>Farmer Inquiries</h3>
        <p>Answer product questions before a farmer commits to an order.</p>
        <p class="hint" style="margin-top:8px;">Tap to learn more →</p>
    </div>
</div>
    </div>
</section>

<section class="cta-band">
    <h2>Ready to list your first product?</h2>
    <p>Supplier accounts are reviewed by an admin after email verification.</p>
    <a href="{{ route('register', ['role' => 'supplier']) }}" class="btn-cta" style="background:var(--wheat-gold); color:var(--ink);">Register a business</a>
</section>

<div class="modal-overlay" id="modal-product-listings">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-product-listings')">&times;</button>
        <img src="https://images.unsplash.com/photo-1651592280676-a01949b60cd7?w=700&q=80&auto=format&fit=crop" alt="Product listing">
        <h3>Product Listings</h3>
        <p>List seed or fertilizer with price and stock — updated any time.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-demand-forecast">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-demand-forecast')">&times;</button>
        <img src="https://images.unsplash.com/photo-1499260126922-fbb24624a4e8?w=700&q=80&auto=format&fit=crop" alt="Demand forecast">
        <h3>Demand Forecast</h3>
        <p>See projected demand by zone so you can stock ahead of the season.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-order-management">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-order-management')">&times;</button>
        <img src="https://images.unsplash.com/photo-1549888722-bf34acd6a68c?w=700&q=80&auto=format&fit=crop" alt="Order management">
        <h3>Order Management</h3>
        <p>Track and update farmer orders from pending through fulfilled.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
<div class="modal-overlay" id="modal-farmer-inquiries">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-farmer-inquiries')">&times;</button>
        <img src="https://images.unsplash.com/photo-1752381400523-816117291175?w=700&q=80&auto=format&fit=crop" alt="Inquiries">
        <h3>Farmer Inquiries</h3>
        <p>Answer product questions before a farmer commits to an order.</p>
        <p>This feature is available from your dashboard once you create an account, and every result it produces is logged so you (and, where relevant, your Extension Officer) can review it later.</p>
        <a href="{{ route('register') }}" class="btn-primary" style="width:100%; text-align:center; margin-top:10px;">Create free account</a>
    </div>
</div>
@endsection
