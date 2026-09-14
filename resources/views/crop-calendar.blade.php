@extends('layouts.app')
@section('title', 'Crop Calendar')
@section('content')
<h2>📅 Crop Calendar</h2>
<p class="muted">Sowing and harvest windows by climate zone — added by admins as new crops/zones are onboarded.</p>

<div class="card">
    <form method="GET" action="{{ route('crop-calendar') }}" class="inline-form">
        <label style="margin:0;">Zone:</label>
        <select name="zone_id" onchange="this.form.submit()">
            @foreach ($zones as $zone)
                <option value="{{ $zone->id }}" {{ $selectedZoneId == $zone->id ? 'selected' : '' }}>
                    {{ $zone->zone_name }} ({{ $zone->region }})
                </option>
            @endforeach
        </select>
    </form>

    <table class="data-table" style="margin-top:16px;">
        <thead>
            <tr><th>Crop</th><th>Season</th><th>Sowing window</th><th>Harvest window</th></tr>
        </thead>
        <tbody>
        @forelse ($entries as $entry)
            <tr>
                <td>{{ $entry->crop->crop_name ?? '—' }}</td>
                <td>{{ $entry->crop->season ?? '—' }}</td>
                <td>
                    @if ($entry->sowing_start || $entry->sowing_end)
                        {{ optional($entry->sowing_start)->format('d M') }} – {{ optional($entry->sowing_end)->format('d M') }}
                    @else
                        —
                    @endif
                </td>
                <td>
                    @if ($entry->harvest_start || $entry->harvest_end)
                        {{ optional($entry->harvest_start)->format('d M') }} – {{ optional($entry->harvest_end)->format('d M') }}
                    @else
                        —
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="muted">No crop calendar entries for this zone yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
