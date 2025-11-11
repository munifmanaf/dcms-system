@extends('layouts.app')

@section('title', $item->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $item->title }}</h3>
                    <div class="card-tools">
                        <span class="badge bg-{{ $item->workflow_state == 'published' ? 'success' : ($item->workflow_state == 'draft' ? 'secondary' : 'warning') }}">
                            {{ ucfirst($item->workflow_state) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- File Type and Basic Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            @if($item->file_type)
                            <p>
                                <strong>File Type:</strong>
                                <span class="badge bg-info">{{ $item->file_type }}</span>
                            </p>
                            @endif
                            <p>
                                <strong>Collection:</strong>
                                <span class="badge bg-light text-dark">{{ $item->collection->name }}</span>
                            </p>
                            <p>
                                <strong>Community:</strong>
                                <span class="badge bg-light text-dark">{{ $item->collection->community->name }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Created:</strong>
                                {{ $item->created_at->format('M d, Y') }}
                            </p>
                            <p>
                                <strong>Last Updated:</strong>
                                {{ $item->updated_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($item->description)
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p class="text-justify">{{ $item->description }}</p>
                    </div>
                    @endif

                    <!-- Dublin Core Metadata -->
                    <div class="metadata-section">
                        <h5>Dublin Core Metadata</h5>
                        @php
                            if (isset($item) && $item->metadata) {
                                $metadata = is_array($item->metadata) ? $item->metadata : json_decode($item->metadata, true);
                            }
                        @endphp
                        
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <!-- Title -->
                                    <tr>
                                        <th class="text-muted" style="width: 30%">Title:</th>
                                        <td>{{ $metadata['dc_title'][0] ?? $item->title }}</td>
                                    </tr>
                                    
                                    <!-- Creators -->
                                    @if(isset($metadata['dc_creator']) && !empty($metadata['dc_creator']))
                                    <tr>
                                        <th class="text-muted">Creators:</th>
                                        <td>
                                            <ul class="list-unstyled mb-0">
                                                @foreach($metadata['dc_creator'] as $creator)
                                                <li>{{ $creator }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                    @endif
                                    
                                    <!-- Subjects -->
                                    @if(isset($metadata['dc_subject']) && !empty($metadata['dc_subject']))
                                    <tr>
                                        <th class="text-muted">Subjects:</th>
                                        <td>
                                            @foreach($metadata['dc_subject'] as $subject)
                                            <span class="badge bg-light text-dark mb-1">{{ $subject }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <!-- Description -->
                                    @if(isset($metadata['dc_description']) && !empty($metadata['dc_description']))
                                    <tr>
                                        <th class="text-muted" style="width: 30%">Description:</th>
                                        <td>{{ $metadata['dc_description'][0] }}</td>
                                    </tr>
                                    @endif
                                    
                                    <!-- Publisher -->
                                    @if(isset($metadata['dc_publisher']) && !empty($metadata['dc_publisher']))
                                    <tr>
                                        <th class="text-muted">Publisher:</th>
                                        <td>{{ $metadata['dc_publisher'][0] }}</td>
                                    </tr>
                                    @endif
                                    
                                    <!-- Date Issued -->
                                    @if(isset($metadata['dc_date_issued']) && !empty($metadata['dc_date_issued']))
                                    <tr>
                                        <th class="text-muted">Date Issued:</th>
                                        <td>{{ $metadata['dc_date_issued'][0] }}</td>
                                    </tr>
                                    @endif
                                    
                                    <!-- Type -->
                                    @if(isset($metadata['dc_type']) && !empty($metadata['dc_type']))
                                    <tr>
                                        <th class="text-muted">Type:</th>
                                        <td>{{ $metadata['dc_type'][0] }}</td>
                                    </tr>
                                    @endif
                                    
                                    <!-- Format -->
                                    @if(isset($metadata['dc_format']) && !empty($metadata['dc_format']))
                                    <tr>
                                        <th class="text-muted">Format:</th>
                                        <td>{{ $metadata['dc_format'][0] }}</td>
                                    </tr>
                                    @endif
                                    
                                    <!-- Identifier -->
                                    @if(isset($metadata['dc_identifier']) && !empty($metadata['dc_identifier']))
                                    <tr>
                                        <th class="text-muted">Identifier:</th>
                                        <td><code>{{ $metadata['dc_identifier'][0] }}</code></td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Raw JSON Metadata (for debugging) -->
                    <details class="mt-4">
                        <summary class="btn btn-sm btn-outline-secondary">View Raw Metadata JSON</summary>
                        <pre class="mt-2 p-3 bg-light rounded"><code>{{ json_encode($metadata, JSON_PRETTY_PRINT) }}</code></pre>
                    </details>
                </div>
                
                

                <div class="card-footer">
                    <div class="btn-group">
                        <a href="{{ route('items.edit', $item->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Item
                        </a>
                        <a href="{{ route('repository.item', $item->id) }}" class="btn btn-info" target="_blank">
                            <i class="fas fa-eye"></i> View Public Page
                        </a>
                        <a href="{{ route('items.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($item->workflow_state == 'draft')
                        <form action="{{ route('items.update', $item->id) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="workflow_state" value="pending_review">
                            <button type="submit" class="btn btn-warning btn-block">
                                <i class="fas fa-paper-plane"></i> Submit for Review
                            </button>
                        </form>
                        @endif
                        
                        @if($item->workflow_state == 'pending_review')
                        <form action="{{ route('items.update', $item->id) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="workflow_state" value="published">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-check"></i> Publish Item
                            </button>
                        </form>
                        @endif
                        
                        @if($item->workflow_state == 'published')
                        <form action="{{ route('items.update', $item->id) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="workflow_state" value="draft">
                            <input type="hidden" name="item_id" value="{{$item->id}}">
                            <button type="submit" class="btn btn-secondary btn-block">
                                <i class="fas fa-undo"></i> Unpublish
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        <!-- Enhanced Workflow Status Display -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-tasks"></i> Workflow Progress
                    </h5>
                </div>
                <div class="card-body">
                    @include('workflow.show', ['item' => $item])
                </div>
            </div>
            <!-- Collection Info -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Collection Information</h5>
                </div>
                <div class="card-body">
                    <p>
                        <strong>Collection:</strong><br>
                        {{ $item->collection->name }}
                    </p>
                    <p>
                        <strong>Community:</strong><br>
                        {{ $item->collection->community->name }}
                    </p>
                    @if($item->collection->description)
                    <p>
                        <strong>Description:</strong><br>
                        {{ $item->collection->description }}
                    </p>
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
    background: #f8f9fa;
    border-radius: 5px;
    padding: 20px;
    border-left: 4px solid #007bff;
}
.table-borderless th {
    width: 30%;
}
</style>
@endsection