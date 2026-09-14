@extends('layouts.app')
@section('title', 'Training Sessions')
@section('content')
<h2>📅 Training Sessions</h2>
<p class="muted">Sessions your Extension Officer scheduled in your zone(s).</p>

@forelse ($sessions as $session)
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">
            <div>
                <strong>{{ $session->title }}</strong>
                <div class="muted">{{ $session->zone->zone_name ?? '—' }} • {{ $session->location ?? 'Location TBA' }}</div>
                @if ($session->description)
                    <p style="margin:8px 0 0;">{{ $session->description }}</p>
                @endif
                <p class="hint" style="margin-top:6px;">
                    {{ \Illuminate\Support\Carbon::parse($session->session_date)->format('d M Y, h:i A') }}
                    — hosted by {{ $session->officer->name ?? 'an Extension Officer' }}
                </p>
            </div>
            <div>
                @if ($registeredSessionIds->contains($session->id))
                    <span class="badge-verified">✓ Registered</span>
                @else
                    <form method="POST" action="{{ route('farmer.trainings.register', $session->id) }}">
                        @csrf
                        <button type="submit" class="btn-primary">Register</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="card"><p class="muted">No training sessions scheduled in your zone(s) yet.</p></div>
@endforelse
@endsection
