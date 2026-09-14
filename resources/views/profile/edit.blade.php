@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<h2>👤 My Profile</h2>

<div class="grid-2" style="align-items:start; gap:24px;">

    {{-- ---------- Left: identity card ---------- --}}
    <div class="card" style="text-align:center;">
        @if ($user->profile_photo)
            <img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->name }}" class="profile-avatar-lg">
        @else
            <div class="profile-avatar-fallback-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        @endif

        <h3 style="margin:14px 0 2px;">{{ $user->name }}</h3>
        <p class="muted" style="margin:0 0 10px; word-break:break-all;">{{ $user->email }}</p>

        <div class="tag-list" style="justify-content:center;">
            <span class="badge-pending" style="text-transform:capitalize;">{{ str_replace('_', ' ', $user->role) }}</span>
            @if ($user->status === 'active')
                <span class="badge-verified">Active</span>
            @elseif ($user->status === 'pending')
                <span class="badge-pending">Pending approval</span>
            @else
                <span class="badge-rejected">Suspended</span>
            @endif
            @if ($user->isSupplier() && $user->supplierProfile)
                @if ($user->supplierProfile->verified)
                    <span class="badge-verified">✓ Verified supplier</span>
                @else
                    <span class="badge-pending">⏳ Awaiting verification</span>
                @endif
            @endif
        </div>

        <p class="hint" style="margin-top:14px;">Member since {{ $user->created_at?->format('d M Y') ?? '—' }}</p>
    </div>

    {{-- ---------- Right: forms ---------- --}}
    <div>
        <div class="card">
            <h3>Edit profile</h3>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                <label>Profile photo</label>
                <div class="profile-photo-row">
                    <input type="file" name="photo" accept="image/*" style="flex:1; min-width:180px;">
                    @if ($user->profile_photo)
                        <form method="POST" action="{{ route('profile.photo.remove') }}"
                              onsubmit="return confirm('Remove your profile photo?');">
                            @csrf
                            <button type="submit" class="btn-link" style="padding:0; font-size:0.8rem;">Remove current photo</button>
                        </form>
                    @endif
                </div>
                <p class="hint" style="margin-top:4px;">JPG or PNG, up to 2 MB.</p>

                <label style="margin-top:14px;">Full name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="150">

                <label>Email</label>
                <input type="email" value="{{ $user->email }}" disabled style="background:var(--paper-dim); cursor:not-allowed;">
                <p class="hint">Email can't be changed here — contact an admin if it needs to change.</p>

                <label>Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="20" placeholder="e.g. 017XXXXXXXX">

                @if ($user->isSupplier())
                    <label style="margin-top:14px;">Business name</label>
                    <input type="text" name="business_name"
                           value="{{ old('business_name', $user->supplierProfile->business_name ?? '') }}"
                           required maxlength="150">

                    <label>Business address</label>
                    <textarea name="business_address" rows="2" maxlength="255">{{ old('business_address', $user->supplierProfile->business_address ?? '') }}</textarea>

                    <label>bKash number (for receiving payments)</label>
                    <input type="text" name="bkash_number"
                           value="{{ old('bkash_number', $user->supplierProfile->bkash_number ?? '') }}" maxlength="20">
                @endif

                <button type="submit" class="btn-primary btn-block" style="margin-top:16px;">Save changes</button>
            </form>
        </div>

        <div class="card" style="margin-top:20px;">
            <h3>Change password</h3>
            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf
                <label>Current password</label>
                <div class="password-wrapper">
                    <input type="password" id="current-password" name="current_password" required>
                    <button type="button" class="toggle-password" data-target="current-password">👁</button>
                </div>

                <label>New password</label>
                <div class="password-wrapper">
                    <input type="password" id="new-password" name="password" required minlength="8">
                    <button type="button" class="toggle-password" data-target="new-password">👁</button>
                </div>

                <label>Confirm new password</label>
                <div class="password-wrapper">
                    <input type="password" id="new-password-confirm" name="password_confirmation" required minlength="8">
                    <button type="button" class="toggle-password" data-target="new-password-confirm">👁</button>
                </div>

                <button type="submit" class="btn-secondary btn-block" style="margin-top:16px;">Update password</button>
            </form>
        </div>
    </div>
</div>
@endsection
