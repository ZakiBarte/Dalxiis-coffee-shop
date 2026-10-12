@extends('layouts.app')
@section('title', $user->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 page-title mb-0">{{ $user->name }}
        <span class="badge text-bg-{{ $user->roleColor() }} fs-6">{{ ucfirst($user->role) }}</span>
    </h1>
    <div>
        <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-secondary">Edit</a>
        @if ($user->id !== auth()->id())
            <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Delete</button>
            </form>
        @endif
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>
<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header card-header-coffee">Account</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Email:</strong> {{ $user->email }}</li>
                <li class="list-group-item"><strong>Role:</strong> {{ ucfirst($user->role) }}</li>
                <li class="list-group-item"><strong>Joined:</strong> {{ $user->created_at->format('d M Y') }}</li>
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
                    <li class="list-group-item text-muted">No orders handled.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
