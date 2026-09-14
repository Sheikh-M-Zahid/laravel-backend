@extends('layouts.app')
@section('title', 'Input Marketplace')
@section('content')
<h2>🛒 Input Marketplace</h2>
<p><a href="{{ route('farmer.orders') }}">📦 View my orders (delivery &amp; payment status) →</a></p>

<div class="card">
    <form method="GET" action="{{ route('farmer.marketplace') }}" class="inline-form">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products…" style="flex:1; min-width:180px;">
        <select name="category">
            <option value="">All categories</option>
            <option value="seed" {{ request('category') === 'seed' ? 'selected' : '' }}>Seed</option>
            <option value="fertilizer" {{ request('category') === 'fertilizer' ? 'selected' : '' }}>Fertilizer</option>
        </select>
        <button type="submit" class="btn-primary">Search</button>
        @if (request('q') || request('category'))
            <a href="{{ route('farmer.marketplace') }}" class="btn-link">Clear</a>
        @endif
    </form>
</div>

<div class="grid-3" style="margin-top:18px;">
    @forelse ($items as $item)
        <div class="product-card">
            <h4>{{ $item->product_name }}</h4>
            <p class="muted" style="margin:0;">
                {{ ucfirst($item->category) }} • Sold by {{ $item->supplier->business_name }}
                @if ($item->supplier->avg_rating)
                    <br><span class="mono">{{ str_repeat('★', round($item->supplier->avg_rating)) }}{{ str_repeat('☆', 5 - round($item->supplier->avg_rating)) }}</span>
                    <span class="hint">{{ $item->supplier->avg_rating }}/5</span>
                @endif
            </p>

            <div class="price-row">
                <span class="mono">৳{{ $item->price }}</span>
                <span class="muted">Stock: {{ $item->stock_quantity }}</span>
            </div>

            @if ($item->supplier->bkash_number)
                <span class="bkash-hint">💳 Pay via bKash: {{ $item->supplier->bkash_number }}</span>
            @endif

            <div class="product-actions">
                <button type="button" class="btn-primary" onclick="openModal('modal-order-{{ $item->id }}')">Place Order</button>
                <a href="{{ route('farmer.orders') }}" class="btn-secondary" style="text-decoration:none; text-align:center;">Make Payment</a>
            </div>
        </div>

        <div class="modal-overlay" id="modal-order-{{ $item->id }}">
            <div class="modal-box">
                <button type="button" class="modal-close" onclick="closeModal('modal-order-{{ $item->id }}')">&times;</button>
                <h3>Order {{ $item->product_name }}</h3>
                <p class="muted">{{ $item->supplier->business_name }} • ৳{{ $item->price }} each • {{ $item->stock_quantity }} in stock</p>
                <form method="POST" action="{{ route('farmer.orders.store') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item->id }}">
                    <label>Quantity</label>
                    <input type="number" name="quantity" min="1" max="{{ $item->stock_quantity }}" value="1" required>
                    <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Confirm order</button>
                </form>
                <p class="hint" style="margin-top:10px;">After ordering, go to <strong>My Orders</strong> to pay via bKash and submit your TrxID.</p>
            </div>
        </div>
    @empty
        <p class="muted">No products match your search.</p>
    @endforelse
</div>

<div style="margin-top:20px;">
    {{ $items->links() }}
</div>
@endsection
