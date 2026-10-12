<form method="GET" action="{{ url()->current() }}" class="row g-2 mb-3">
    <div class="col-md-8">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ $placeholder ?? 'Search...' }}" aria-label="Search">
    </div>
    <div class="col-md-4">
        <button type="submit" class="btn btn-coffee">Search</button>
        @if (request()->filled('q'))
            <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Clear</a>
        @endif
    </div>
</form>
