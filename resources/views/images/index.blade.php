{{-- resources/views/images/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Image Gallery</h3>
                    <div class="card-tools">
                        <a href="{{ route('images.create') }}" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Image
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="image-gallery">
                        @foreach($images as $image)
                        <div class="col-md-3 col-sm-6 mb-4 image-item" data-id="{{ $image->id }}">
                            <div class="card">
                                <img src="{{ $image->getThumbnailUrl() }}" 
                                     class="card-img-top" 
                                     alt="{{ $image->original_name }}"
                                     style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h6 class="card-title text-truncate">{{ $image->original_name }}</h6>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            {{ $image->size / 1024 }} KB<br>
                                            {{ $image->created_at->format('M d, Y') }}
                                        </small>
                                    </p>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('images.show', $image) }}" 
                                           class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('images.edit', $image) }}" 
                                           class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-danger delete-image" 
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
                    
                    @if($images->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-images fa-4x text-muted mb-3"></i>
                        <h4>No images uploaded yet</h4>
                        <p class="text-muted">Upload your first image to get started</p>
                        <a href="{{ route('images.create') }}" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Image
                        </a>
                    </div>
                    @endif
                    
                    <div class="d-flex justify-content-center">
                        {{ $images->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Delete image
    $('.delete-image').click(function() {
        const imageId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this image?')) {
            $.ajax({
                url: '/images/' + imageId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('.image-item[data-id="' + imageId + '"]').remove();
                        toastr.success('Image deleted successfully');
                    }
                },
                error: function() {
                    toastr.error('Failed to delete image');
                }
            });
        }
    });
});
</script>
@endpush