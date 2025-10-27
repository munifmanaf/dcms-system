@extends('layouts.app')

@section('page_title', $community->name)
@section('breadcrumb', 'Community Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ $community->name }}</h3>
                <div class="card-tools">
                    <span class="badge {{ $community->is_public ? 'badge-success' : 'badge-secondary' }}">
                        {{ $community->is_public ? 'Public' : 'Private' }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                @if($community->description)
                <div class="mb-4">
                    <h5>Description</h5>
                    <p>{{ $community->description }}</p>
                </div>
                @endif

                <div class="mb-4">
                    <h5>Collections in this Community</h5>
                    @if($community->collections->count() > 0)
                    <div class="list-group">
                        @foreach($community->collections as $collection)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $collection->name }}</h6>
                                @if($collection->description)
                                <p class="mb-1 text-muted small">{{ Str::limit($collection->description, 100) }}</p>
                                @endif
                                <small class="text-muted">
                                    {{ $collection->items_count }} items • 
                                    <span class="badge {{ $collection->is_public ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $collection->is_public ? 'Public' : 'Private' }}
                                    </span>
                                </small>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('collections.show', $collection) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('collections.edit', $collection) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No collections in this community yet.</p>
                        <a href="{{ route('collections.create') }}" class="btn btn-primary btn-sm">Create Collection</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Community Details</h3>
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt>Status</dt>
                    <dd>
                        <span class="badge {{ $community->is_public ? 'badge-success' : 'badge-secondary' }}">
                            {{ $community->is_public ? 'Public' : 'Private' }}
                        </span>
                    </dd>

                    <dt class="mt-2">Collections</dt>
                    <dd>{{ $community->collections_count }} collections</dd>

                    <dt class="mt-2">Total Items</dt>
                    <dd>{{ $community->items_count ?? 0 }} items</dd>

                    <dt class="mt-2">Created</dt>
                    <dd>{{ $community->created_at->format('M j, Y g:i A') }}</dd>

                    <dt class="mt-2">Last Updated</dt>
                    <dd>{{ $community->updated_at->format('M j, Y g:i A') }}</dd>
                </dl>
            </div>
            <div class="card-footer">
                <div class="btn-group w-100">
                    <a href="{{ route('communities.edit', $community) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('communities.index') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('collections.create') }}?community={{ $community->id }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Collection
                    </a>
                    <a href="{{ route('items.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-plus"></i> Add Item
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection