@extends('layouts.app')

@section('page_title', 'Collections')
@section('breadcrumb', 'Collections')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Collections</h3>
        <div class="card-tools">
            <a href="{{ route('collections.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Collection
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($collections as $collection)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card card-outline card-info h-100">
                    <div class="card-header">
                        <h3 class="card-title">{{ $collection->name }}</h3>
                        <div class="card-tools">
                            <span class="badge {{ $collection->is_public ? 'badge-success' : 'badge-secondary' }}">
                                {{ $collection->is_public ? 'Public' : 'Private' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="fas fa-layer-group"></i> {{ $collection->community->name }}
                            </small>
                        </div>
                        
                        @if($collection->description)
                        <p class="card-text">{{ Str::limit($collection->description, 100) }}</p>
                        @else
                        <p class="card-text text-muted">No description</p>
                        @endif
                        
                        <div class="mt-3">
                            <div class="row text-center">
                                <div class="col-12">
                                    <small class="text-muted">Items</small>
                                    <div class="h5 mb-0">{{ $collection->items_count }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group btn-group-sm w-100">
                            <a href="{{ route('collections.show', $collection) }}" class="btn btn-default" title="View">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('collections.edit', $collection) }}" class="btn btn-default" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('collections.destroy', $collection) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-default" title="Delete" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($collections->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-folder fa-3x text-muted mb-3"></i>
            <h4>No collections found</h4>
            <p class="text-muted">Create collections to organize your items within communities.</p>
            <a href="{{ route('collections.create') }}" class="btn btn-primary">Create Collection</a>
        </div>
        @endif
    </div>
</div>
@endsection