@extends('layouts.app')
@section('title', 'Customers')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 page-title mb-0">Customers</h1>
    <a href="{{ route('customers.create') }}" class="btn btn-coffee">+ New customer</a>
</div>
@include('partials.search', ['placeholder' => 'Search customers by name, phone, email or card number...'])
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Name</th><th>Phone</th><th>Card</th><th>Tier</th><th>Orders</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td class="fw-semibold"><a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a></td>
                    <td>{{ $customer->phone ?? '-' }}</td>
                    <td>{{ $customer->loyaltyCard->card_number ?? '-' }}</td>
                    <td>{{ ucfirst($customer->loyaltyCard->tier ?? '-') }}</td>
                    <td>{{ $customer->orders_count }}</td>
                    <td class="text-end">
                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No customers yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $customers->links() }}</div>
@endsection
