<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 20px; color: #1F4D2C; margin-bottom: 2px; }
        .muted { color: #777; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { text-align: left; padding: 6px 8px; border-bottom: 1px solid #ddd; }
        th { background: #F4F1E6; }
        .totals td { border: none; padding: 3px 8px; }
        .totals .label { text-align: right; color: #555; }
        .totals .amount { text-align: right; font-weight: bold; width: 110px; }
        .header-row { width: 100%; }
        .header-row td { border: none; vertical-align: top; padding: 0; }
    </style>
</head>
<body>
    <table class="header-row">
        <tr>
            <td>
                <h1>🌾 Smart Agri-Advisory Platform</h1>
                <p class="muted">Invoice / Receipt</p>
            </td>
            <td style="text-align:right;">
                <strong>Order #{{ $order->id }}</strong><br>
                <span class="muted">{{ $order->created_at?->format('d M Y, h:i A') }}</span>
            </td>
        </tr>
    </table>

    <table class="header-row" style="margin-top:20px;">
        <tr>
            <td>
                <strong>Billed to</strong><br>
                {{ $order->farmer->name }}<br>
                {{ $order->farmer->email }}<br>
                {{ $order->farmer->phone }}
            </td>
            <td>
                <strong>Supplier</strong><br>
                {{ $order->supplier->business_name }}<br>
                {{ $order->supplier->business_address }}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr><th>Item</th><th>Qty</th><th>Unit price</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
        @foreach ($order->items as $item)
            <tr>
                <td>{{ $item->product->product_name ?? 'Item' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>৳{{ number_format($item->unit_price, 2) }}</td>
                <td>৳{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="totals" style="width:260px; margin-left:auto;">
        <tr><td class="label">Total</td><td class="amount">৳{{ number_format($order->total_amount, 2) }}</td></tr>
        <tr><td class="label">Paid</td><td class="amount">৳{{ number_format($order->amount_paid, 2) }}</td></tr>
        <tr><td class="label">Due</td><td class="amount">৳{{ number_format($order->due_amount, 2) }}</td></tr>
    </table>

    <p style="margin-top:30px;" class="muted">
        Order status: {{ ucfirst($order->order_status) }} · Payment status: {{ str_replace('_', ' ', ucfirst($order->payment_status)) }}
        @if ($order->bkash_trx_id) · bKash TrxID: {{ $order->bkash_trx_id }} @endif
    </p>
    <p class="muted">This is a system-generated receipt from the Smart Agri-Advisory Platform.</p>
</body>
</html>
