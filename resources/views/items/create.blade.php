@extends('layouts.app')

@section('title', 'Create Item')

@section('content_header')
    <h1>Create New Item</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Basic Information -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="title">Title *</label>
                                <input type="text" name="title" id="title" 
                                       class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="collection_id">Collection *</label>
                                <select name="collection_id" id="collection_id" 
                                        class="form-control @error('collection_id') is-invalid @enderror" required>
                                    <option value="">Select Collection</option>
                                    @foreach($collections as $collection)
                                        <option value="{{ $collection->id }}" 
                                            {{ old('collection_id') == $collection->id ? 'selected' : '' }}>
                                            {{ $collection->name }} ({{ $collection->community->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('collection_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" 
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- File Upload -->
                    <div class="form-group">
                        <label for="file">File Upload</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('file') is-invalid @enderror" 
                                   id="file" name="file">
                            <label class="custom-file-label" for="file">Choose file</label>
                        </div>
                        @error('file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Categories -->
                    <div class="form-group">
                        <label>Categories</label>
                        <div class="row">
                            @foreach($categories as $category)
                                <div class="col-md-3 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" 
                                               name="categories[]" 
                                               value="{{ $category->id }}"
                                               id="category_{{ $category->id }}"
                                               {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="category_{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- METADATA SECTION -->
                    <div class="card mt-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-tags mr-2"></i>Metadata
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                Add custom metadata fields to your item. These will be stored as structured data.
                            </p>

                            <!-- Predefined Metadata Fields -->
                            <div class="predefined-metadata mb-4">
                                <h6 class="text-muted mb-3">Common Fields</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="metadata_author">Author</label>
                                            <input type="text" name="metadata[author]" 
                                                   class="form-control"
                                                   value="{{ old('metadata.author') }}"
                                                   placeholder="Enter author name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="metadata_year">Year</label>
                                            <input type="number" name="metadata[year]" 
                                                   class="form-control"
                                                   value="{{ old('metadata.year') }}"
                                                   placeholder="Publication year">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="metadata_language">Language</label>
                                            <input type="text" name="metadata[language]" 
                                                   class="form-control"
                                                   value="{{ old('metadata.language') }}"
                                                   placeholder="e.g., English, Spanish">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="metadata_pages">Pages</label>
                                            <input type="number" name="metadata[pages]" 
                                                   class="form-control"
                                                   value="{{ old('metadata.pages') }}"
                                                   placeholder="Number of pages">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Metadata Fields -->
                            <div class="dynamic-metadata">
                                <h6 class="text-muted mb-3">Custom Fields</h6>
                                <div id="metadata-fields">
                                    @php
                                        // Handle old input for custom fields
                                        $customMetadata = [];
                                        if (old('metadata_keys') && old('metadata_values')) {
                                            foreach (old('metadata_keys') as $index => $key) {
                                                $value = old('metadata_values')[$index] ?? '';
                                                if (!empty($key) && !empty($value)) {
                                                    $customMetadata[$key] = $value;
                                                }
                                            }
                                        }
                                    @endphp

                                    @if(count($customMetadata) > 0)
                                        @foreach($customMetadata as $key => $value)
                                            <div class="metadata-field row mb-2">
                                                <div class="col-md-5">
                                                    <input type="text" name="metadata_keys[]" 
                                                           class="form-control metadata-key" 
                                                           value="{{ $key }}" 
                                                           placeholder="Field name">
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="text" name="metadata_values[]" 
                                                           class="form-control metadata-value" 
                                                           value="{{ $value }}" 
                                                           placeholder="Field value">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger btn-sm remove-metadata">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    <!-- Always show at least one empty field -->
                                    <div class="metadata-field row mb-2">
                                        <div class="col-md-5">
                                            <input type="text" name="metadata_keys[]" 
                                                   class="form-control metadata-key" 
                                                   placeholder="Field name (e.g., publisher, isbn)">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" name="metadata_values[]" 
                                                   class="form-control metadata-value" 
                                                   placeholder="Field value">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-metadata">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" id="add-metadata" class="btn btn-success btn-sm mt-2">
                                    <i class="fas fa-plus"></i> Add Custom Field
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Status and Actions -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" 
                                           name="is_published" 
                                           id="is_published"
                                           {{ old('is_published') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_published">
                                        Publish this item
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Published items are visible to everyone. Leave unchecked to save as draft.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Create Item
                            </button>
                            <a href="{{ route('items.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.metadata-field {
    transition: all 0.3s ease;
}
.metadata-field:hover {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
}
</style>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Create page jQuery loaded');

    // Update file input label
    $('#file').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });

    // Add metadata field
    $('#add-metadata').on('click', function() {
        console.log('Add metadata button clicked');
        
        var fieldHtml = `
            <div class="metadata-field row mb-2">
                <div class="col-md-5">
                    <input type="text" name="metadata_keys[]" 
                           class="form-control metadata-key" 
                           placeholder="Field name">
                </div>
                <div class="col-md-5">
                    <input type="text" name="metadata_values[]" 
                           class="form-control metadata-value" 
                           placeholder="Field value">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-metadata">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#metadata-fields').append(fieldHtml);
        console.log('New metadata field added');
    });

    // Remove metadata field
    $(document).on('click', '.remove-metadata', function() {
        console.log('Remove metadata clicked');
        $(this).closest('.metadata-field').remove();
    });

    // Prevent duplicate field names
    $(document).on('blur', '.metadata-key', function() {
        var currentKey = $(this).val().toLowerCase().trim();
        var currentField = $(this).closest('.metadata-field');
        
        if (currentKey) {
            var duplicates = 0;
            $('.metadata-key').not(this).each(function() {
                var otherKey = $(this).val().toLowerCase().trim();
                if (otherKey === currentKey) {
                    duplicates++;
                }
            });
            
            if (duplicates > 0) {
                alert('Field name "' + currentKey + '" already exists! Please use unique field names.');
                $(this).focus();
            }
        }
    });
});
</script>
@endpush