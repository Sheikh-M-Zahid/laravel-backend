@extends('layouts.app')
@section('title', 'Reset password')

@section('content')
<div class="card auth-card">
    <h2>Reset your password</h2>
    <p class="muted">Enter the 6-digit code sent to <strong>{{ $email }}</strong> and choose a new password.</p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">

        <label>Verification code</label>
        <input type="text" name="otp" class="otp-input" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" placeholder="000000" required autofocus>
        <div id="otp-timer" data-expires-in-seconds="300" class="otp-timer"></div>

        <label>New password</label>
        <div class="password-wrapper">
            <input type="password" id="reset-password" name="password" required minlength="8">
            <button type="button" class="toggle-password" data-target="reset-password">👁</button>
        </div>

        <label>Confirm new password</label>
        <div class="password-wrapper">
            <input type="password" id="reset-password-confirm" name="password_confirmation" required minlength="8">
            <button type="button" class="toggle-password" data-target="reset-password-confirm">👁</button>
        </div>

        <button type="submit" class="btn-primary" style="width:100%; margin-top:18px;">Reset password</button>
    </form>

    <form method="POST" action="{{ route('password.resend') }}" style="margin-top:14px;">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <button type="submit" class="btn-link" style="padding:0;">Didn't get it? Resend code</button>
    </form>
</div>
@endsection
