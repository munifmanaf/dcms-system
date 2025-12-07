{{-- resources/views/images/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Image: ' . $image->original_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Edit Image Details
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('images.show', $image) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Image
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('images.update', $image) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Current Image Preview -->
                                <div class="form-group">
                                    <label>Current Image</label>
                                    <div class="border rounded p-3 text-center bg-light">
                                        <img src="{{ $image->thumbnail_url }}" 
                                             class="img-fluid rounded"
                                             style="max-height: 200px;">
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                {{ $image->original_name }} · {{ $image->formatted_size }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Basic Info -->
                                <div class="form-group">
                                    <label for="original_name">Filename</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="original_name" 
                                           value="{{ $image->original_name }}"
                                           disabled>
                                    <small class="form-text text-muted">
                                        Original filename cannot be changed
                                    </small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="category" 
                                           name="category"
                                           value="{{ old('category', $image->category) }}"
                                           placeholder="e.g., Nature, Portraits, Events">
                                    <small class="form-text text-muted">
                                        Organize your images by category
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" 
                                              id="description" 
                                              name="description" 
                                              rows="4"
                                              placeholder="Describe this image...">{{ old('description', $image->description) }}</textarea>
                                </div>
                                
                                <!-- Tags -->
                                <div class="form-group">
                                    <label for="tags">Tags</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="tags" 
                                           name="tags"
                                           value="{{ old('tags', $image->tags) }}"
                                           placeholder="tag1, tag2, tag3 (comma separated)">
                                    <small class="form-text text-muted">
                                        Separate tags with commas
                                    </small>
                                </div>
                                
                                <!-- Visibility -->
                                <div class="form-group">
                                    <label>Visibility</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="is_public" 
                                               name="is_public"
                                               value="1"
                                               {{ $image->is_public ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_public">
                                            Make this image public
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Public images can be viewed by anyone
                                    </small>
                                </div>
                                
                                <!-- Status -->
                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="is_active" 
                                               name="is_active"
                                               value="1"
                                               {{ $image->is_active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Inactive images won't appear in galleries
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Buttons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="{{ route('images.show', $image) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="button" class="btn btn-warning" onclick="resetForm()">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Image Stats
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Uploaded:</th>
                            <td>{{ $image->created_at->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Last Modified:</th>
                            <td>{{ $image->updated_at->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Dimensions:</th>
                            <td>{{ $image->dimensions ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>File Size:</th>
                            <td>{{ $image->formatted_size }}</td>
                        </tr>
                        <tr>
                            <th>Storage Path:</th>
                            <td><small>{{ $image->path }}</small></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Danger Zone -->
            <div class="card border-danger mt-3">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i> Danger Zone
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Replace Image -->
                    <div class="mb-3">
                        <h6>Replace Image</h6>
                        <p class="small text-muted">
                            Upload a new image to replace this one (keeps same ID and metadata)
                        </p>
                        <form action="{{ route('images.replace', $image) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="new_image" accept="image/*">
                                    <label class="custom-file-label">Choose new image...</label>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-sync"></i> Replace
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Regenerate Previews -->
                    <div class="mb-3">
                        <h6>Regenerate Previews</h6>
                        <p class="small text-muted">
                            Recreate all preview sizes (thumbnail, preview, etc.)
                        </p>
                        <form action="{{ route('images.regenerate', $image) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info btn-block">
                                <i class="fas fa-redo"></i> Regenerate Previews
                            </button>
                        </form>
                    </div>
                    
                    <!-- Delete Image -->
                    <div>
                        <h6>Delete Image</h6>
                        <p class="small text-muted">
                            Permanently delete this image and all its previews
                        </p>
                        <form action="{{ route('images.destroy', $image) }}" method="POST" id="deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-block" onclick="confirmDelete()">
                                <i class="fas fa-trash"></i> Delete Permanently
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Reset form
function resetForm() {
    if (confirm('Reset all changes?')) {
        $('#editForm')[0].reset();
        // Restore checkbox states
        $('#is_public').prop('checked', {{ $image->is_public ? 'true' : 'false' }});
        $('#is_active').prop('checked', {{ $image->is_active ? 'true' : 'false' }});
    }
}

// Confirm delete
function confirmDelete() {
    Swal.fire({
        title: 'Delete Image?',
        html: `<div class="text-center">
                  <img src="{{ $image->thumbnail_url }}" class="img-fluid mb-3" style="max-height: 150px;">
                  <p>Are you sure you want to delete <strong>{{ $image->original_name }}</strong>?</p>
                  <p class="text-danger"><small>This action cannot be undone!</small></p>
               </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        width: 500
    }).then((result) => {
        if (result.isConfirmed) {
            $('#deleteForm').submit();
        }
    });
}

// File input label
$('.custom-file-input').on('change', function() {
    const fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').text(fileName);
});
</script>
@endpush