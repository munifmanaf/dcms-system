@extends('layouts.app')

@section('title', isset($item) ? 'Edit Item' : 'Add New Item')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas {{ isset($item) ? 'fa-edit' : 'fa-plus' }}"></i>
                        {{ isset($item) ? 'Edit Item' : 'Add New Item' }}
                    </h3>
                </div>
                <form action="{{ isset($item) ? route('items.update', $item->id) : route('items.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($item))
                        @method('PUT')
                    @endif

                    <div class="card-body">
                        <!-- Basic Information -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h4 class="card-title">Basic Information</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="title">Title *</label>
                                            <input type="text" name="title" id="title" 
                                                   class="form-control @error('title') is-invalid @enderror" 
                                                   value="{{ old('title', $item->title ?? '') }}" required>
                                            @error('title')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="file_type">File Type *</label>
                                            <select name="file_type" id="file_type" 
                                                    class="form-control @error('file_type') is-invalid @enderror" required>
                                                <option value="">Select File Type</option>
                                                <option value="PDF" {{ old('file_type', $item->file_type ?? '') == 'PDF' ? 'selected' : '' }}>PDF</option>
                                                <option value="Word Document" {{ old('file_type', $item->file_type ?? '') == 'Word Document' ? 'selected' : '' }}>Word Document</option>
                                                <option value="Image" {{ old('file_type', $item->file_type ?? '') == 'Image' ? 'selected' : '' }}>Image</option>
                                                <option value="Dataset" {{ old('file_type', $item->file_type ?? '') == 'Dataset' ? 'selected' : '' }}>Dataset</option>
                                                <option value="Video" {{ old('file_type', $item->file_type ?? '') == 'Video' ? 'selected' : '' }}>Video</option>
                                                <option value="Other" {{ old('file_type', $item->file_type ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('file_type')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" 
                                              class="form-control @error('description') is-invalid @enderror" 
                                              rows="3">{{ old('description', $item->description ?? '') }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="collection_id">Collection *</label>
                                            <select name="collection_id" id="collection_id" 
                                                    class="form-control @error('collection_id') is-invalid @enderror" required>
                                                <option value="">Select Collection</option>
                                                @foreach($collections as $collection)
                                                    <option value="{{ $collection->id }}" 
                                                        {{ old('collection_id', $item->collection_id ?? '') == $collection->id ? 'selected' : '' }}>
                                                        {{ $collection->community->name }} - {{ $collection->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('collection_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="workflow_state">Status</label>
                                            <select name="workflow_state" id="workflow_state" 
                                                    class="form-control @error('workflow_state') is-invalid @enderror">
                                                <option value="draft" {{ old('workflow_state', $item->workflow_state ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                                <option value="pending_review" {{ old('workflow_state', $item->workflow_state ?? '') == 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                                                <option value="published" {{ old('workflow_state', $item->workflow_state ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                                            </select>
                                            @error('workflow_state')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dublin Core Metadata -->
                        <div class="card card-info">
                            <div class="card-header">
                                <h4 class="card-title">Dublin Core Metadata</h4>
                                <small class="text-muted">Standard metadata fields for repository items</small>
                            </div>
                            <div class="card-body">
                                @php
                                    // Handle metadata for both create and edit
                                    $metadata = [];
                                    if (isset($item) && $item->metadata) {
                                        $metadata = is_array($item->metadata) ? $item->metadata : json_decode($item->metadata, true);
                                    }
                                    $metadata = $metadata ?? [];
                                @endphp

                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- DC Creator -->
                                        <div class="form-group">
                                            <label for="dc_creator">Authors/Creators</label>
                                            <small class="text-muted">(dc.creator)</small>
                                            <input type="text" name="dc_creator" id="dc_creator" 
                                                   class="form-control" 
                                                   value="{{ old('dc_creator', implode(', ', $metadata['dc_creator'] ?? [])) }}"
                                                   placeholder="Enter multiple authors separated by commas">
                                            <small class="text-muted">Separate multiple authors with commas</small>
                                        </div>

                                        <!-- DC Subject -->
                                        <div class="form-group">
                                            <label for="dc_subject">Subjects/Keywords</label>
                                            <small class="text-muted">(dc.subject)</small>
                                            <input type="text" name="dc_subject" id="dc_subject" 
                                                   class="form-control" 
                                                   value="{{ old('dc_subject', implode(', ', $metadata['dc_subject'] ?? [])) }}"
                                                   placeholder="Enter subjects or keywords separated by commas">
                                            <small class="text-muted">Separate multiple subjects with commas</small>
                                        </div>

                                        <!-- DC Description -->
                                        <div class="form-group">
                                            <label for="dc_description">Description</label>
                                            <small class="text-muted">(dc.description)</small>
                                            <textarea name="dc_description" id="dc_description" 
                                                      class="form-control" 
                                                      rows="3"
                                                      placeholder="Detailed description of the item">{{ old('dc_description', $metadata['dc_description'][0] ?? '') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <!-- DC Publisher -->
                                        <div class="form-group">
                                            <label for="dc_publisher">Publisher</label>
                                            <small class="text-muted">(dc.publisher)</small>
                                            <input type="text" name="dc_publisher" id="dc_publisher" 
                                                   class="form-control" 
                                                   value="{{ old('dc_publisher', $metadata['dc_publisher'][0] ?? '') }}"
                                                   placeholder="e.g., University Press, Journal Name">
                                        </div>

                                        <!-- DC Date Issued -->
                                        <div class="form-group">
                                            <label for="dc_date_issued">Publication Date</label>
                                            <small class="text-muted">(dc.date.issued)</small>
                                            <input type="date" name="dc_date_issued" id="dc_date_issued" 
                                                   class="form-control" 
                                                   value="{{ old('dc_date_issued', $metadata['dc_date_issued'][0] ?? '') }}">
                                        </div>

                                        <!-- DC Type -->
                                        <div class="form-group">
                                            <label for="dc_type">Resource Type</label>
                                            <small class="text-muted">(dc.type)</small>
                                            <select name="dc_type" id="dc_type" class="form-control">
                                                <option value="">Select Type</option>
                                                <option value="Thesis" {{ old('dc_type', $metadata['dc_type'][0] ?? '') == 'Thesis' ? 'selected' : '' }}>Thesis</option>
                                                <option value="Research Paper" {{ old('dc_type', $metadata['dc_type'][0] ?? '') == 'Research Paper' ? 'selected' : '' }}>Research Paper</option>
                                                <option value="Journal Article" {{ old('dc_type', $metadata['dc_type'][0] ?? '') == 'Journal Article' ? 'selected' : '' }}>Journal Article</option>
                                                <option value="Dataset" {{ old('dc_type', $metadata['dc_type'][0] ?? '') == 'Dataset' ? 'selected' : '' }}>Dataset</option>
                                                <option value="Image" {{ old('dc_type', $metadata['dc_type'][0] ?? '') == 'Image' ? 'selected' : '' }}>Image</option>
                                                <option value="Video" {{ old('dc_type', $metadata['dc_type'][0] ?? '') == 'Video' ? 'selected' : '' }}>Video</option>
                                                <option value="Literary Work" {{ old('dc_type', $metadata['dc_type'][0] ?? '') == 'Literary Work' ? 'selected' : '' }}>Literary Work</option>
                                                <option value="Other" {{ old('dc_type', $metadata['dc_type'][0] ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>

                                        <!-- DC Format -->
                                        <div class="form-group">
                                            <label for="dc_format">Format</label>
                                            <small class="text-muted">(dc.format)</small>
                                            <input type="text" name="dc_format" id="dc_format" 
                                                   class="form-control" 
                                                   value="{{ old('dc_format', $metadata['dc_format'][0] ?? '') }}"
                                                   placeholder="e.g., PDF, DOCX, JPG, MP4">
                                        </div>

                                        <!-- DC Identifier -->
                                        <div class="form-group">
                                            <label for="dc_identifier">Identifier</label>
                                            <small class="text-muted">(dc.identifier)</small>
                                            <input type="text" name="dc_identifier" id="dc_identifier" 
                                                   class="form-control" 
                                                   value="{{ old('dc_identifier', $metadata['dc_identifier'][0] ?? '') }}"
                                                   placeholder="e.g., DOI, ISBN, or custom ID">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div class="card card-warning">
                            <div class="card-header">
                                <h4 class="card-title">File Attachment</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="file">Upload File</label>
                                    @if(isset($item) && $item->file_path)
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            Current file: <strong>{{ basename($item->file_path) }}</strong>
                                            <br>
                                            <small>Upload a new file to replace the existing one.</small>
                                        </div>
                                    @endif
                                    <input type="file" name="file" id="file" 
                                           class="form-control-file @error('file') is-invalid @enderror"
                                           {{ !isset($item) ? 'required' : '' }}>
                                    @error('file')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">
                                        Supported formats: PDF, DOC, DOCX, JPG, PNG, CSV, XLSX, MP4
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="float-left">
                            <a href="{{ route('items.index') }}" class="btn btn-default">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                        <div class="float-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 
                                {{ isset($item) ? 'Update Item' : 'Create Item' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill dc_format based on file_type selection
    const fileTypeSelect = document.getElementById('file_type');
    const formatInput = document.getElementById('dc_format');

    if (fileTypeSelect && formatInput) {
        fileTypeSelect.addEventListener('change', function() {
            const fileType = this.value;
            const formatMap = {
                'PDF': 'PDF',
                'Word Document': 'DOCX',
                'Image': 'JPG',
                'Dataset': 'CSV',
                'Video': 'MP4',
                'Other': 'Other'
            };
            
            if (formatMap[fileType] && !formatInput.value) {
                formatInput.value = formatMap[fileType];
            }
        });
    }

    // Auto-generate identifier if empty
    const identifierInput = document.getElementById('dc_identifier');
    if (identifierInput && !identifierInput.value) {
        const timestamp = new Date().getTime();
        identifierInput.value = 'ITEM-' + timestamp;
    }
});
</script>
@endsection