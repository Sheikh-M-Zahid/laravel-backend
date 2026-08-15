@extends('layouts.app')
@section('title', 'Supplier Dashboard')
@section('content')
<div class="role-banner tone-supplier" style="background-image: url('https://images.unsplash.com/photo-1744744041837-47d851bc9722?w=1600&q=80&auto=format&fit=crop');">
    <div class="role-banner-content">
        <span class="role-banner-eyebrow">Reach more farmers</span>
        <h2>{{ $supplier?->business_name ?? 'Your storefront' }} is live on the marketplace.</h2>
        <p>Every farmer running a fertilizer recommendation sees your listings first when stock is available.</p>
    </div>
</div>

<h2>🏪 Supplier Dashboard</h2>

@unless ($supplier?->verified)
    <div class="alert alert-error">Your supplier account hasn't been verified by an admin yet.</div>
@endunless

<h3>Actions</h3>
<div class="action-grid">
    <button type="button" class="action-tile" onclick="openModal('modal-bkash')">
        <span class="action-icon">💳</span>
        <h4>bKash Payment Number</h4>
        <p>{{ $supplier?->bkash_number ? 'Current: ' . $supplier->bkash_number : 'Not set yet — farmers can\'t pay you until this is set.' }}</p>
        <span class="action-cta">Open →</span>
    </button>
    <button type="button" class="action-tile" onclick="openModal('modal-my-farm')">
        <span class="action-icon">🚜</span>
        <h4>My Farm</h4>
        <p>Products, farmer orders, and inquiries — each in its own page.</p>
        <span class="action-cta">Open →</span>
    </button>
    <a href="{{ route('supplier.demand-forecast') }}" class="action-tile" style="text-decoration:none; display:flex;">
        <span class="action-icon">📈</span>
        <h4>Demand Forecast</h4>
        <p>See projected demand by zone.</p>
        <span class="action-cta">Open →</span>
    </a>
</div>

<div class="modal-overlay" id="modal-my-farm">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-my-farm')">&times;</button>
        <h3>🚜 My Farm</h3>
        <p class="muted">Pick a section — each opens its own page.</p>
        <a href="{{ route('supplier.my-products') }}" class="btn-primary btn-block" style="text-decoration:none; text-align:center; margin-bottom:10px;">📦 My Products</a>
        <a href="{{ route('supplier.my-orders') }}" class="btn-primary btn-block" style="text-decoration:none; text-align:center; margin-bottom:10px;">🚚 Farmer Orders — Delivery &amp; Payment</a>
        <a href="{{ route('supplier.my-inquiries') }}" class="btn-primary btn-block" style="text-decoration:none; text-align:center;">💬 Farmer Inquiries</a>
    </div>
</div>

<div class="modal-overlay" id="modal-bkash">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-bkash')">&times;</button>
        <h3>💳 bKash Payment Number</h3>
        <p class="muted">Farmers will send payment here and submit the TrxID for you to verify.</p>
        <form method="POST" action="{{ route('supplier.bkash.update') }}">
            @csrf
            <label>bKash number</label>
            <input type="text" name="bkash_number" value="{{ $supplier?->bkash_number }}" placeholder="01XXXXXXXXX" required>
            <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Save</button>
        </form>
    </div>
</div>

<h3 style="margin-top:30px;">Quick glance</h3>
<div class="grid-3">
    <div class="card"><strong>{{ $items->count() }}</strong> product(s) listed</div>
    <div class="card"><strong>{{ $orders->where('order_status', '!=', 'completed')->where('order_status', '!=', 'cancelled')->count() }}</strong> order(s) in progress</div>
    <div class="card"><strong>{{ $inquiries->whereNull('response')->count() }}</strong> inquiry(ies) awaiting reply</div>
</div>
@endsection
