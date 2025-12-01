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
                    <br>
                    <hr>
                    <br>
                    @if (str_contains($item->file_type, 'Image') || str_contains($item->file_type, 'Video') || 
                    str_contains($item->file_type, 'PDF') || str_contains($item->file_type, 'Word Document') ||
                    str_contains($item->file_type, 'Dataset'))
                    <div class="preview-section mt-4">
                        <h5>File Preview</h5>
                        <div class="file-preview mt-2">
                            @if (str_contains($item->file_type, 'Image'))
                                <!-- Image Preview -->
                                <div class="image-preview text-center">
                                    <img src="{{ asset('storage/' . $item->file_path) }}" 
                                        alt="{{ $item->title }}" 
                                        class="img-fluid rounded shadow-sm" 
                                        style="max-height: 400px; max-width: 100%;"
                                        onerror="this.style.display='none'">
                                    <div class="mt-2">
                                        <small class="text-muted">Click image to view full size</small>
                                    </div>
                                </div>
                                
                            @elseif (str_contains($item->file_type, 'Video'))
                                <!-- Video Preview -->
                                <div class="video-preview text-center">
                                    <video controls class="rounded shadow-sm" style="max-width: 100%; max-height: 400px;">
                                        <source src="{{ asset('storage/' . $item->file_path) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div class="mt-2">
                                        <small class="text-muted">Video preview - use controls to play/pause</small>
                                    </div>
                                </div>
                                
                            @elseif (str_contains($item->file_type, 'PDF'))
                                <!-- PDF Preview -->
                                <div class="pdf-preview text-center">
                                    <div class="pdf-placeholder bg-light rounded p-4 mb-3">
                                        <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                                        <h5>PDF Document</h5>
                                        <p class="text-muted">{{ basename($item->file_path) }}</p>
                                    </div>
                                    <iframe src="{{ asset('storage/' . $item->file_path) }}#toolbar=0" 
                                            width="100%" 
                                            height="500" 
                                            class="border rounded"
                                            onerror="this.style.display='none'">
                                        Your browser doesn't support PDF preview. 
                                        <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank">Download instead</a>.
                                    </iframe>
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $item->file_path) }}" 
                                        target="_blank" 
                                        class="btn btn-primary btn-sm">
                                            <i class="fas fa-external-link-alt"></i> Open in New Tab
                                        </a>
                                        <a href="{{ asset('storage/' . $item->file_path) }}" 
                                        download 
                                        class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> Download PDF
                                        </a>
                                    </div>
                                </div>
                                
                            @elseif (str_contains($item->file_type, 'Word Document'))
                                <!-- Word Document Preview -->
                                <div class="word-preview text-center">
                                    <div class="word-placeholder bg-light rounded p-4 mb-3">
                                        <i class="fas fa-file-word fa-4x text-primary mb-3"></i>
                                        <h5>Word Document</h5>
                                        <p class="text-muted">{{ basename($item->file_path) }}</p>
                                        <p class="small text-muted">Preview not available for Word documents</p>
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $item->file_path) }}" 
                                        target="_blank" 
                                        class="btn btn-primary btn-sm">
                                            <i class="fas fa-external-link-alt"></i> Open in Word
                                        </a>
                                        <a href="{{ asset('storage/' . $item->file_path) }}" 
                                        download 
                                        class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> Download Document
                                        </a>
                                    </div>
                                </div>
                                
                            @elseif (str_contains($item->file_type, 'Dataset'))
                                <!-- Excel/Dataset Preview -->
                                <div class="excel-preview text-center">
                                    <div class="excel-placeholder bg-light rounded p-4 mb-3">
                                        <i class="fas fa-file-excel fa-4x text-success mb-3"></i>
                                        <h5>Spreadsheet Dataset</h5>
                                        <p class="text-muted">{{ basename($item->file_path) }}</p>
                                        <p class="small text-muted">Preview not available for spreadsheet files</p>
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $item->file_path) }}" 
                                        target="_blank" 
                                        class="btn btn-primary btn-sm">
                                            <i class="fas fa-external-link-alt"></i> Open in Excel
                                        </a>
                                        <a href="{{ asset('storage/' . $item->file_path) }}" 
                                        download 
                                        class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> Download Dataset
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <div class="card-footer">
                    <div class="btn-group">
                        <a href="{{ route('items.edit', $item->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Item
                        </a>
                        <a href="{{ route('items.show', $item->id) }}" class="btn btn-info" target="_blank">
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
                        @if(Auth::user()->hasAnyRole(['user']))
                        @if($item->workflow_state == 'draft')
                        <form action="{{ route('items.status', $item->id) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="workflow_state" value="pending_review">
                            <button type="submit" class="btn btn-warning btn-block">
                                <i class="fas fa-paper-plane"></i> Submit for Review
                            </button>
                        </form>
                        @elseif ($item->workflow_state == 'pending_review')
                        <form action="{{ route('items.status', $item->id) }}" method="POST">
                            @csrf @method('PUT')
                            {{-- <input type="hidden" name="workflow_state" value="pending_review"> --}}
                            <button type="button" class="btn btn-warning btn-block">
                                <i class="fas fa-clock"></i> Waiting for Review
                            </button>
                        </form>
                        @elseif ($item->workflow_state == 'published')
                        <form action="{{ route('items.status', $item->id) }}" method="POST">
                            @csrf @method('PUT')
                            {{-- <input type="hidden" name="workflow_state" value="pending_review"> --}}
                            <button type="button" class="btn btn-success btn-block">
                                <i class="fas fa-paper-check"></i> Published
                            </button>
                        </form>
                        @endif
                        @endif
                        @if(Auth::user()->hasAnyRole(['admin', 'manager']))
                        @if($item->workflow_state == 'pending_review')
                        <form action="{{ route('items.status', $item->id) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="workflow_state" value="published">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-check"></i> Publish Item
                            </button>
                        </form>
                        <br>
                        <form action="{{ route('items.status', $item->id) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="workflow_state" value="draft">
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-x"></i> Draft
                            </button>
                        </form>
                        @endif
                        
                        @if($item->workflow_state == 'published')
                        <form action="{{ route('items.status', $item->id) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="workflow_state" value="draft">
                            <input type="hidden" name="item_id" value="{{$item->id}}">
                            <button type="submit" class="btn btn-secondary btn-block">
                                <i class="fas fa-undo"></i> Unpublish
                            </button>
                        </form>
                        @endif
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
            <div class="modal fade" id="imageModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $item->title }}</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="" id="fullSizeImage" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Image modal functionality
document.addEventListener('DOMContentLoaded', function() {
    const image = document.querySelector('.image-preview img');
    if (image) {
        image.addEventListener('click', function() {
            const fullSizeImage = document.getElementById('fullSizeImage');
            fullSizeImage.src = this.src;
            $('#imageModal').modal('show');
        });
    }
});
</script>
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

.file-preview {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    background: #f8f9fa;
}
.image-preview img, .video-preview video {
    transition: transform 0.3s ease;
    cursor: pointer;
}
.image-preview img:hover {
    transform: scale(1.02);
}
.video-preview video {
    background: #000;
}
.pdf-placeholder, .word-placeholder, .excel-placeholder {
    border: 2px dashed #dee2e6;
}
</style>
@endsection
