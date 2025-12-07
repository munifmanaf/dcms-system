{{-- resources/views/images/show.blade.php --}}
@extends('layouts.app')

@section('title', $image->original_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Image Column -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-image"></i> {{ $image->original_name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('images.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Gallery
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Image Display -->
                    <div class="text-center mb-4">
                        <div class="image-display-container" style="max-height: 70vh; overflow: hidden;">
                            <img src="{{ $image->screen_url }}" 
                                 id="mainImage"
                                 class="img-fluid rounded"
                                 alt="{{ $image->original_name }}"
                                 style="max-height: 70vh; cursor: zoom-in;"
                                 data-toggle="modal" 
                                 data-target="#imageModal">
                        </div>
                        
                        <!-- Image Navigation -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <a href="{{ route('images.download', $image) }}" class="btn btn-primary btn-block">
                                    <i class="fas fa-download"></i> Download Original
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ $image->url }}" target="_blank" class="btn btn-success btn-block">
                                    <i class="fas fa-external-link-alt"></i> Open in New Tab
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preview Sizes -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-th"></i> Available Sizes
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                @if($image->previews)
                                    @foreach($image->previews as $size => $preview)
                                    <div class="col-4 col-md-2 mb-3">
                                        <a href="{{ $preview['url'] }}" target="_blank" class="d-block">
                                            <div class="border rounded p-2">
                                                <img src="{{ $preview['url'] }}" 
                                                     class="img-fluid mb-2"
                                                     style="height: 80px; object-fit: cover;">
                                                <div class="small">
                                                    <strong>{{ strtoupper($size) }}</strong><br>
                                                    {{ $preview['width'] }}×{{ $preview['height'] }}
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    @endforeach
                                @endif
                                
                                <!-- Original -->
                                <div class="col-4 col-md-2 mb-3">
                                    <a href="{{ $image->url }}" target="_blank" class="d-block">
                                        <div class="border rounded p-2 border-primary">
                                            <div class="d-flex align-items-center justify-content-center mb-2" style="height: 80px;">
                                                <i class="fas fa-file-image fa-3x text-primary"></i>
                                            </div>
                                            <div class="small">
                                                <strong>ORIGINAL</strong><br>
                                                {{ $image->width }}×{{ $image->height }}
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <!-- Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i> Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <a href="{{ route('images.edit', $image) }}" class="btn btn-warning btn-block">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <form action="{{ route('images.destroy', $image) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-block delete-btn">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="mt-3">
                        <button class="btn btn-info btn-block mb-2" onclick="copyImageUrl()">
                            <i class="fas fa-copy"></i> Copy URL
                        </button>
                        
                        <button class="btn btn-secondary btn-block mb-2" onclick="shareImage()">
                            <i class="fas fa-share-alt"></i> Share
                        </button>
                        
                        @if($image->is_public)
                        <form action="{{ route('images.update', $image) }}" method="POST" class="d-inline w-100">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_public" value="0">
                            <button type="submit" class="btn btn-warning btn-block">
                                <i class="fas fa-lock"></i> Make Private
                            </button>
                        </form>
                        @else
                        <form action="{{ route('images.update', $image) }}" method="POST" class="d-inline w-100">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_public" value="1">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-globe"></i> Make Public
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Image Details Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Image Details
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Filename:</th>
                            <td>{{ $image->original_name }}</td>
                        </tr>
                        <tr>
                            <th>Stored As:</th>
                            <td>{{ $image->stored_name }}</td>
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
                            <th>Type:</th>
                            <td>{{ strtoupper($image->extension) }} ({{ $image->mime_type ?? 'N/A' }})</td>
                        </tr>
                        <tr>
                            <th>Uploaded:</th>
                            <td>{{ $image->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Modified:</th>
                            <td>{{ $image->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($image->is_public)
                                <span class="badge badge-success">Public</span>
                                @else
                                <span class="badge badge-secondary">Private</span>
                                @endif
                                
                                @if($image->is_active)
                                <span class="badge badge-primary">Active</span>
                                @else
                                <span class="badge badge-warning">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @if($image->category)
                        <tr>
                            <th>Category:</th>
                            <td>{{ $image->category }}</td>
                        </tr>
                        @endif
                        @if($image->item)
                        <tr>
                            <th>Attached to:</th>
                            <td>
                                <a href="{{ route('items.show', $image->item) }}">
                                    {{ $image->item->name ?? 'Item' }}
                                </a>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            
            <!-- Metadata Card -->
            @if($image->metadata && count($image->metadata) > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-camera"></i> EXIF Metadata
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        @foreach($image->metadata as $key => $value)
                            @if(!is_array($value) && !in_array($key, ['dimensions']))
                            <tr>
                                <th width="40%">{{ ucfirst(str_replace('_', ' ', $key)) }}:</th>
                                <td>{{ $value }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            </div>
            @endif
            
            <!-- Description & Tags -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sticky-note"></i> Description & Tags
                    </h3>
                </div>
                <div class="card-body">
                    @if($image->description)
                    <div class="mb-3">
                        <h6>Description:</h6>
                        <p>{{ $image->description }}</p>
                    </div>
                    @endif
                    
                    @if($image->tags)
                    <div>
                        <h6>Tags:</h6>
                        <div class="tags-container">
                            @foreach(explode(',', $image->tags) as $tag)
                            <span class="badge badge-info mr-1 mb-1">{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $image->original_name }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" class="img-fluid" style="max-height: 80vh;">
            </div>
            <div class="modal-footer">
                <a href="{{ $image->url }}" id="downloadModal" class="btn btn-primary" download>
                    <i class="fas fa-download"></i> Download
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.image-display-container {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 10px;
}

.tags-container {
    min-height: 30px;
}

.delete-btn {
    cursor: pointer;
}

.modal-img {
    max-height: 80vh;
}
</style>
@endpush

@push('scripts')
<script>
// Image modal
$('#imageModal').on('show.bs.modal', function() {
    const imgSrc = $('#mainImage').attr('src');
    $('#modalImage').attr('src', imgSrc);
    $('#downloadModal').attr('href', imgSrc);
});

// Delete confirmation
$('.delete-btn').click(function(e) {
    e.preventDefault();
    
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
            $(this).closest('form').submit();
        }
    });
});

// Copy URL to clipboard
function copyImageUrl() {
    const url = '{{ $image->url }}';
    
    navigator.clipboard.writeText(url).then(function() {
        Swal.fire({
            icon: 'success',
            title: 'URL Copied!',
            text: 'Image URL copied to clipboard',
            timer: 1500,
            showConfirmButton: false
        });
    }).catch(function() {
        // Fallback for older browsers
        const tempInput = document.createElement('input');
        tempInput.value = url;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        
        Swal.fire({
            icon: 'success',
            title: 'URL Copied!',
            text: 'Image URL copied to clipboard',
            timer: 1500,
            showConfirmButton: false
        });
    });
}

// Share image
function shareImage() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $image->original_name }}',
            text: 'Check out this image',
            url: '{{ $image->url }}'
        });
    } else {
        copyImageUrl();
    }
}

// Keyboard shortcuts
$(document).keydown(function(e) {
    // Escape closes modal
    if (e.keyCode === 27 && $('#imageModal').hasClass('show')) {
        $('#imageModal').modal('hide');
    }
    
    // Left/Right arrow navigation between images
    // You can implement this if you have previous/next functionality
});
</script>
@endpush