@extends('layouts.app')
@section('title', $member->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 page-title mb-0">{{ $member->name }}
        <span class="badge text-bg-{{ $member->roleColor() }} fs-6">{{ ucfirst($member->role) }}</span>
    </h1>
    <div>
        <a href="{{ route('team.edit', $member) }}" class="btn btn-outline-secondary">Edit</a>
        <a href="{{ route('team.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>
<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header card-header-coffee">Staff profile</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Email:</strong> {{ $member->email }}</li>
                <li class="list-group-item"><strong>Job title:</strong> {{ $member->staffProfile->job_title ?? '-' }}</li>
                <li class="list-group-item"><strong>Phone:</strong> {{ $member->staffProfile->phone ?? '-' }}</li>
                <li class="list-group-item"><strong>Hired on:</strong> {{ optional($member->staffProfile?->hired_on)->format('d M Y') ?? '-' }}</li>
                <li class="list-group-item"><strong>Status:</strong>
                    @if ($member->staffProfile && ! $member->staffProfile->is_active)
                        <span class="badge text-bg-secondary">Inactive (cannot log in)</span>
                    @else
                        <span class="badge text-bg-success">Active</span>
                    @endif
                </li>
            </ul>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header card-header-coffee">Recent orders handled</div>
            <ul class="list-group list-group-flush">
                @forelse ($recentOrders as $order)
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="{{ route('orders.show', $order) }}">#{{ $order->id }} - {{ $order->customer->name ?? 'Walk-in' }}</a>
                        <span><span class="badge text-bg-{{ $order->statusColor() }}">{{ ucfirst($order->status) }}</span> ${{ number_format($order->total, 2) }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No orders handled yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
