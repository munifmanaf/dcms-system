@extends('layouts.app')

@section('page_title', $collection->name)
@section('breadcrumb', 'Collection Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ $collection->name }}</h3>
                <div class="card-tools">
                    <span class="badge {{ $collection->is_public ? 'badge-success' : 'badge-secondary' }}">
                        {{ $collection->is_public ? 'Public' : 'Private' }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h5>Community</h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-layer-group text-primary mr-2"></i>
                        <a href="{{ route('communities.show', $collection->community) }}">
                            {{ $collection->community->name }}
                        </a>
                    </div>
                </div>

                @if($collection->description)
                <div class="mb-4">
                    <h5>Description</h5>
                    <p>{{ $collection->description }}</p>
                </div>
                @endif

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Items in this Collection</h5>
                        <a href="{{ route('items.create') }}?collection={{ $collection->id }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Item
                        </a>
                    </div>
                    
                    @if($collection->items->count() > 0)
                    <div class="list-group">
                        @foreach($collection->items as $item)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $item->title }}</h6>
                                @if($item->file_name)
                                <small class="text-muted">
                                    <i class="fas fa-file"></i> {{ $item->file_name }}
                                </small>
                                @endif
                                <div class="mt-1">
                                    @foreach($item->categories as $category)
                                    <span class="badge badge-secondary badge-sm">{{ $category->name }}</span>
                                    @endforeach
                                    <span class="badge {{ $item->is_published ? 'badge-success' : 'badge-warning' }} badge-sm">
                                        {{ $item->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </div>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No items in this collection yet.</p>
                        <a href="{{ route('items.create') }}?collection={{ $collection->id }}" class="btn btn-primary btn-sm">Add First Item</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Collection Details</h3>
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt>Status</dt>
                    <dd>
                        <span class="badge {{ $collection->is_public ? 'badge-success' : 'badge-secondary' }}">
                            {{ $collection->is_public ? 'Public' : 'Private' }}
                        </span>
                    </dd>

                    <dt class="mt-2">Community</dt>
                    <dd>
                        <a href="{{ route('communities.show', $collection->community) }}">
                            {{ $collection->community->name }}
                        </a>
                    </dd>

                    <dt class="mt-2">Total Items</dt>
                    <dd>{{ $collection->items_count }} items</dd>

                    <dt class="mt-2">Created</dt>
                    <dd>{{ $collection->created_at->format('M j, Y g:i A') }}</dd>

                    <dt class="mt-2">Last Updated</dt>
                    <dd>{{ $collection->updated_at->format('M j, Y g:i A') }}</dd>
                </dl>
            </div>
            <div class="card-footer">
                <div class="btn-group w-100">
                    <a href="{{ route('collections.edit', $collection) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('collections.index') }}" class="btn btn-default">
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
                    <a href="{{ route('items.create') }}?collection={{ $collection->id }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Item
                    </a>
                    <a href="{{ route('communities.show', $collection->community) }}" class="btn btn-outline-primary">
                        <i class="fas fa-layer-group"></i> View Community
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection