@extends('layouts.app')
@section('title', $category->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 page-title mb-0">{{ $category->name }}</h1>
    <div>
        <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-secondary">Edit</a>
        <form method="POST" action="{{ route('categories.destroy', $category) }}" class="d-inline">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">Delete</button>
        </form>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>
<p class="text-muted">{{ $category->description ?? 'No description.' }}</p>
<div class="card shadow-sm">
    <div class="card-header card-header-coffee">Products in this category</div>
    <ul class="list-group list-group-flush">
        @forelse ($category->products as $product)
            <li class="list-group-item d-flex justify-content-between">
                <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                <span>${{ number_format($product->price, 2) }}</span>
            </li>
        @empty
            <li class="list-group-item text-muted">No products yet.</li>
        @endforelse
    </ul>
</div>
@endsection
