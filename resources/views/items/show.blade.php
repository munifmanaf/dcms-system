@extends('layouts.app')

@section('page_title', $item->title)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
    <li class="breadcrumb-item active">{{ $item->title }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Item Details Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt mr-2"></i>
                        {{ $item->title }}
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $item->is_published ? 'success' : 'warning' }}">
                            {{ $item->is_published ? 'Published' : 'Draft' }}
                        </span>
                        @if($item->workflow_state)
                        <span class="badge badge-primary ml-1">
                            {{ ucfirst($item->workflow_state) }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- File Information -->
                    @if($item->file_path)
                    <div class="file-section mb-4">
                        <h5><i class="fas fa-file"></i> File Information</h5>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <div>
                                <h6 class="mb-1">{{ $item->file_name }}</h6>
                                <small class="text-muted">
                                    {{ $item->file_type ?? 'Unknown type' }} • 
                                    @if($item->file_size)
                                        {{ number_format($item->file_size / 1024, 2) }} KB • 
                                    @endif
                                    Uploaded {{ $item->created_at->format('M j, Y') }}
                                </small>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('items.download', $item) }}" class="btn btn-primary">
                                    <i class="fas fa-download"></i> Download
                                </a>
                                @if($item->file_path && (str_contains($item->file_type ?? '', 'image') || str_contains($item->file_type ?? '', 'pdf')))
                                <a href="{{ Storage::disk('public')->url($item->file_path) }}" 
                                   target="_blank" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Preview
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Description -->
                    @if($item->description)
                    <div class="description-section mb-4">
                        <h5><i class="fas fa-align-left"></i> Description</h5>
                        <p class="text-muted">{{ $item->description }}</p>
                    </div>
                    @endif

                    <!-- Collection & Categories -->
                    <div class="info-section mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-folder"></i> Collection</h6>
                                <p class="text-muted">
                                    {{ $item->collection->name ?? 'No collection' }}<br>
                                    @if($item->collection && $item->collection->community)
                                    <small>Community: {{ $item->collection->community->name }}</small>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-tags"></i> Categories</h6>
                                <div class="mb-2">
                                    @if(isset($item->categories) && $item->categories->count() > 0)
                                        @foreach($item->categories as $category)
                                        <span class="badge badge-secondary">{{ $category->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No categories</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metadata -->
                    @if(!empty($item->metadata) && is_array($item->metadata))
                    <div class="metadata-section">
                        <h5><i class="fas fa-tags"></i> Metadata</h5>
                        <div class="row">
                            @foreach($item->metadata as $key => $value)
                            @if(!empty($value))
                            <div class="col-md-6 mb-2">
                                <strong class="text-capitalize">{{ str_replace('_', ' ', $key) }}:</strong>
                                <span class="text-muted">
                                    @if(is_array($value))
                                        {{ implode(', ', $value) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </span>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Version History (if available) -->
            @php
                $hasVersions = method_exists($item, 'versions') && $item->versions()->exists();
            @endphp
            
            @if($hasVersions)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history mr-2"></i>
                        Recent Versions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($item->versions()->take(3)->get() as $version)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <span class="badge badge-primary">v{{ $version->version_number }}</span>
                                    {{ $version->title }}
                                </h6>
                                <small class="text-muted">{{ $version->created_at->diffForHumans() }}</small>
                            </div>
                            @if($version->changes)
                            <p class="mb-1 small text-muted">{{ $version->changes }}</p>
                            @endif
                            <small class="text-muted">By {{ $version->user->name ?? 'Unknown' }}</small>
                        </div>
                        @endforeach
                    </div>
                    @php
                        $versionCount = $item->versions()->count();
                    @endphp
                    @if($versionCount > 3)
                    <div class="text-center mt-3">
                        <a href="{{ route('items.versions', $item) }}" class="btn btn-sm btn-outline-primary">
                            View All Versions ({{ $versionCount }})
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Workflow History -->
            @if(file_exists(resource_path('views/workflow/show.blade.php')))
                @include('workflow.show')
            @endif
        </div>

        <div class="col-md-4">
            <!-- Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog mr-2"></i>
                        Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('items.edit', $item) }}" class="btn btn-primary">
                            <i class="fas fa-edit mr-1"></i> Edit Item
                        </a>
                        
                        <!-- Version Management -->
                        @php
                            $hasVersions = method_exists($item, 'versions') && $item->versions()->exists();
                            $versionCount = $hasVersions ? $item->versions()->count() : 0;
                        @endphp
                        
                        @if($hasVersions && $versionCount > 0)
                        <a href="{{ route('items.versions', $item) }}" class="btn btn-info">
                            <i class="fas fa-history mr-1"></i> 
                            Versions ({{ $versionCount }})
                        </a>
                        @endif

                        <!-- Workflow -->
                        @if(route('workflow.show', $item) && $item->workflow_state)
                        <a href="{{ route('workflow.show', $item) }}" class="btn btn-warning">
                            <i class="fas fa-tasks mr-1"></i> Workflow
                        </a>
                        @endif

                        <!-- Delete Form -->
                        <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-grid">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this item?')">
                                <i class="fas fa-trash mr-1"></i> Delete Item
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Item Information Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        Item Information
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Created:</strong></td>
                            <td class="text-muted">
                                <span title="{{ $item->created_at->format('M j, Y g:i A') }}">
                                    {{ $item->created_at->diffForHumans() }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>By:</strong></td>
                            <td class="text-muted">{{ $item->user->name ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Updated:</strong></td>
                            <td class="text-muted">
                                <span title="{{ $item->updated_at->format('M j, Y g:i A') }}">
                                    {{ $item->updated_at->diffForHumans() }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>File Type:</strong></td>
                            <td class="text-muted">{{ $item->file_type ?? 'Text' }}</td>
                        </tr>
                        <tr>
                            <td><strong>File Size:</strong></td>
                            <td class="text-muted">
                                @if($item->file_size)
                                    {{ number_format($item->file_size / 1024, 2) }} KB
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        @if($item->collection)
                        <tr>
                            <td><strong>Collection:</strong></td>
                            <td class="text-muted">{{ $item->collection->name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Quick Stats Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Quick Stats
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Workflow Actions Count -->
                    <div class="small-box bg-info mb-3">
                        <div class="inner">
                            <h3>{{ isset($item->workflowActions) ? $item->workflowActions->count() : 0 }}</h3>
                            <p>Workflow Actions</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-history"></i>
                        </div>
                    </div>
                    
                    <!-- Last Action -->
                    <div class="mt-3">
                        <strong>Last Action:</strong><br>
                        @if(isset($item->workflowActions) && $item->workflowActions->count() > 0)
                            @php $lastAction = $item->workflowActions->last(); @endphp
                            <span class="text-primary">
                                {{ $lastAction->workflowStep->name ?? 'Unknown step' }}
                            </span>
                            by {{ $lastAction->user->name ?? 'Unknown user' }}<br>
                            <small class="text-muted">{{ $lastAction->created_at->diffForHumans() }}</small>
                        @else
                            <small class="text-muted">No actions yet</small>
                        @endif
                    </div>

                    <!-- Version Count -->
                    @php
                        $hasVersions = method_exists($item, 'versions') && $item->versions()->exists();
                        $versionCount = $hasVersions ? $item->versions()->count() : 0;
                    @endphp
                    
                    @if($hasVersions)
                    <div class="mt-3">
                        <strong>Versions:</strong><br>
                        <span class="text-info">{{ $versionCount }} versions</span><br>
                        <small class="text-muted">
                            Last updated {{ $item->updated_at->diffForHumans() }}
                        </small>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Workflow Status Component -->
            @if(file_exists(resource_path('views/components/workflow-status.blade.php')))
                @include('components.workflow-status', ['item' => $item])
            @endif
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
.small-box {
    border-radius: 0.25rem;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    display: block;
    margin-bottom: 20px;
    position: relative;
    background: #17a2b8;
    color: white;
    padding: 15px;
}
.small-box > .inner { padding: 10px; }
.small-box h3 { 
    font-size: 2.2rem; 
    font-weight: bold; 
    margin: 0 0 10px 0; 
    white-space: nowrap; 
    padding: 0; 
    color: white;
}
.small-box p { 
    margin: 0;
    color: rgba(255,255,255,0.8);
}
.small-box .icon {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 0;
    font-size: 70px;
    color: rgba(0,0,0,0.15);
}
.file-section, .description-section, .info-section, .metadata-section {
    border-bottom: 1px solid #eaeaea;
    padding-bottom: 1rem;
}
.file-section:last-child, .description-section:last-child, 
.info-section:last-child, .metadata-section:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
</style>
@endsection