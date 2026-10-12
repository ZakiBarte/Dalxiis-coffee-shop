@extends('layouts.app')
@section('title', 'Team')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 page-title mb-0">Team</h1>
        <small class="text-muted">
            @if (auth()->user()->isAdmin())
                You can add and change managers and employees.
            @else
                You can add and change employees.
            @endif
        </small>
    </div>
    <a href="{{ route('team.create') }}" class="btn btn-coffee">+ New {{ auth()->user()->isAdmin() ? 'staff member' : 'employee' }}</a>
</div>
@include('partials.search', ['placeholder' => 'Search staff by name, email, role, job title or phone...'])
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Name</th><th>Role</th><th>Job title</th><th>Phone</th><th>Status</th><th>Orders</th><th class="text-end">Details</th></tr></thead>
            <tbody>
            @forelse ($members as $member)
                <tr>
                    <td class="fw-semibold">{{ $member->name }}<br><small class="text-muted">{{ $member->email }}</small></td>
                    <td><span class="badge text-bg-{{ $member->roleColor() }}">{{ ucfirst($member->role) }}</span></td>
                    <td>{{ $member->staffProfile->job_title ?? '-' }}</td>
                    <td>{{ $member->staffProfile->phone ?? '-' }}</td>
                    <td>
                        @if ($member->staffProfile && ! $member->staffProfile->is_active)
                            <span class="badge text-bg-secondary">Inactive</span>
                        @else
                            <span class="badge text-bg-success">Active</span>
                        @endif
                    </td>
                    <td>{{ $member->orders_count }}</td>
                    <td class="text-end"><a href="{{ route('team.show', $member) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No staff yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $members->links() }}</div>
@endsection
