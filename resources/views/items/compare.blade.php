@extends('layouts.app')

@section('page_title', "Compare Versions - {$item->title}")
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
    <li class="breadcrumb-item"><a href="{{ route('items.show', $item) }}">{{ $item->title }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('items.versions', $item) }}">Versions</a></li>
    <li class="breadcrumb-item active">Compare</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-code-compare"></i> Compare Versions - {{ $item->title }}
        </h3>
        <div class="card-tools">
            <a href="{{ route('items.versions', $item) }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Versions
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Version Selection -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">Version 1</h6>
                    </div>
                    <div class="card-body">
                        @if(isset($version1) && $version1 instanceof \App\Models\ItemVersion)
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge badge-primary">v{{ $version1->version_number }}</span>
                                <h6 class="mt-1 mb-0">{{ $version1->title }}</h6>
                                <small class="text-muted">
                                    By {{ $version1->user->name }} • 
                                    {{ $version1->created_at->format('M j, Y g:i A') }}
                                </small>
                            </div>
                            <a href="{{ route('items.versions.download', $version1) }}" 
                               class="btn btn-sm btn-outline-primary" title="Download this version">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                        @else
                        <p class="text-muted mb-0">Current Version</p>
                        <h6 class="mt-1 mb-0">{{ $item->title }}</h6>
                        <small class="text-muted">Latest state</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="card-title mb-0">Version 2</h6>
                    </div>
                    <div class="card-body">
                        @if(isset($version2) && $version2 instanceof \App\Models\ItemVersion)
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge badge-primary">v{{ $version2->version_number }}</span>
                                <h6 class="mt-1 mb-0">{{ $version2->title }}</h6>
                                <small class="text-muted">
                                    By {{ $version2->user->name }} • 
                                    {{ $version2->created_at->format('M j, Y g:i A') }}
                                </small>
                            </div>
                            <a href="{{ route('items.versions.download', $version2) }}" 
                               class="btn btn-sm btn-outline-primary" title="Download this version">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                        @else
                        <p class="text-muted mb-0">Current Version</p>
                        <h6 class="mt-1 mb-0">{{ $item->title }}</h6>
                        <small class="text-muted">Latest state</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Differences -->
        @if(empty($differences))
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i> No differences found between these versions.
        </div>
        @else
        <div class="differences-container">
            @foreach($differences as $field => $diff)
            <div class="card mb-3">
                <div class="card-header bg-{{ $field === 'file' ? 'warning' : 'light' }}">
                    <h6 class="card-title mb-0 text-capitalize">
                        @switch($field)
                            @case('title')
                                <i class="fas fa-heading mr-1"></i>
                                @break
                            @case('description')
                                <i class="fas fa-align-left mr-1"></i>
                                @break
                            @case('metadata')
                                <i class="fas fa-tags mr-1"></i>
                                @break
                            @case('file')
                                <i class="fas fa-file mr-1"></i>
                                @break
                            @default
                                <i class="fas fa-pencil-alt mr-1"></i>
                        @endswitch
                        {{ str_replace('_', ' ', $field) }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="version-content bg-light p-3 rounded">
                                <h6 class="text-muted small">Version 1</h6>
                                @if($field === 'file')
                                    <div>
                                        <strong>Name:</strong> {{ $diff['from']['name'] ?? 'No file' }}<br>
                                        <strong>Size:</strong> {{ $diff['from']['size'] ? number_format($diff['from']['size'] / 1024, 2) . ' KB' : 'N/A' }}
                                    </div>
                                @elseif($field === 'metadata')
                                    @if(!empty($diff['from']))
                                        <pre class="mb-0 small">{{ json_encode($diff['from'], JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        <span class="text-muted">No metadata</span>
                                    @endif
                                @else
                                    <div class="content-diff">
                                        {{ $diff['from'] ?? 'Empty' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="version-content bg-light p-3 rounded">
                                <h6 class="text-muted small">Version 2</h6>
                                @if($field === 'file')
                                    <div>
                                        <strong>Name:</strong> {{ $diff['to']['name'] ?? 'No file' }}<br>
                                        <strong>Size:</strong> {{ $diff['to']['size'] ? number_format($diff['to']['size'] / 1024, 2) . ' KB' : 'N/A' }}
                                    </div>
                                @elseif($field === 'metadata')
                                    @if(!empty($diff['to']))
                                        <pre class="mb-0 small">{{ json_encode($diff['to'], JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        <span class="text-muted">No metadata</span>
                                    @endif
                                @else
                                    <div class="content-diff">
                                        {{ $diff['to'] ?? 'Empty' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<style>
.content-diff {
    white-space: pre-wrap;
    word-wrap: break-word;
    font-family: inherit;
}
.differences-container .card {
    border-left: 4px solid #007bff;
}
.version-content {
    min-height: 80px;
    max-height: 200px;
    overflow-y: auto;
}
</style>
@endsection