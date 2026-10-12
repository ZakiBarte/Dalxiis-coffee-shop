@extends('layouts.app')
@section('title', $customer->exists ? 'Edit customer' : 'New customer')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header card-header-coffee">{{ $customer->exists ? 'Edit customer' : 'New customer' }}</div>
            <div class="card-body">
                <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
                    @csrf
                    @if ($customer->exists) @method('PUT') @endif
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="tier" class="form-label">Loyalty card tier</label>
                        <select id="tier" name="tier" class="form-select @error('tier') is-invalid @enderror">
                            @foreach (['bronze', 'silver', 'gold'] as $tier)
                                <option value="{{ $tier }}" @selected(old('tier', $customer->loyaltyCard->tier ?? 'bronze') === $tier)>{{ ucfirst($tier) }}</option>
                            @endforeach
                        </select>
                        @error('tier')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-coffee">Save</button>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
