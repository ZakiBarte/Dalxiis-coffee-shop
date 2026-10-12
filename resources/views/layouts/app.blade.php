<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Home') | Dalxiis Coffee Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --coffee: #4b2e1e; --coffee-dark: #36200f; --accent: #c8873c; --cream: #faf5ef; }
        body { background: var(--cream); color: #2b1d14; }
        .navbar-coffee { background: var(--coffee); }
        .navbar-coffee .navbar-brand, .navbar-coffee .nav-link { color: #f3e6d8; }
        .navbar-coffee .nav-link:hover, .navbar-coffee .nav-link.active { color: var(--accent); }
        .btn-coffee { background: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-coffee:hover { background: #a96d28; border-color: #a96d28; color: #fff; }
        .page-title { color: var(--coffee); font-weight: 700; }
        .card { border: 0; border-radius: 12px; }
        .card-header-coffee { background: var(--coffee); color: #f3e6d8; border-radius: 12px 12px 0 0 !important; }
        .hero { background: linear-gradient(135deg, var(--coffee), var(--coffee-dark)); color: #f3e6d8; border-radius: 16px; }
        .stat-number { font-size: 1.9rem; font-weight: 700; color: var(--coffee); }
        footer { color: #7a6555; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand navbar-coffee mb-4">
    <div class="container flex-wrap">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">&#9749; Dalxiis Coffee Shop</a>
        <ul class="navbar-nav flex-wrap me-auto">
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Menu</a></li>
            @auth
                @php $me = auth()->user(); @endphp
                @if ($me->isCustomer())
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('cart.*') ? 'active' : '' }}" href="{{ route('cart.index') }}">Cart ({{ collect(session('cart', []))->sum() }})</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('my-orders.*') ? 'active' : '' }}" href="{{ route('my-orders.index') }}">My Orders</a></li>
                @else
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">Orders</a></li>
                    @if ($me->canManageCatalog())
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Products</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">Categories</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">Customers</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('team.*') ? 'active' : '' }}" href="{{ route('team.index') }}">Team</a></li>
                    @endif
                    @if ($me->isAdmin())
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Users</a></li>
                    @endif
                @endif
            @endauth
        </ul>
        <ul class="navbar-nav flex-wrap align-items-center">
            @auth
                <li class="nav-item"><span class="nav-link">{{ auth()->user()->name }} <span class="badge text-bg-{{ auth()->user()->roleColor() }}">{{ ucfirst(auth()->user()->role) }}</span></span></li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
                    </form>
                </li>
            @else
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">Login</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">Register</a></li>
            @endauth
        </ul>
    </div>
</nav>

<main class="container pb-5">
    @include('partials.alerts')
    @yield('content')
</main>

<footer class="text-center py-4 small">&copy; {{ date('Y') }} Dalxiis Coffee Shop</footer>
</body>
</html>
