<!-- resources/views/repository/browse.blade.php -->
@extends('layouts.app')

@section('title', 'Browse Repository - ' . $repository->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Results</h3>
                </div>
                <div class="card-body">
                    <form id="filter-form">
                        <h6>By Community</h6>
                        @foreach($communities as $community)
                        <div class="form-check">
                            <input class="form-check-input community-filter" type="checkbox" 
                                   name="community_id[]" value="{{ $community->id }}" 
                                   id="community{{ $community->id }}"
                                   {{ in_array($community->id, (array)request('community_id', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="community{{ $community->id }}">
                                {{ $community->name }} ({{ $community->items_count }})
                            </label>
                        </div>
                        @endforeach

                        <hr>
                        
                        <h6>By Collection</h6>
                        @foreach($collections as $collection)
                        <div class="form-check">
                            <input class="form-check-input collection-filter" type="checkbox" 
                                   name="collection_id[]" value="{{ $collection->id }}"
                                   id="collection{{ $collection->id }}"
                                   {{ in_array($collection->id, (array)request('collection_id', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="collection{{ $collection->id }}">
                                {{ $collection->name }} ({{ $collection->items_count }})
                            </label>
                        </div>
                        @endforeach

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                            <a href="{{ route('repository.browse') }}" class="btn btn-default btn-sm btn-block">
                                <i class="fas fa-times"></i> Clear All
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-search"></i> 
                        @if(request('q'))
                            Search Results for "{{ request('q') }}"
                        @else
                            Browse All Items
                        @endif
                        <span class="badge bg-primary ml-2">{{ $items->total() }} items found</span>
                    </h3>
                    <div class="card-tools">
                        <form action="{{ route('repository.browse') }}" method="GET" class="form-inline">
                            <div class="input-group input-group-sm">
                                <input type="text" name="q" class="form-control" placeholder="Search..." 
                                       value="{{ request('q') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($items->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Authors</th>
                                    <th>Collection</th>
                                    <th>Date</th>
                                    <th>Downloads</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('items.show', $item->id) }}" class="font-weight-bold">
                                            {{ $item->title }}
                                        </a>
                                        @if($item->metadata['dc_description'][0] ?? false)
                                        <br>
                                        <small class="text-muted">
                                            {{ Str::limit($item->metadata['dc_description'][0], 100) }}
                                        </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->metadata['dc_creator'] ?? false)
                                            {{ implode(', ', array_slice($item->metadata['dc_creator'], 0, 2)) }}
                                            @if(count($item->metadata['dc_creator']) > 2)
                                                <span class="text-muted">+{{ count($item->metadata['dc_creator']) - 2 }} more</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $item->collection->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $item->created_at->format('M d, Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $item->download_count }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No items found</h4>
                        <p class="text-muted">Try adjusting your search criteria or filters</p>
                        <a href="{{ route('repository.browse') }}" class="btn btn-primary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                    @endif
                </div>

                @if($items->hasPages())
                <div class="card-footer">
                    <div class="float-right">
                        {{ $items->appends(request()->query())->links() }}
                    </div>
                    <div class="float-left">
                        <small class="text-muted">
                            Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} items
                        </small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function() {
    $('#filter-form').on('submit', function(e) {
        // Keep existing search query
        @if(request('q'))
        $('<input>').attr({
            type: 'hidden',
            name: 'q',
            value: '{{ request('q') }}'
        }).appendTo('#filter-form');
        @endif
    });
});
</script>
@endsection