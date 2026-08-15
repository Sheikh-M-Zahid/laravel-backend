@extends('layouts.app')
@section('title', 'My Orders')
@section('content')
<h2>📦 My Orders</h2>

@forelse ($orders as $order)
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">
            <div>
                <strong>Order #{{ $order->id }}</strong> — {{ $order->supplier->business_name }}
                <div class="muted">
                    @foreach ($order->items as $item)
                        {{ $item->product->product_name }} × {{ $item->quantity }} (৳{{ $item->unit_price }} each)<br>
                    @endforeach
                </div>
            </div>
            <div style="text-align:right;">
                <div>Total: <span class="mono">৳{{ $order->total_amount }}</span></div>
                <div>Paid: <span class="mono">৳{{ $order->amount_paid }}</span></div>
                <div>Due: <span class="mono" style="color:{{ $order->due_amount > 0 ? 'var(--danger)' : 'var(--field-green)' }};">৳{{ $order->due_amount }}</span></div>
            </div>
        </div>

        <div style="display:flex; gap:10px; margin-top:10px; flex-wrap:wrap;">
            <span class="badge-{{ $order->order_status === 'completed' ? 'verified' : ($order->order_status === 'cancelled' ? 'rejected' : 'pending') }}">
                🚚 {{ $order->order_status === 'completed' ? 'Delivered' : ucfirst($order->order_status) }}
            </span>
            <span class="badge-{{ $order->payment_status === 'paid' ? 'verified' : ($order->payment_status === 'pending_verification' ? 'pending' : 'rejected') }}">
                💰 {{ $order->payment_status === 'paid' ? 'Paid' : ($order->payment_status === 'pending_verification' ? 'Payment under review' : 'Unpaid') }}
            </span>
        </div>

        @if ($order->due_amount > 0 && $order->payment_status !== 'pending_verification')
            <details style="margin-top:14px;">
                <summary>💳 Pay via bKash — send ৳{{ $order->due_amount }} to {{ $order->supplier->bkash_number ?? 'the number the supplier provides' }}</summary>
                <form method="POST" action="{{ route('farmer.orders.pay', $order->id) }}">
                    @csrf
                    <label>Your bKash number (the one you sent from)</label>
                    <input type="text" name="bkash_sender_number" placeholder="01XXXXXXXXX" required>
                    <label>bKash Transaction ID (TrxID)</label>
                    <input type="text" name="bkash_trx_id" placeholder="e.g. 9F7A2K3XYZ" required>
                    <label>Amount sent (৳)</label>
                    <input type="number" step="0.01" name="amount_paid" value="{{ $order->due_amount }}" required>
                    <button type="submit" class="btn-primary" style="margin-top:10px;">Submit payment</button>
                </form>
            </details>
        @elseif ($order->payment_status === 'pending_verification')
            <p class="hint" style="margin-top:10px;">
                TrxID <strong>{{ $order->bkash_trx_id }}</strong> (from {{ $order->bkash_sender_number }}) submitted on
                {{ $order->payment_submitted_at?->format('d M Y, h:i A') }} — waiting for the supplier to confirm.
            </p>
        @endif
    </div>
@empty
    <div class="card"><p class="muted">You haven't placed any orders yet. Visit the <a href="{{ route('farmer.marketplace') }}">marketplace</a> to order inputs.</p></div>
@endforelse
@endsection
