@extends('layouts.app')
@section('title', 'Orders')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 page-title mb-0">Orders</h1>
    <a href="{{ route('orders.create') }}" class="btn btn-coffee">+ New order</a>
</div>
@include('partials.search', ['placeholder' => 'Search orders by number, customer, staff, status, payment or notes...'])
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>#</th><th>Customer</th><th>Taken by</th><th>Date</th><th>Status</th><th class="text-end">Total</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td><a href="{{ route('orders.show', $order) }}">#{{ $order->id }}</a></td>
                    <td>{{ $order->customer->name ?? 'Walk-in' }}</td>
                    <td>{{ $order->user->name ?? '' }}@if (! $order->user_id)<span class="badge text-bg-warning">Unclaimed</span>@endif</td>
                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td><span class="badge text-bg-{{ $order->statusColor() }}">{{ ucfirst($order->status) }}</span></td>
                    <td class="text-end">${{ number_format($order->total, 2) }}</td>
                    <td class="text-end">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                        @if (! $order->user_id)
                            <form method="POST" action="{{ route('orders.claim', $order) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-coffee">Claim</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No orders yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $orders->links() }}</div>
@endsection
