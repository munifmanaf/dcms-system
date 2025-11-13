@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Advanced Filters</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('items.search') }}">
                        <!-- Keyword Search -->
                        <div class="form-group">
                            <label for="q">Keyword Search</label>
                            <input type="text" name="q" id="q" class="form-control" 
                                   value="{{ request('q') }}" placeholder="Search titles, descriptions...">
                        </div>
                        
                        <!-- Collection Filter -->
                        <div class="form-group">
                            <label for="collection">Collection</label>
                            <select name="collection" id="collection" class="form-control">
                                <option value="">All Collections</option>
                                @foreach($collections as $collection)
                                <option value="{{ $collection->id }}" 
                                        {{ request('collection') == $collection->id ? 'selected' : '' }}>
                                    {{ $collection->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- File Type Filter -->
                        <div class="form-group">
                            <label for="file_type">File Type</label>
                            <input type="text" name="file_type" id="file_type" class="form-control"
                                   value="{{ request('file_type') }}" placeholder="e.g., PDF, Image, Video">
                        </div>
                        
                        <!-- Sort Options -->
                        <div class="form-group">
                            <label for="sort">Sort By</label>
                            <select name="sort" id="sort" class="form-control">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title A-Z</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                        <a href="{{ route('items.search') }}" class="btn btn-default btn-block">Reset</a>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Search Results 
                        @if(request()->anyFilled(['q', 'collection', 'file_type']))
                        <small class="text-muted">({{ $items->total() }} items found)</small>
                        @endif
                    </h3>
                </div>
                <div class="card-body">
                    @if($items->count() > 0)
                        <div class="row">
                            @foreach($items as $item)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ Str::limit($item->title, 50) }}</h6>
                                        <p class="card-text small text-muted">
                                            {{ Str::limit($item->description, 80) }}
                                        </p>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">{{ $item->file_type ?? 'File' }}</small>
                                            <small class="text-muted">{{ $item->created_at->format('M d, Y') }}</small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('items.show', $item->id) }}" class="btn btn-sm btn-primary">View</a>
                                        <small class="float-right">
                                            <i class="fas fa-download"></i> {{ $item->download_count }}
                                            <i class="fas fa-eye ml-2"></i> {{ $item->view_count }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $items->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4>No items found</h4>
                            <p class="text-muted">Try adjusting your search criteria</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection