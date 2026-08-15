@extends('layouts.app')
@section('title', 'Forgot password')

@section('content')
<div class="card auth-card">
    <h2>Forgot your password?</h2>
    <p class="muted">Enter your account email — we'll send a 6-digit code to reset it.</p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        <button type="submit" class="btn-primary" style="width:100%; margin-top:18px;">Send reset code</button>
    </form>
    <p class="auth-switch"><a href="{{ route('login') }}">Back to login</a></p>
</div>
@endsection
