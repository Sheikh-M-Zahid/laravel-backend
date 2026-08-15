@extends('layouts.app')
@section('title', 'Platform Analytics')
@section('content')
<h2>📊 Platform Analytics</h2>

<div class="card">
    <h3>Crop Recommendation Distribution</h3>
    <table class="data-table">
        <thead><tr><th>Crop</th><th>Recommendations</th></tr></thead>
        <tbody>
        @foreach ($cropCounts as $c)
            <tr><td>{{ $c->crop }}</td><td class="mono">{{ $c->total }}</td></tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="card">
    <h3>Historical Snapshots</h3>
    <form method="POST" action="{{ route('admin.analytics.snapshot') }}">
        @csrf
        <button type="submit" class="btn-primary">Take a snapshot now</button>
    </form>
    <table class="data-table" style="margin-top:14px;">
        <thead><tr><th>Date</th><th>Active Farmers</th><th>Recommendations</th><th>Avg. Model Accuracy</th><th>Orders</th></tr></thead>
        <tbody>
        @foreach ($snapshots as $s)
            <tr>
                <td>{{ $s->snapshot_date }}</td><td class="mono">{{ $s->active_farmers }}</td>
                <td class="mono">{{ $s->total_recommendations }}</td><td class="mono">{{ $s->avg_model_accuracy }}%</td><td class="mono">{{ $s->total_orders }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
