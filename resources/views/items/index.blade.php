{{-- @extends('layouts.app')

@section('page_title', 'Items')
@section('breadcrumb', 'Items')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Items</h3>
        <div class="card-tools">
            <a href="{{ route('items.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Item
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Search and Filters -->
        <div class="row mb-4">
            <div class="col-md-12">
                <form action="{{ route('items.index') }}" method="GET" class="form-inline">
                    <div class="form-group mr-2">
                        <input type="text" name="search" class="form-control" placeholder="Search items..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="form-group mr-2">
                        <select name="category" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select name="collection" class="form-control">
                            <option value="">All Collections</option>
                            @foreach($collections as $collection)
                            <option value="{{ $collection->id }}" {{ request('collection') == $collection->id ? 'selected' : '' }}>
                                {{ $collection->name }} ({{ $collection->community->name }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('items.index') }}" class="btn btn-default">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </form>
            </div>
        </div>

        <!-- Items Grid -->
        <div class="row">
            @foreach($items as $item)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card document-card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ Str::limit($item->title, 50) }}</h5>
                    </div>
                    <div class="card-body">
                        <!-- Item Type Display -->
                        <div class="mb-2">
                            @if($item->file_type)
                                @if(str_contains($item->file_type, 'image'))
                                <span class="badge badge-info">
                                    <i class="fas fa-image mr-1"></i>Image
                                </span>
                                @elseif(str_contains($item->file_type, 'pdf'))
                                <span class="badge badge-danger">
                                    <i class="fas fa-file-pdf mr-1"></i>PDF
                                </span>
                                @elseif(str_contains($item->file_type, 'word') || str_contains($item->file_type, 'document'))
                                <span class="badge badge-primary">
                                    <i class="fas fa-file-word mr-1"></i>Document
                                </span>
                                @elseif(str_contains($item->file_type, 'excel') || str_contains($item->file_type, 'spreadsheet'))
                                <span class="badge badge-success">
                                    <i class="fas fa-file-excel mr-1"></i>Spreadsheet
                                </span>
                                @elseif(str_contains($item->file_type, 'video'))
                                <span class="badge badge-warning">
                                    <i class="fas fa-file-video mr-1"></i>Video
                                </span>
                                @elseif(str_contains($item->file_type, 'audio'))
                                <span class="badge badge-secondary">
                                    <i class="fas fa-file-audio mr-1"></i>Audio
                                </span>
                                @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-file mr-1"></i>File
                                </span>
                                @endif
                            @else
                                <span class="badge badge-light border">
                                    <i class="fas fa-sticky-note mr-1"></i>Text Content
                                </span>
                            @endif
                        </div>
                        @if($item->file_path)
                            @if(Storage::disk('public')->exists($item->file_path))
                                <a href="{{ route('items.download', $item) }}" class="btn btn-sm btn-info" title="Download">
                                    <i class="fas fa-download"></i> Download
                                </a>
                                <!-- Optional: Display file if it's an image -->
                                @if(str_contains($item->file_type, 'image'))
                                    <div class="mt-2">
                                        <img src="{{ Storage::disk('public')->url($item->file_path) }}" 
                                            alt="{{ $item->title }}" 
                                            style="max-height: 150px; max-width: 200px;"
                                            class="img-thumbnail">
                                    </div>
                                @endif
                                <small class="text-muted">
                                    {{ $item->file_name }} ({{ number_format($item->file_size / 1024, 1) }} KB)
                                </small>
                            @else
                                <span class="text-muted">File missing</span>
                            @endif
                        @else
                            <span class="text-muted">No file attached</span>
                        @endif
                        <p class="card-text text-muted small">
                            @if($item->file_name)
                            <i class="fas fa-file mr-1"></i> {{ Str::limit($item->file_name, 30) }}
                            @else
                            <i class="fas fa-sticky-note mr-1"></i> Text Content
                            @endif
                        </p>
                        <div class="mb-2">
                            <small class="text-muted">
                                <strong>Collection:</strong> {{ $item->collection->name }}<br>
                                <strong>Community:</strong> {{ $item->collection->community->name }}
                            </small>
                        </div>
                        <div class="mb-3">
                            @foreach($item->categories as $category)
                            <span class="badge badge-secondary mb-1">{{ $category->name }}</span>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge {{ $item->is_published ? 'badge-success' : 'badge-warning' }}">
                                {{ $item->is_published ? 'Published' : 'Draft' }}
                            </span>
                            <small class="text-muted">{{ $item->created_at->format('M j, Y') }}</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group btn-group-sm w-100">
                            <a href="{{ route('items.show', $item) }}" class="btn btn-default" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('items.edit', $item) }}" class="btn btn-default" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('workflow.show', $item) }}" class="btn btn-default" title="Workflow">
                                <i class="fas fa-tasks"></i>
                            </a>
                            <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline">
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

        @if($items->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
            <h4>No items found</h4>
            <p class="text-muted">Get started by creating your first item.</p>
            <a href="{{ route('items.create') }}" class="btn btn-primary">Create Item</a>
        </div>
        @endif

        <!-- Pagination -->
        @if($items->hasPages())
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-center">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection --}}

@extends('layouts.app')

@section('page_title', 'Items')
@section('breadcrumb', 'Items')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Items</h3>
        <div class="card-tools">
            <a href="{{ route('items.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Item
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Search and Filters - Improved Layout -->
        <div class="row mb-4">
            <div class="col-md-12">
                <form action="{{ route('items.index') }}" method="GET" class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search items..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="category" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="collection" class="form-control">
                            <option value="">All Collections</option>
                            @foreach($collections as $collection)
                            <option value="{{ $collection->id }}" {{ request('collection') == $collection->id ? 'selected' : '' }}>
                                {{ $collection->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Summary -->
        @if(request()->hasAny(['search', 'category', 'collection', 'status']))
        <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
            <strong>{{ $items->total() }}</strong> items found
            @if(request('search')) matching "<strong>{{ request('search') }}</strong>"@endif
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <!-- Items Grid -->
        <div class="row">
            @foreach($items as $item)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card document-card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <h6 class="card-title mb-0 font-weight-bold text-truncate" title="{{ $item->title }}">
                            {{ Str::limit($item->title, 40) }}
                        </h6>
                        <span class="badge {{ $item->is_published ? 'badge-success' : 'badge-warning' }} badge-sm">
                            {{ $item->is_published ? 'Live' : 'Draft' }}
                        </span>
                    </div>
                    
                    <div class="card-body py-3">
                        <!-- File Type & Quick Actions -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                @include('partials.file-type-badge', ['fileType' => $item->file_type])
                            </div>
                            <div class="btn-group">
                                @if($item->file_path && Storage::disk('public')->exists($item->file_path))
                                <a href="{{ route('items.download', $item) }}" class="btn btn-sm btn-outline-primary" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                @if(str_contains($item->file_type, 'image') || str_contains($item->file_type, 'pdf'))
                                <a href="{{ Storage::disk('public')->url($item->file_path) }}" target="_blank" 
                                   class="btn btn-sm btn-outline-info" title="Preview">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endif
                                @endif
                            </div>
                        </div>

                        <!-- File Preview (Images only) -->
                        @if($item->file_path && Storage::disk('public')->exists($item->file_path) && str_contains($item->file_type, 'image'))
                        <div class="text-center mb-2">
                            <img src="{{ Storage::disk('public')->url($item->file_path) }}" 
                                alt="{{ $item->title }}" 
                                class="img-fluid rounded border" 
                                style="max-height: 120px; object-fit: cover;">
                        </div>
                        @endif

                        <!-- File Info -->
                        @if($item->file_path)
                            @if(Storage::disk('public')->exists($item->file_path))
                            <div class="file-info small text-muted mb-2">
                                <div class="text-truncate" title="{{ $item->file_name }}">
                                    <i class="fas fa-file mr-1"></i> {{ Str::limit($item->file_name, 35) }}
                                </div>
                                <div>
                                    <i class="fas fa-hdd mr-1"></i> {{ number_format($item->file_size / 1024, 1) }} KB
                                </div>
                            </div>
                            @else
                            <div class="alert alert-warning small mb-2 py-1">
                                <i class="fas fa-exclamation-triangle"></i> File missing
                            </div>
                            @endif
                        @else
                        <div class="text-muted small mb-2">
                            <i class="fas fa-sticky-note"></i> Text content only
                        </div>
                        @endif

                        <!-- Collection & Community -->
                        <div class="small text-muted mb-2">
                            <div class="text-truncate">
                                <i class="fas fa-folder mr-1"></i> 
                                <strong>{{ $item->collection->name }}</strong>
                            </div>
                            <div class="text-truncate">
                                <i class="fas fa-users mr-1"></i> 
                                {{ $item->collection->community->name }}
                            </div>
                        </div>

                        <!-- Categories -->
                        @if($item->categories->count() > 0)
                        <div class="mb-2">
                            @foreach($item->categories->take(2) as $category)
                            <span class="badge badge-light border text-dark small mb-1">{{ $category->name }}</span>
                            @endforeach
                            @if($item->categories->count() > 2)
                            <span class="badge badge-light border text-muted small">+{{ $item->categories->count() - 2 }} more</span>
                            @endif
                        </div>
                        @endif

                        <!-- JSON Metadata Preview -->
                        @if(!empty($item->metadata) && is_array($item->metadata))
                        <div class="metadata-preview small text-muted mt-2">
                            @php
                                $displayedMeta = 0;
                                $commonFields = ['author', 'year', 'language', 'pages', 'description', 'type'];
                            @endphp
                            
                            <!-- Display common fields first -->
                            @foreach($commonFields as $field)
                                @if(isset($item->metadata[$field]) && !empty($item->metadata[$field]) && $displayedMeta < 2)
                                <div class="text-truncate" title="{{ $item->metadata[$field] }}">
                                    <strong>{{ Str::title($field) }}:</strong> {{ Str::limit($item->metadata[$field], 25) }}
                                </div>
                                @php $displayedMeta++; @endphp
                                @endif
                            @endforeach
                            
                            <!-- Display other fields if we haven't reached the limit -->
                            @if($displayedMeta < 2)
                                @foreach($item->metadata as $key => $value)
                                    @if(!in_array($key, $commonFields) && !empty($value) && $displayedMeta < 2)
                                    <div class="text-truncate" title="{{ $value }}">
                                        <strong>{{ Str::title($key) }}:</strong> {{ Str::limit($value, 25) }}
                                    </div>
                                    @php $displayedMeta++; @endphp
                                    @endif
                                @endforeach
                            @endif
                            
                            <!-- Show more indicator -->
                            @if(count($item->metadata) > $displayedMeta)
                            <div class="text-muted">
                                <small>+{{ count($item->metadata) - $displayedMeta }} more fields</small>
                            </div>
                            @endif
                        </div>
                        @elseif(!empty($item->metadata))
                        <div class="text-muted small">
                            <i>Invalid metadata format</i>
                        </div>
                        @endif
                    </div>
                    
                    <div class="card-footer py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted" title="{{ $item->created_at->format('M j, Y g:i A') }}">
                                <i class="fas fa-clock mr-1"></i>{{ $item->created_at->diffForHumans() }}
                            </small>
                            <div class="btn-group">
                                <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-outline-secondary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(route('workflow.show', $item))
                                <a href="{{ route('workflow.show', $item) }}" class="btn btn-sm btn-outline-secondary" title="Workflow">
                                    <i class="fas fa-tasks"></i>
                                </a>
                                @endif
                                <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" 
                                            onclick="return confirm('Are you sure you want to delete this item?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($items->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
            <h4>No items found</h4>
            <p class="text-muted">
                @if(request()->hasAny(['search', 'category', 'collection', 'status']))
                Try adjusting your search filters or 
                <a href="{{ route('items.index') }}">clear all filters</a>.
                @else
                Get started by creating your first item.
                @endif
            </p>
            <a href="{{ route('items.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Item
            </a>
        </div>
        @endif

        <!-- Pagination with Results Info -->
        @if($items->hasPages())
        <div class="row mt-4">
            <div class="col-md-6">
                <p class="text-muted small">
                    Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} items
                </p>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.document-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.document-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
}
.badge-sm {
    font-size: 0.7em;
    padding: 0.25em 0.5em;
}
.file-info div {
    margin-bottom: 0.1rem;
}
.metadata-preview div {
    margin-bottom: 0.1rem;
}
</style>
@endsection