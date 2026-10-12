@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 page-title mb-0">{{ $product->name }}</h1>
    <div>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-secondary">Edit</a>
        <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">Delete</button>
        </form>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header card-header-coffee">Details</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Category:</strong> <a href="{{ route('categories.show', $product->category) }}">{{ $product->category->name }}</a></li>
                <li class="list-group-item"><strong>Description:</strong> {{ $product->description ?? '-' }}</li>
                <li class="list-group-item"><strong>Price:</strong> ${{ number_format($product->price, 2) }}</li>
                <li class="list-group-item"><strong>Status:</strong>
                    @if ($product->is_available)
                        <span class="badge text-bg-success">Available</span>
                    @else
                        <span class="badge text-bg-secondary">Unavailable</span>
                    @endif
                </li>
                <li class="list-group-item"><strong>Units sold:</strong> {{ $unitsSold }}</li>
            </ul>
        </div>
    </div>
</div>
@endsection
