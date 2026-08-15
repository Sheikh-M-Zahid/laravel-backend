@extends('layouts.app')
@section('title', 'Regional Crop Trends')
@section('content')
<h2>📊 Regional Crop Trends</h2>
<div class="card">
    <table class="data-table">
        <thead><tr><th>Crop</th><th>Recommendations</th></tr></thead>
        <tbody>
        @foreach ($trends as $t)
            <tr><td>{{ $t->crop }}</td><td class="mono">{{ $t->total }}</td></tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
