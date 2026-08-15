@extends('layouts.app')
@section('title', 'Farmer Orders — Supplier')
@section('content')
<p><a href="{{ route('supplier.dashboard') }}">← Back to dashboard</a></p>
<h2>🚚 Farmer Orders — Delivery &amp; Payment</h2>

<div class="card">
    <table class="data-table">
        <thead><tr><th>Farmer</th><th>Items</th><th>Total / Paid / Due</th><th>Delivery</th><th>Payment</th></tr></thead>
        <tbody>
        @forelse ($orders as $order)
            <tr>
                <td>{{ $order->farmer->name }}</td>
                <td>
                    @foreach ($order->items as $item)
                        {{ $item->product->product_name }} × {{ $item->quantity }}<br>
                    @endforeach
                </td>
                <td class="mono">
                    ৳{{ $order->total_amount }} / ৳{{ $order->amount_paid }} /
                    <span style="color:{{ $order->due_amount > 0 ? 'var(--danger)' : 'var(--field-green)' }};">৳{{ $order->due_amount }}</span>
                </td>
                <td><button type="button" class="btn-small" onclick="openModal('modal-delivery-{{ $order->id }}')">{{ $order->order_status === 'completed' ? 'Delivered' : ucfirst($order->order_status) }}</button></td>
                <td>
                    @if ($order->payment_status === 'paid')
                        <span class="badge-verified">✓ Paid</span>
                    @elseif ($order->payment_status === 'pending_verification')
                        <button type="button" class="btn-small" onclick="openModal('modal-payment-{{ $order->id }}')">Review payment</button>
                    @else
                        <span class="badge-pending">Unpaid</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">No orders yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@foreach ($orders as $order)
    <div class="modal-overlay" id="modal-delivery-{{ $order->id }}">
        <div class="modal-box">
            <button type="button" class="modal-close" onclick="closeModal('modal-delivery-{{ $order->id }}')">&times;</button>
            <h3>Order #{{ $order->id }} — Delivery Status</h3>
            <form method="POST" action="{{ route('supplier.orders.fulfil', $order->id) }}">
                @csrf
                <label>Status</label>
                <select name="order_status" required>
                    <option value="confirmed" @selected($order->order_status=='confirmed')>Confirmed</option>
                    <option value="shipped" @selected($order->order_status=='shipped')>Shipped</option>
                    <option value="completed" @selected($order->order_status=='completed')>Delivered</option>
                    <option value="cancelled" @selected($order->order_status=='cancelled')>Cancelled</option>
                </select>
                <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Update</button>
            </form>
        </div>
    </div>

    @if ($order->payment_status === 'pending_verification')
        <div class="modal-overlay" id="modal-payment-{{ $order->id }}">
            <div class="modal-box">
                <button type="button" class="modal-close" onclick="closeModal('modal-payment-{{ $order->id }}')">&times;</button>
                <h3>Verify Payment — Order #{{ $order->id }}</h3>
                <p class="muted">
                    TrxID: <strong>{{ $order->bkash_trx_id }}</strong><br>
                    From: {{ $order->bkash_sender_number }}<br>
                    Amount: ৳{{ $order->amount_paid }}
                </p>
                <form method="POST" action="{{ route('supplier.orders.verify-payment', $order->id) }}" style="margin-bottom:8px;">
                    @csrf
                    <input type="hidden" name="decision" value="confirm">
                    <button type="submit" class="btn-primary btn-block">✓ Confirm payment</button>
                </form>
                <form method="POST" action="{{ route('supplier.orders.verify-payment', $order->id) }}">
                    @csrf
                    <input type="hidden" name="decision" value="reject">
                    <input type="text" name="note" placeholder="Reason (optional)">
                    <button type="submit" class="btn-primary btn-block" style="margin-top:8px; background:var(--danger);">✗ Reject</button>
                </form>
            </div>
        </div>
    @endif
@endforeach
@endsection
