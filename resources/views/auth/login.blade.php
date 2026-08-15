@extends('layouts.app')
@section('title', 'Log in — Smart Agri-Advisory Platform')

@section('content')
<div class="card auth-card">
    <h2>Log in</h2>
    <p class="muted">Welcome back — pick up where you left off.</p>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label>Password</label>
        <div class="password-wrapper">
            <input type="password" id="login-password" name="password" required>
            <button type="button" class="toggle-password" data-target="login-password">👁</button>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
            <label class="checkbox-label" style="margin:0;">
                <input type="checkbox" name="remember" style="width:auto;"> Remember me
            </label>
            <a href="{{ route('password.request') }}" style="font-size:0.85rem;">Forgot password?</a>
        </div>

        <button type="submit" class="btn-primary" style="width:100%; margin-top:18px;">Log in</button>
    </form>
    <p class="auth-switch">Don't have an account? <a href="{{ route('register') }}">Create one</a></p>
</div>
@endsection
