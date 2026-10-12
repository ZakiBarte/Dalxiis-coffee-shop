@extends('layouts.app')
@section('title', $category->exists ? 'Edit category' : 'New category')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header card-header-coffee">{{ $category->exists ? 'Edit category' : 'New category' }}</div>
            <div class="card-body">
                <form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
                    @csrf
                    @if ($category->exists) @method('PUT') @endif
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" id="description" name="description" value="{{ old('description', $category->description) }}" class="form-control @error('description') is-invalid @enderror">
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-coffee">Save</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
