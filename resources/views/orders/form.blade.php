@extends('layouts.app')
@section('title', $order->exists ? 'Edit order' : 'New order')
@section('content')
@php $items = old('items', $rows); @endphp
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header card-header-coffee">{{ $order->exists ? 'Edit order #' . $order->id : 'New order' }}</div>
            <div class="card-body">
                <form method="POST" action="{{ $order->exists ? route('orders.update', $order) : route('orders.store') }}">
                    @csrf
                    @if ($order->exists) @method('PUT') @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select id="customer_id" name="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                                <option value="">Walk-in customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected(old('customer_id', $order->customer_id) == $customer->id)>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                                @foreach (['pending', 'preparing', 'completed', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="payment_method" class="form-label">Payment</label>
                            <select id="payment_method" name="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                                @foreach (['cash', 'card', 'mobile'] as $method)
                                    <option value="{{ $method }}" @selected(old('payment_method', $order->payment_method) === $method)>{{ ucfirst($method) }}</option>
                                @endforeach
                            </select>
                            @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    @unless (auth()->user()->isEmployee())
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Handled by</label>
                            <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">Unclaimed (waiting for staff)</option>
                                @foreach ($staff as $member)
                                    <option value="{{ $member->id }}" @selected(old('user_id', $order->exists ? $order->user_id : auth()->id()) == $member->id)>{{ $member->name }} ({{ $member->role }})</option>
                                @endforeach
                            </select>
                            @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endunless

                    <h2 class="h6 page-title">Items <small class="text-muted fw-normal">(fill the rows you need, leave the rest empty)</small></h2>
                    @error('items')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                    @for ($i = 0; $i < $rowCount; $i++)
                        <div class="row g-2 mb-2">
                            <div class="col-8">
                                <select name="items[{{ $i }}][product_id]" class="form-select @error('items.' . $i . '.product_id') is-invalid @enderror">
                                    <option value="">-- Product --</option>
                                    @foreach ($products as $product)
                                        @if ($product->is_available || (int) ($items[$i]['product_id'] ?? 0) === $product->id)
                                            <option value="{{ $product->id }}" @selected((int) ($items[$i]['product_id'] ?? 0) === $product->id)>
                                                {{ $product->name }} (${{ number_format($product->price, 2) }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('items.' . $i . '.product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-4">
                                <input type="number" min="1" max="50" name="items[{{ $i }}][quantity]" value="{{ $items[$i]['quantity'] ?? '' }}" placeholder="Qty" class="form-control @error('items.' . $i . '.quantity') is-invalid @enderror">
                                @error('items.' . $i . '.quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    @endfor

                    <div class="mb-3 mt-3">
                        <label for="notes" class="form-label">Notes</label>
                        <input type="text" id="notes" name="notes" value="{{ old('notes', $order->notes) }}" class="form-control @error('notes') is-invalid @enderror" placeholder="e.g. extra hot, no sugar">
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-coffee">Save order</button>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
