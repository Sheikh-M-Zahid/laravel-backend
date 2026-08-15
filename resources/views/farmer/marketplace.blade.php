@extends('layouts.app')
@section('title', 'Input Marketplace')
@section('content')
<h2>🛒 Input Marketplace</h2>
<p><a href="{{ route('farmer.orders') }}">📦 View my orders (delivery &amp; payment status) →</a></p>

<div class="grid-3">
    @foreach ($items as $item)
        <div class="product-card">
            <h4>{{ $item->product_name }}</h4>
            <p class="muted" style="margin:0;">{{ ucfirst($item->category) }} • Sold by {{ $item->supplier->business_name }}</p>

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
    @endforeach
</div>
@endsection
