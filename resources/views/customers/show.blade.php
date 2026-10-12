@extends('layouts.app')
@section('title', $customer->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 page-title mb-0">{{ $customer->name }}</h1>
    <div>
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-secondary">Edit</a>
        <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">Delete</button>
        </form>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>
<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header card-header-coffee">Details</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Phone:</strong> {{ $customer->phone ?? '-' }}</li>
                <li class="list-group-item"><strong>Email:</strong> {{ $customer->email ?? '-' }}</li>
                <li class="list-group-item"><strong>Loyalty card:</strong> {{ $customer->loyaltyCard->card_number ?? '-' }}</li>
                <li class="list-group-item"><strong>Tier:</strong> {{ ucfirst($customer->loyaltyCard->tier ?? '-') }}</li>
                <li class="list-group-item"><strong>Points:</strong> {{ $customer->points() }}</li>
            </ul>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header card-header-coffee">Order history</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>#</th><th>Date</th><th>Status</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><a href="{{ route('orders.show', $order) }}">#{{ $order->id }}</a></td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
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
</div>
@endsection
