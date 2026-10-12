@extends('layouts.app')
@section('title', 'Menu')
@section('content')
<div class="hero p-5 mb-4 text-center">
    <h1 class="display-5 fw-bold">Dalxiis Coffee Shop</h1>
    <p class="lead mb-3">Freshly roasted coffee, baked daily.</p>
    @guest
        <a href="{{ route('login') }}" class="btn btn-coffee me-2">Login</a>
        <a href="{{ route('register') }}" class="btn btn-outline-light">Create an account to order</a>
    @else
        @if (auth()->user()->isCustomer())
            <a href="{{ route('cart.index') }}" class="btn btn-coffee me-2">View my cart</a>
            <a href="{{ route('my-orders.index') }}" class="btn btn-outline-light">My orders</a>
        @else
            <a href="{{ route('dashboard') }}" class="btn btn-coffee">Go to Dashboard</a>
        @endif
    @endguest
</div>

<h2 class="h3 page-title mb-3">Our Menu</h2>

{{-- Search --}}
<form method="GET" action="{{ route('home') }}" class="row g-2 mb-3">
    @if ($categoryId)
        <input type="hidden" name="category" value="{{ $categoryId }}">
    @endif
    <div class="col-md-8">
        <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Search drinks and food, e.g. latte, croissant...">
    </div>
    <div class="col-md-4">
        <button type="submit" class="btn btn-coffee">Search</button>
        @if ($search !== '' || $categoryId)
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">Clear</a>
        @endif
    </div>
</form>

{{-- Category buttons --}}
<div class="mb-4">
    <a href="{{ route('home', ['q' => $search ?: null]) }}" class="btn btn-sm {{ $categoryId ? 'btn-outline-secondary' : 'btn-coffee' }} mb-1">All</a>
    @foreach ($categories as $category)
        <a href="{{ route('home', ['category' => $category->id, 'q' => $search ?: null]) }}"
           class="btn btn-sm {{ (string) $categoryId === (string) $category->id ? 'btn-coffee' : 'btn-outline-secondary' }} mb-1">{{ $category->name }}</a>
    @endforeach
</div>

@guest
    <div class="alert alert-info">Please <a href="{{ route('login') }}">login</a> or <a href="{{ route('register') }}">register</a> to add items to your cart and place an order.</div>
@endguest

@forelse ($products as $categoryName => $items)
    <div class="card shadow-sm mb-4">
        <div class="card-header card-header-coffee fw-semibold">{{ $categoryName }}</div>
        <ul class="list-group list-group-flush">
            @foreach ($items as $product)
                <li class="list-group-item">
                    <div class="row align-items-center g-2">
                        <div class="col-md-6">
                            <strong>{{ $product->name }}</strong>
                            @if ($product->description)<br><small class="text-muted">{{ $product->description }}</small>@endif
                        </div>
                        <div class="col-md-2 fw-bold">${{ number_format($product->price, 2) }}</div>
                        <div class="col-md-4">
                            @auth
                                @if (auth()->user()->isCustomer())
                                    <form method="POST" action="{{ route('cart.add', $product) }}" class="d-flex gap-2">
                                        @csrf
                                        <input type="number" name="quantity" value="1" min="1" max="20" class="form-control form-control-sm" style="max-width: 80px;" aria-label="Quantity">
                                        <button type="submit" class="btn btn-sm btn-coffee">Add to cart</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@empty
    <div class="alert alert-warning">Nothing found. Try another search or <a href="{{ route('home') }}">show the full menu</a>.</div>
@endforelse
@endsection
