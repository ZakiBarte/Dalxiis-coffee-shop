@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<h1 class="h3 page-title mb-1">{{ $mine ? 'My Dashboard' : 'Dashboard' }}</h1>
<p class="text-muted mb-4">
    @if ($mine)
        You are logged in as an employee. You see the orders you are working on.
    @else
        You are logged in as {{ auth()->user()->role }}. You see everything in the shop.
    @endif
</p>

<div class="row g-3 mb-4">
    @php
        $cards = [
            [$mine ? 'My orders' : 'Orders', $stats['orders']],
            ['Open orders', $stats['open']],
            ['Today\'s sales', '$' . number_format($stats['today'], 2)],
            ['Completed revenue', '$' . number_format($stats['revenue'], 2)],
            ['Waiting to be claimed', $stats['unclaimed']],
        ];
        if (! $mine) {
            $cards[] = ['Products', $stats['products']];
            $cards[] = ['Customers', $stats['customers']];
        }
    @endphp
    @foreach ($cards as [$label, $value])
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <div class="stat-number">{{ $value }}</div>
                    <div class="text-muted small">{{ $label }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if ($unclaimedOrders->isNotEmpty())
    <div class="card shadow-sm mb-4">
        <div class="card-header card-header-coffee">Waiting for a staff member</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>#</th><th>Customer</th><th>Placed</th><th class="text-end">Total</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                @foreach ($unclaimedOrders as $order)
                    <tr>
                        <td><a href="{{ route('orders.show', $order) }}">#{{ $order->id }}</a></td>
                        <td>{{ $order->customer->name ?? 'Walk-in' }}</td>
                        <td>{{ $order->created_at->diffForHumans() }}</td>
                        <td class="text-end">${{ number_format($order->total, 2) }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('orders.claim', $order) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-coffee">Claim order</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header card-header-coffee d-flex justify-content-between">
                <span>{{ $mine ? 'My recent orders' : 'Recent orders' }}</span>
                <a href="{{ route('orders.create') }}" class="btn btn-sm btn-coffee">+ New order</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>#</th><th>Customer</th><th>Status</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('orders.show', $order) }}">#{{ $order->id }}</a></td>
                            <td>{{ $order->customer->name ?? 'Walk-in' }}</td>
                            <td><span class="badge text-bg-{{ $order->statusColor() }}">{{ ucfirst($order->status) }}</span></td>
                            <td class="text-end">${{ number_format($order->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No orders yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header card-header-coffee">{{ $mine ? 'My best sellers' : 'Best sellers' }}</div>
            <ul class="list-group list-group-flush">
                @forelse ($bestSellers as $product)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $product->name }}</span>
                        <span class="badge text-bg-secondary">{{ $product->units_sold }} sold</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No sales yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
