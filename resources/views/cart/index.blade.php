@extends('layouts.app')
@section('title', 'My Cart')
@section('content')
<h1 class="h3 page-title mb-3">My Cart</h1>

@if (count($lines) === 0)
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <p class="text-muted mb-3">Your cart is empty.</p>
            <a href="{{ route('home') }}" class="btn btn-coffee">Browse the menu</a>
        </div>
    </div>
@else
    <div class="card shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Product</th><th>Price</th><th style="width: 220px;">Quantity</th><th class="text-end">Subtotal</th><th class="text-end">Remove</th></tr></thead>
                <tbody>
                @foreach ($lines as $line)
                    <tr>
                        <td class="fw-semibold">{{ $line['product']->name }}</td>
                        <td>${{ number_format($line['product']->price, 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('cart.update', $line['product']) }}" class="d-flex gap-2">
                                @csrf @method('PATCH')
                                <input type="number" name="quantity" value="{{ $line['quantity'] }}" min="1" max="20" class="form-control form-control-sm" style="max-width: 80px;">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                            </form>
                        </td>
                        <td class="text-end">${{ number_format($line['subtotal'], 2) }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('cart.remove', $line['product']) }}" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot><tr><th colspan="3" class="text-end">Total</th><th class="text-end">${{ number_format($total, 2) }}</th><th></th></tr></tfoot>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header card-header-coffee">Place your order</div>
        <div class="card-body">
            <form method="POST" action="{{ route('cart.checkout') }}">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="payment_method" class="form-label">Payment method</label>
                        <select id="payment_method" name="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                            @foreach (['cash', 'card', 'mobile'] as $method)
                                <option value="{{ $method }}" @selected(old('payment_method') === $method)>{{ ucfirst($method) }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8 mb-3">
                        <label for="notes" class="form-label">Notes (optional)</label>
                        <input type="text" id="notes" name="notes" value="{{ old('notes') }}" class="form-control @error('notes') is-invalid @enderror" placeholder="e.g. extra hot, no sugar">
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-coffee">Place order (${{ number_format($total, 2) }})</button>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">Keep shopping</a>
            </form>
        </div>
    </div>
@endif
@endsection
