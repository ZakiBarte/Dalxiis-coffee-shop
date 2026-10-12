@extends('layouts.app')
@section('title', $member->exists ? 'Edit staff member' : 'New staff member')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header card-header-coffee">{{ $member->exists ? 'Edit ' . $member->name : 'New staff member' }}</div>
            <div class="card-body">
                <form method="POST" action="{{ $member->exists ? route('team.update', $member) : route('team.store') }}">
                    @csrf
                    @if ($member->exists) @method('PUT') @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $member->name) }}" class="form-control @error('name') is-invalid @enderror">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email (used to log in)</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $member->email) }}" class="form-control @error('email') is-invalid @enderror">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select id="role" name="role" class="form-select @error('role') is-invalid @enderror">
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}" @selected(old('role', $member->role) === $role)>{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="job_title" class="form-label">Job title</label>
                            <input type="text" id="job_title" name="job_title" value="{{ old('job_title', $member->staffProfile->job_title ?? '') }}" class="form-control @error('job_title') is-invalid @enderror" placeholder="e.g. Barista, Cashier">
                            @error('job_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $member->staffProfile->phone ?? '') }}" class="form-control @error('phone') is-invalid @enderror">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hired_on" class="form-label">Hired on</label>
                            <input type="date" id="hired_on" name="hired_on" value="{{ old('hired_on', optional($member->staffProfile?->hired_on)->format('Y-m-d')) }}" class="form-control @error('hired_on') is-invalid @enderror">
                            @error('hired_on')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password {{ $member->exists ? '(leave empty to keep)' : '(min 8 characters)' }}</label>
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $member->staffProfile->is_active ?? true))>
                        <label class="form-check-label" for="is_active">Active (inactive staff cannot log in)</label>
                    </div>

                    <button type="submit" class="btn btn-coffee">Save</button>
                    <a href="{{ $member->exists ? route('team.show', $member) : route('team.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
