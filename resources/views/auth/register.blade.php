@extends('layouts.app')
@section('title', 'Create an account — Smart Agri-Advisory Platform')

@section('content')
<div class="card auth-card">
    <h2>Create your account</h2>
    <p class="muted">We'll email you a 6-digit code to confirm your address before your account is created.</p>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <label>Full name</label>
        <input type="text" name="name" value="{{ old('name') }}" required>

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>

        <label>Phone number</label>
        <input type="text" name="phone" value="{{ old('phone') }}">

        <label>Password</label>
        <div class="password-wrapper">
            <input type="password" id="reg-password" name="password" required minlength="8">
            <button type="button" class="toggle-password" data-target="reg-password">👁</button>
        </div>

        <label>Confirm password</label>
        <div class="password-wrapper">
            <input type="password" id="reg-password-confirm" name="password_confirmation" required minlength="8">
            <button type="button" class="toggle-password" data-target="reg-password-confirm">👁</button>
        </div>

        <label>I am signing up as a...</label>
        <select name="role" id="role-select" required onchange="toggleSupplierFields()">
            <option value="farmer" @selected(old('role', request('role'))==='farmer')>🌾 Farmer</option>
            <option value="extension_officer" @selected(old('role', request('role'))==='extension_officer')>🧑‍🌾 Extension Officer</option>
            <option value="supplier" @selected(old('role', request('role'))==='supplier')>🏪 Supplier</option>
        </select>
        <p class="hint">*Officer and supplier accounts stay pending until an admin approves them, after email verification.
            Farm details (zone, soil readings) are added from your dashboard after login.</p>

        <div id="supplier-fields" style="display:none">
            <label>Business name</label>
            <input type="text" name="business_name" value="{{ old('business_name') }}">
            <label>Business address</label>
            <input type="text" name="business_address" value="{{ old('business_address') }}">
        </div>

        <button type="submit" class="btn-primary" style="width:100%; margin-top:18px;">Send verification code</button>
    </form>
    <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
</div>

<script>
function toggleSupplierFields() {
    const role = document.getElementById('role-select').value;
    document.getElementById('supplier-fields').style.display = role === 'supplier' ? 'block' : 'none';
}
toggleSupplierFields();
</script>
@endsection
