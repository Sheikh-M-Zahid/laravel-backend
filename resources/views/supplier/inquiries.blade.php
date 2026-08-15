@extends('layouts.app')
@section('title', 'Farmer Inquiries — Supplier')
@section('content')
<p><a href="{{ route('supplier.dashboard') }}">← Back to dashboard</a></p>
<h2>💬 Farmer Inquiries</h2>

<div class="card">
    @forelse ($inquiries as $inq)
        <div class="profile-row" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
            <div>
                <strong>{{ $inq->farmer->name }}:</strong> {{ $inq->message }}
                @if ($inq->response)
                    <p class="muted">Your reply: {{ $inq->response }}</p>
                @endif
            </div>
            @unless ($inq->response)
                <button type="button" class="btn-small" onclick="openModal('modal-inquiry-{{ $inq->id }}')">Reply</button>
            @endunless
        </div>
        @unless ($inq->response)
            <div class="modal-overlay" id="modal-inquiry-{{ $inq->id }}">
                <div class="modal-box">
                    <button type="button" class="modal-close" onclick="closeModal('modal-inquiry-{{ $inq->id }}')">&times;</button>
                    <h3>Reply to {{ $inq->farmer->name }}</h3>
                    <p class="muted">{{ $inq->message }}</p>
                    <form method="POST" action="{{ route('supplier.inquiries.respond', $inq->id) }}">
                        @csrf
                        <textarea name="response" rows="3" placeholder="Write a reply..." required></textarea>
                        <button type="submit" class="btn-primary btn-block" style="margin-top:14px;">Send reply</button>
                    </form>
                </div>
            </div>
        @endunless
    @empty
        <p class="muted">No inquiries yet.</p>
    @endforelse
</div>
@endsection
