@extends('layouts.app')
@section('title', 'My Recommendation History')
@section('content')
<h2>📜 My Recommendation History</h2>
<div class="card">
    <table class="data-table">
        <thead><tr><th>ID</th><th>Crop</th><th>Confidence</th><th>Plot / Zone</th><th>Date</th><th>Fertilizer</th></tr></thead>
        <tbody>
        @forelse ($recommendations as $rec)
            <tr>
                <td class="mono">#{{ $rec->id }}</td>
                <td>{{ $rec->recommendedCrop->crop_name ?? '—' }}</td>
                <td class="mono">{{ $rec->confidence_score }}%</td>
                <td>
                    @if ($rec->farmProfile)
                        Plot #{{ $rec->farmProfile->id }} — {{ $rec->farmProfile->zone->zone_name ?? '—' }}
                    @else
                        —
                    @endif
                </td>
                <td>{{ $rec->created_at?->format('d M Y') ?? '—' }}</td>
                <td>
                    @if ($rec->fertilizerRecommendation)
                        {{ $rec->fertilizerRecommendation->fertilizer_type }}: {{ $rec->fertilizerRecommendation->dosage_kg_per_acre }} kg/acre
                    @else
                        —
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">You don't have any recommendations yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $recommendations->links() }}
</div>
@endsection
