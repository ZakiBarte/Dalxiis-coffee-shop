@extends('layouts.app')
@section('title', 'My Orders')
@section('content')
<h1 class="h3 page-title mb-3">My Orders</h1>

<div class="card shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between">
        <div><span class="text-muted small">Loyalty card</span><br><strong>{{ $customer->loyaltyCard->card_number ?? '-' }}</strong></div>
        <div><span class="text-muted small">Tier</span><br><strong>{{ ucfirst($customer->loyaltyCard->tier ?? '-') }}</strong></div>
        <div><span class="text-muted small">Points</span><br><strong>{{ $customer->points() }}</strong></div>
        <div class="align-self-center"><a href="{{ route('home') }}" class="btn btn-coffee">Order something</a></div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>#</th><th>Date</th><th>Status</th><th class="text-end">Total</th><th class="text-end">Details</th></tr></thead>
            <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td><span class="badge text-bg-{{ $order->statusColor() }}">{{ ucfirst($order->status) }}</span></td>
                    <td class="text-end">${{ number_format($order->total, 2) }}</td>
                    <td class="text-end"><a href="{{ route('my-orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">You have no orders yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $orders->links() }}</div>
@endsection
