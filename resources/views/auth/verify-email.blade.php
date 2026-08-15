@extends('layouts.app')
@section('title', 'Verify your email')

@section('content')
<div class="card auth-card">
    <h2>Check your email</h2>
    <p class="muted">We sent a 6-digit code to <strong>{{ $email }}</strong>. Enter it below to finish creating your account.</p>

    <form method="POST" action="{{ route('verify-email') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <label>Verification code</label>
        <input type="text" name="otp" class="otp-input" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" placeholder="000000" required autofocus>
        <div id="otp-timer" data-expires-in-seconds="300" class="otp-timer"></div>

        <button type="submit" class="btn-primary" style="width:100%; margin-top:18px;">Verify &amp; create account</button>
    </form>

    <form method="POST" action="{{ route('verify-email.resend') }}" style="margin-top:14px;">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <button type="submit" class="btn-link" style="padding:0;">Didn't get it? Resend code</button>
    </form>

    <p class="auth-switch">Wrong email? <a href="{{ route('register') }}">Start over</a></p>
</div>
@endsection
