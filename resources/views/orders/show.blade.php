@extends('layouts.app')
@section('title', 'Order #' . $order->id)
@section('content')
@php
    $me = auth()->user();
    $canEdit = ! $me->isEmployee() || ($order->user_id !== null && (int) $order->user_id === (int) $me->id);
@endphp
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 page-title mb-0">Order #{{ $order->id }}
        <span class="badge text-bg-{{ $order->statusColor() }} fs-6">{{ ucfirst($order->status) }}</span>
    </h1>
    <div>
        @if (! $order->user_id)
            <form method="POST" action="{{ route('orders.claim', $order) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-coffee">Claim order</button>
            </form>
        @endif
        @if ($canEdit)
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-secondary">Edit</a>
        @endif
        @unless ($me->isEmployee())
            <form method="POST" action="{{ route('orders.destroy', $order) }}" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Delete</button>
            </form>
        @endunless
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header card-header-coffee">Order info</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Customer:</strong>
                    @if ($order->customer)
                        @if ($me->canManageCatalog())
                            <a href="{{ route('customers.show', $order->customer) }}">{{ $order->customer->name }}</a>
                        @else
                            {{ $order->customer->name }}
                        @endif
                    @else
                        Walk-in
                    @endif
                </li>
                @if ($order->customer && $order->customer->loyaltyCard)
                    <li class="list-group-item"><strong>Card:</strong> {{ $order->customer->loyaltyCard->card_number }} ({{ ucfirst($order->customer->loyaltyCard->tier) }})</li>
                @endif
                <li class="list-group-item"><strong>Handled by:</strong> {{ $order->user->name ?? 'Unclaimed' }}</li>
                <li class="list-group-item"><strong>Payment:</strong> {{ ucfirst($order->payment_method) }}</li>
                <li class="list-group-item"><strong>Date:</strong> {{ $order->created_at->format('d M Y H:i') }}</li>
                <li class="list-group-item"><strong>Notes:</strong> {{ $order->notes ?? '-' }}</li>
            </ul>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header card-header-coffee">Items</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Product</th><th>Qty</th><th>Unit price</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody>
                    @foreach ($order->products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->pivot->quantity }}</td>
                            <td>${{ number_format($product->pivot->unit_price, 2) }}</td>
                            <td class="text-end">${{ number_format($product->pivot->quantity * $product->pivot->unit_price, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr><th colspan="3" class="text-end">Total</th><th class="text-end">${{ number_format($order->total, 2) }}</th></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
