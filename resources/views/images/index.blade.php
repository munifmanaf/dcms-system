{{-- resources/views/images/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Image Gallery')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-images"></i> Image Gallery
                        <span class="badge badge-primary ml-2">{{ $images->total() }}</span>
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('images.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Upload Image
                        </a>
                        <a href="{{ route('images.create') }}?batch=true" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Batch Upload
                        </a>
                        <button class="btn btn-info" data-toggle="collapse" data-target="#filterSection">
                            <i class="fas fa-filter"></i> Filters
                        </button>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="collapse" id="filterSection">
                    <div class="card-body">
                        <form method="GET" action="{{ route('images.index') }}" class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Search</label>
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Search images..."
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="category" class="form-control">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category }}" 
                                                {{ request('category') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Sort By</label>
                                    <select name="sort" class="form-control">
                                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>
                                            Newest First
                                        </option>
                                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                            Oldest First
                                        </option>
                                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>
                                            Name A-Z
                                        </option>
                                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>
                                            Name Z-A
                                        </option>
                                        <option value="size" {{ request('sort') == 'size' ? 'selected' : '' }}>
                                            Largest First
                                        </option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Visibility</label>
                                    <select name="visibility" class="form-control">
                                        <option value="">All</option>
                                        <option value="public" {{ request('visibility') == 'public' ? 'selected' : '' }}>
                                            Public Only
                                        </option>
                                        <option value="private" {{ request('visibility') == 'private' ? 'selected' : '' }}>
                                            Private Only
                                        </option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Items Per Page</label>
                                    <select name="per_page" class="form-control">
                                        <option value="12" {{ request('per_page') == 12 ? 'selected' : '' }}>12</option>
                                        <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                                        <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48</option>
                                        <option value="96" {{ request('per_page') == 96 ? 'selected' : '' }}>96</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-1 d-flex align-items-end">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="{{ route('images.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-info">
                    <i class="fas fa-image"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Images</span>
                    <span class="info-box-number">{{ $totalImages }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-success">
                    <i class="fas fa-globe"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Public Images</span>
                    <span class="info-box-number">{{ $publicImages }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning">
                    <i class="fas fa-hdd"></i>
                </span>
                {{-- <div class="info-box-content">
                    <span class="info-box-text">Storage Used</span>
                    <span class="info-box-number">{{ formatBytes($totalSize) }}</span>
                </div> --}}
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-primary">
                    <i class="fas fa-folder"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Categories</span>
                    <span class="info-box-number">{{ $categories->count() }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Image Grid -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- Bulk Actions -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="btn-group" id="bulkActions" style="display: none;">
                                <button type="button" class="btn btn-default" onclick="selectAllImages()">
                                    <i class="fas fa-check-square"></i> Select All
                                </button>
                                <button type="button" class="btn btn-danger" onclick="deleteSelectedImages()">
                                    <i class="fas fa-trash"></i> Delete Selected
                                </button>
                                <button type="button" class="btn btn-info" onclick="exportSelectedImages()">
                                    <i class="fas fa-download"></i> Export Selected
                                </button>
                                <button type="button" class="btn btn-success" onclick="makeSelectedPublic()">
                                    <i class="fas fa-globe"></i> Make Public
                                </button>
                                <button type="button" class="btn btn-warning" onclick="makeSelectedPrivate()">
                                    <i class="fas fa-lock"></i> Make Private
                                </button>
                                <span class="ml-2 align-self-center" id="selectedCount">0 selected</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gallery -->
                    @if($images->count() > 0)
                    <div class="row" id="imageGallery">
                        @foreach($images as $image)
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card image-card h-100">
                                <!-- Image Checkbox -->
                                <div class="card-img-overlay p-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input image-checkbox" 
                                               id="image_{{ $image->id }}"
                                               value="{{ $image->id }}"
                                               onchange="updateBulkActions()">
                                        <label class="custom-control-label" for="image_{{ $image->id }}"></label>
                                    </div>
                                </div>
                                
                                <!-- Image -->
                                <div class="image-container" style="height: 180px; overflow: hidden;">
                                    <a href="{{ route('images.show', $image) }}" class="d-block h-100">
                                        <img src="{{ $image->thumbnail_url }}" 
                                             class="card-img-top h-100 w-100"
                                             alt="{{ $image->original_name }}"
                                             style="object-fit: cover;">
                                    </a>
                                </div>
                                
                                <!-- Badges -->
                                <div class="card-img-overlay p-1" style="top: auto; bottom: 0;">
                                    @if($image->is_public)
                                    <span class="badge badge-success float-right" title="Public">
                                        <i class="fas fa-globe"></i>
                                    </span>
                                    @else
                                    <span class="badge badge-secondary float-right" title="Private">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    @endif
                                    
                                    @if($image->item)
                                    <span class="badge badge-info float-right mr-1" title="Attached to item">
                                        <i class="fas fa-link"></i>
                                    </span>
                                    @endif
                                </div>
                                
                                <!-- Card Body -->
                                <div class="card-body p-2">
                                    <!-- Filename -->
                                    <h6 class="card-title text-truncate mb-1" title="{{ $image->original_name }}">
                                        {{ Str::limit($image->original_name, 25) }}
                                    </h6>
                                    
                                    <!-- Info -->
                                    <div class="small text-muted mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span>
                                                <i class="fas fa-calendar"></i> 
                                                {{ $image->created_at->format('M d') }}
                                            </span>
                                            @if($image->dimensions)
                                            <span>
                                                <i class="fas fa-expand-alt"></i> 
                                                {{ $image->width }}×{{ $image->height }}
                                            </span>
                                            @endif
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>
                                                <i class="fas fa-weight"></i> 
                                                {{ $image->formatted_size }}
                                            </span>
                                            <span>
                                                {{ strtoupper($image->extension) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="btn-group btn-group-sm w-100">
                                        <a href="{{ route('images.show', $image) }}" 
                                           class="btn btn-info" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ $image->url }}" 
                                           target="_blank"
                                           class="btn btn-success" 
                                           title="Open Original">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <a href="{{ route('images.download', $image) }}" 
                                           class="btn btn-primary" 
                                           title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="{{ route('images.edit', $image) }}" 
                                           class="btn btn-warning" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-danger delete-single-image" 
                                                data-id="{{ $image->id }}"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-images fa-4x text-muted mb-3"></i>
                            <h3>No Images Found</h3>
                            <p class="text-muted">
                                @if(request()->hasAny(['search', 'category', 'visibility']))
                                Try changing your filters or 
                                <a href="{{ route('images.index') }}">clear all filters</a>
                                @else
                                Upload your first image to get started
                                @endif
                            </p>
                            @if(!request()->hasAny(['search', 'category', 'visibility']))
                            <a href="{{ route('images.create') }}" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Your First Image
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <!-- Pagination -->
                    @if($images->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $images->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.image-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #dee2e6;
}

.image-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.image-container {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.empty-state {
    padding: 40px 20px;
}

.card-img-overlay .badge {
    opacity: 0.9;
    font-size: 0.7rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.custom-checkbox .custom-control-label::before,
.custom-checkbox .custom-control-label::after {
    top: 0.25rem;
    left: 0.25rem;
}
</style>
@endpush

@push('scripts')
<script>
// Bulk actions
let selectedImageIds = [];

function updateBulkActions() {
    selectedImageIds = [];
    $('.image-checkbox:checked').each(function() {
        selectedImageIds.push($(this).val());
    });
    
    const bulkActions = $('#bulkActions');
    const selectedCount = $('#selectedCount');
    
    if (selectedImageIds.length > 0) {
        bulkActions.show();
        selectedCount.text(selectedImageIds.length + ' selected');
    } else {
        bulkActions.hide();
    }
}

function selectAllImages() {
    const allChecked = $('.image-checkbox').length === $('.image-checkbox:checked').length;
    $('.image-checkbox').prop('checked', !allChecked);
    updateBulkActions();
}

function deleteSelectedImages() {
    if (selectedImageIds.length === 0) return;
    
    Swal.fire({
        title: 'Delete Images?',
        html: `Are you sure you want to delete <strong>${selectedImageIds.length}</strong> image(s)?<br><br>
               <small class="text-danger">This action cannot be undone!</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete them!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    image_ids: selectedImageIds
                },
                success: function(response) {
                    if (response.success) {
                        // Remove deleted images
                        selectedImageIds.forEach(id => {
                            $(`#image_${id}`).closest('.col-xl-2, .col-lg-3, .col-md-4, .col-sm-6').fadeOut(300, function() {
                                $(this).remove();
                            });
                        });
                        
                        selectedImageIds = [];
                        updateBulkActions();
                        
                        Swal.fire('Deleted!', response.message, 'success');
                        
                        // Update stats if needed
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to delete images', 'error');
                }
            });
        }
    });
}

function exportSelectedImages() {
    if (selectedImageIds.length === 0) return;
    
    Swal.fire({
        title: 'Export Images',
        text: 'Preparing download...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url:'',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            image_ids: selectedImageIds
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(data) {
            Swal.close();
            
            // Create download link
            const blob = new Blob([data]);
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'images-export-' + new Date().toISOString().split('T')[0] + '.zip';
            link.click();
        },
        error: function() {
            Swal.fire('Error', 'Failed to export images', 'error');
        }
    });
}

function makeSelectedPublic() {
    if (selectedImageIds.length === 0) return;
    
    $.ajax({
        url:'',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            image_ids: selectedImageIds,
            action: 'make_public'
        },
        success: function(response) {
            if (response.success) {
                // Update badges
                selectedImageIds.forEach(id => {
                    $(`#image_${id}`).closest('.image-card')
                        .find('.badge-success').removeClass('d-none');
                    $(`#image_${id}`).closest('.image-card')
                        .find('.badge-secondary').addClass('d-none');
                });
                
                Swal.fire('Updated!', response.message, 'success');
            }
        },
        error: function() {
            Swal.fire('Error', 'Failed to update images', 'error');
        }
    });
}

function makeSelectedPrivate() {
    if (selectedImageIds.length === 0) return;
    
    $.ajax({
        url:'',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            image_ids: selectedImageIds,
            action: 'make_private'
        },
        success: function(response) {
            if (response.success) {
                // Update badges
                selectedImageIds.forEach(id => {
                    $(`#image_${id}`).closest('.image-card')
                        .find('.badge-success').addClass('d-none');
                    $(`#image_${id}`).closest('.image-card')
                        .find('.badge-secondary').removeClass('d-none');
                });
                
                Swal.fire('Updated!', response.message, 'success');
            }
        },
        error: function() {
            Swal.fire('Error', 'Failed to update images', 'error');
        }
    });
}

// Delete single image
$(document).on('click', '.delete-single-image', function() {
    const imageId = $(this).data('id');
    
    Swal.fire({
        title: 'Delete Image?',
        text: "This will permanently delete the image and all its previews!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/images/' + imageId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Remove from view
                        $(`.delete-single-image[data-id="${imageId}"]`)
                            .closest('.col-xl-2, .col-lg-3, .col-md-4, .col-sm-6')
                            .fadeOut(300, function() {
                                $(this).remove();
                                // Update count
                                const totalBadge = $('.info-box-number:first');
                                const currentCount = parseInt(totalBadge.text());
                                totalBadge.text(currentCount - 1);
                            });
                            
                        Swal.fire('Deleted!', 'Image has been deleted.', 'success');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to delete image', 'error');
                }
            });
        }
    });
});
</script>
@endpush