@extends('layouts.app')

@section('page_title', 'Communities')
@section('breadcrumb', 'Communities')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Communities</h3>
        <div class="card-tools">
            <a href="{{ route('communities.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Community
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($communities as $community)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card card-outline card-primary h-100">
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
                        <p class="card-text">{{ Str::limit($community->description, 100) }}</p>
                        @else
                        <p class="card-text text-muted">No description</p>
                        @endif
                        
                        <div class="mt-3">
                            <div class="row text-center">
                                <div class="col-6">
                                    <small class="text-muted">Collections</small>
                                    <div class="h5 mb-0">{{ $community->collections_count }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Items</small>
                                    <div class="h5 mb-0">{{ $community->items_count ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group btn-group-sm w-100">
                            <a href="{{ route('communities.show', $community) }}" class="btn btn-default" title="View">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('communities.edit', $community) }}" class="btn btn-default" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('communities.destroy', $community) }}" method="POST" class="d-inline">
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

        @if($communities->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
            <h4>No communities found</h4>
            <p class="text-muted">Create communities to organize your collections and items.</p>
            <a href="{{ route('communities.create') }}" class="btn btn-primary">Create Community</a>
        </div>
        @endif
    </div>
</div>
@endsection