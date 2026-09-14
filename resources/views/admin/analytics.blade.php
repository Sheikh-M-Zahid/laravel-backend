@extends('layouts.app')
@section('title', 'Platform Analytics')
@section('content')
<h2>📊 Platform Analytics</h2>

<div class="grid-2" style="align-items:start;">
    <div class="card">
        <h3>Crop Recommendation Distribution</h3>
        <canvas id="cropChart" height="220"></canvas>
    </div>
    <div class="card">
        <h3>Trend (last {{ $snapshots->count() }} snapshots)</h3>
        <canvas id="trendChart" height="220"></canvas>
    </div>
</div>

<div class="card">
    <h3>Crop Recommendation Distribution — table</h3>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('cropChart'), {
    type: 'bar',
    data: {
        labels: @json($cropCounts->pluck('crop')),
        datasets: [{ label: 'Recommendations', data: @json($cropCounts->pluck('total')), backgroundColor: '#4C7A3E' }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: @json($snapshots->reverse()->values()->pluck('snapshot_date')),
        datasets: [
            { label: 'Orders', data: @json($snapshots->reverse()->values()->pluck('total_orders')), borderColor: '#B98A2E', tension: 0.3 },
            { label: 'Recommendations', data: @json($snapshots->reverse()->values()->pluck('total_recommendations')), borderColor: '#4C7A3E', tension: 0.3 }
        ]
    },
    options: { responsive: true }
});
</script>
@endsection
