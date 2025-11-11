@extends('layouts.app')

@section('title', 'Items Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Items Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('items.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Item
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form action="{{ route('items.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search items..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group float-right">
                                <a href="{{ route('items.index') }}" class="btn btn-sm btn-outline-secondary {{ !request('workflow_state') ? 'active' : '' }}">
                                    All
                                </a>
                                <a href="{{ route('items.index', ['workflow_state' => 'published']) }}" class="btn btn-sm btn-outline-success {{ request('workflow_state') == 'published' ? 'active' : '' }}">
                                    Published
                                </a>
                                <a href="{{ route('items.index', ['workflow_state' => 'draft']) }}" class="btn btn-sm btn-outline-secondary {{ request('workflow_state') == 'draft' ? 'active' : '' }}">
                                    Draft
                                </a>
                                <a href="{{ route('items.index', ['workflow_state' => 'pending_review']) }}" class="btn btn-sm btn-outline-warning {{ request('workflow_state') == 'pending_review' ? 'active' : '' }}">
                                    Pending
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Items Grid -->
                    <div class="row">
                        @forelse($items as $item)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ Str::limit($item->title, 60) }}</h5>
                                    <div class="card-tools">
                                        <span class="badge bg-{{ $item->workflow_state == 'published' ? 'success' : ($item->workflow_state == 'draft' ? 'secondary' : 'warning') }}">
                                            {{ ucfirst($item->workflow_state) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- File Type Badge -->
                                    @if($item->file_type)
                                        
                                    <div class="mb-2">
                                        @if(str_contains($item->file_type, 'Image'))
                                        <span class="badge badge-info badge-sm">
                                            <i class="fas fa-image mr-1"></i>Image
                                        </span>
                                        @elseif(str_contains($item->file_type, 'PDF'))
                                        <span class="badge badge-danger badge-sm">
                                            <i class="fas fa-file-pdf mr-1"></i>PDF
                                        </span>
                                        @elseif(str_contains($item->file_type, 'Word Document') || str_contains($item->file_type, 'document'))
                                        <span class="badge badge-primary badge-sm">
                                            <i class="fas fa-file-word mr-1"></i>Document
                                        </span>
                                        @elseif(str_contains($item->file_type, 'Dataset') || str_contains($item->file_type, 'spreadsheet'))
                                        <span class="badge badge-success badge-sm">
                                            <i class="fas fa-file-excel mr-1"></i>Spreadsheet
                                        </span>
                                        @elseif(str_contains($item->file_type, 'Video'))
                                        <span class="badge badge-warning badge-sm">
                                            <i class="fas fa-file-video mr-1"></i>Video
                                        </span>
                                        @elseif(str_contains($item->file_type, 'Audio'))
                                        <span class="badge badge-secondary badge-sm">
                                            <i class="fas fa-file-audio mr-1"></i>Audio
                                        </span>
                                        @else
                                        <span class="badge badge-secondary badge-sm">
                                            <i class="fas fa-file mr-1"></i>File
                                        </span>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Description -->
                                    @if($item->description)
                                    <p class="card-text text-muted small">
                                        {{ Str::limit($item->description, 100) }}
                                    </p>
                                    @endif

                                    <!-- Dublin Core Metadata -->
                                    <div class="metadata-section">
                                        <!-- Authors -->
                                        @php
                                            if (isset($item) && $item->metadata) {
                                                $metadata = is_array($item->metadata) ? $item->metadata : json_decode($item->metadata, true);
                                            }
                                            $creators = $metadata['dc_creator'] ?? [];
                                            $subjects = $metadata['dc_subject'] ?? [];
                                            $dateIssued = $metadata['dc_date_issued'][0] ?? null;
                                            $publisher = $metadata['dc_publisher'][0] ?? null;
                                        @endphp

                                        @if(!empty($creators))
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> 
                                                {{ implode(', ', $creators) }}
                                            </small>
                                        </div>
                                        @endif

                                        <!-- Subjects -->
                                        @if(!empty($subjects))
                                        <div class="mb-2">
                                            @foreach(array_slice($subjects, 0, 3) as $subject)
                                            <span class="badge bg-light text-dark badge-sm mb-1">{{ $subject }}</span>
                                            @endforeach
                                            @if(count($subjects) > 3)
                                            <span class="text-muted small">+{{ count($subjects) - 3 }} more</span>
                                            @endif
                                        </div>
                                        @endif

                                        <!-- Date and Publisher -->
                                        <div class="mb-2">
                                            @if($dateIssued)
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> {{ $dateIssued }}
                                            </small>
                                            @endif
                                            @if($publisher)
                                            <small class="text-muted ml-2">
                                                <i class="fas fa-building"></i> {{ $publisher }}
                                            </small>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Collection Info -->
                                    <div class="border-top pt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-folder"></i> {{ $item->collection->name }}
                                            <br>
                                            <i class="fas fa-users"></i> {{ $item->collection->community->name }}
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="btn-group btn-group-sm w-100">
                                        <a href="{{ route('items.show', $item->id) }}" 
                                           class="btn btn-info" 
                                           target="_blank"
                                           title="View Public Page">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('items.edit', $item->id) }}" 
                                           class="btn btn-primary"
                                           title="Edit Item">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this item?')"
                                                    title="Delete Item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-md-12">
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No items found</h4>
                                <p class="text-muted">Get started by creating your first item</p>
                                <a href="{{ route('items.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create Item
                                </a>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($items->hasPages())
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="float-right">
                                {{ $items->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.metadata-section {
    border-left: 3px solid #007bff;
    padding-left: 10px;
    margin: 10px 0;
}
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.badge-sm {
    font-size: 0.7em;
}
</style>
@endsection