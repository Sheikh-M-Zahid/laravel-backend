@extends('layouts.app')
@section('title', 'My Products — Supplier')
@section('content')
<p><a href="{{ route('supplier.dashboard') }}">← Back to dashboard</a></p>
<h2>📦 My Products</h2>

<div class="card">
    <button type="button" class="btn-primary" onclick="openModal('modal-new-product')">➕ List a new product</button>
</div>

<div class="modal-overlay" id="modal-new-product">
    <div class="modal-box">
        <button type="button" class="modal-close" onclick="closeModal('modal-new-product')">&times;</button>
        <h3>➕ List a New Product</h3>
        <form method="POST" action="{{ route('supplier.products.store') }}">
            @csrf
            <label>Product name</label>
            <input type="text" name="product_name" required>
            <label>Category</label>
            <select name="category" required>
                <option value="seed">Seed</option>
                <option value="fertilizer">Fertilizer</option>
            </select>
            <div class="grid-2">
                <div><label>Price (৳)</label><input type="number" step="0.01" name="price" required></div>
                <div><label>Stock quantity</label><input type="number" name="stock_quantity" required></div>
            </div>
            <label>Description</label>
            <textarea name="description" rows="2"></textarea>
            <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Add product</button>
        </form>
    </div>
</div>

<div class="card">
    <table class="data-table">
        <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th></th></tr></thead>
        <tbody>
        @forelse ($items as $item)
            <tr>
                <td>{{ $item->product_name }}</td><td>{{ $item->category }}</td>
                <td class="mono">৳{{ $item->price }}</td><td class="mono">{{ $item->stock_quantity }}</td>
                <td><button type="button" class="btn-small" onclick="openModal('modal-stock-{{ $item->id }}')">Update</button></td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">You haven't listed any products yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@foreach ($items as $item)
    <div class="modal-overlay" id="modal-stock-{{ $item->id }}">
        <div class="modal-box">
            <button type="button" class="modal-close" onclick="closeModal('modal-stock-{{ $item->id }}')">&times;</button>
            <h3>Update {{ $item->product_name }}</h3>
            <form method="POST" action="{{ route('supplier.products.stock', $item->id) }}">
                @csrf
                <label>Stock quantity</label>
                <input type="number" name="stock_quantity" value="{{ $item->stock_quantity }}" required>
                <label>Price (৳)</label>
                <input type="number" step="0.01" name="price" value="{{ $item->price }}" required>
                <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Save</button>
            </form>
        </div>
    </div>
@endforeach
@endsection
