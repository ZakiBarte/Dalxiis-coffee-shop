@extends('layouts.app')
@section('title', 'Users')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 page-title mb-0">Users</h1>
    <a href="{{ route('users.create') }}" class="btn btn-coffee">+ New user</a>
</div>
@include('partials.search', ['placeholder' => 'Search users by name, email or role...'])
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Orders handled</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge text-bg-{{ $user->roleColor() }}">{{ ucfirst($user->role) }}</span></td>
                    <td>{{ $user->orders_count }}</td>
                    <td class="text-end"><a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection
