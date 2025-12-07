{{-- resources/views/images/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Upload Image')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cloud-upload-alt"></i> Upload New Image
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('images.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Gallery
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(request()->has('batch'))
                    <!-- Batch Upload -->
                    @include('images.partials.batch-upload')
                    @else
                    <!-- Single Upload -->
                    @include('images.partials.single-upload')
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.upload-area {
    border: 3px dashed #dee2e6;
    border-radius: 10px;
    padding: 40px 20px;
    text-align: center;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s;
}

.upload-area:hover {
    border-color: #007bff;
    background: #e9f7fe;
}

.upload-area.dragover {
    border-color: #28a745;
    background: #e8f5e9;
}

#previewContainer img {
    max-height: 200px;
    border-radius: 5px;
}

.progress {
    height: 25px;
}

.upload-step {
    padding: 15px;
    border-left: 4px solid #dee2e6;
    margin-bottom: 15px;
}

.upload-step.active {
    border-left-color: #007bff;
    background: #e9f7fe;
}

.upload-step.completed {
    border-left-color: #28a745;
    background: #e8f5e9;
}
</style>
@endpush

@push('scripts')
<script>
// Single upload scripts
@if(!request()->has('batch'))
$(document).ready(function() {
    // Drag and drop
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('image');
    
    if (uploadArea) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            uploadArea.classList.add('dragover');
        }
        
        function unhighlight() {
            uploadArea.classList.remove('dragover');
        }
        
        uploadArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                fileInput.files = files;
                previewImage(fileInput);
            }
        }
        
        uploadArea.addEventListener('click', () => fileInput.click());
    }
    
    // Preview image
    window.previewImage = function(input) {
        const preview = document.getElementById('imagePreview');
        const container = document.getElementById('previewContainer');
        const fileInfo = document.getElementById('fileInfo');
        const uploadAreaText = document.getElementById('uploadAreaText');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.style.display = 'block';
                
                // Update file info
                fileInfo.innerHTML = `
                    <strong>${file.name}</strong><br>
                    ${(file.size / 1024 / 1024).toFixed(2)} MB · ${file.type}
                `;
                
                // Update upload area text
                if (uploadAreaText) {
                    uploadAreaText.innerHTML = `
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <h5>File Selected</h5>
                        <p class="text-muted">${file.name}</p>
                    `;
                }
            }
            
            reader.readAsDataURL(file);
            
            // Update step
            $('.upload-step').eq(0).removeClass('active').addClass('completed');
            $('.upload-step').eq(1).addClass('active');
        }
    }
    
    // Category suggestions
    const categoryInput = document.getElementById('category');
    if (categoryInput) {
        // You can add autocomplete here
        // For example, fetch categories via AJAX
    }
    
    // Handle form submission
    $('#uploadForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const uploadBtn = $('#uploadButton');
        const progressBar = $('#uploadProgress .progress-bar');
        
        // Update step
        $('.upload-step').eq(1).removeClass('active').addClass('completed');
        $('.upload-step').eq(2).addClass('active');
        
        // Show progress
        $('#uploadProgress').show();
        uploadBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
        
        $.ajax({
            url: '{{ route("images.store") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        progressBar.css('width', percent + '%')
                                  .text(percent + '%');
                                  
                        // Update step text
                        $('#uploadStepText').text(`Uploading: ${percent}%`);
                    }
                });
                
                return xhr;
            },
            success: function(response) {
                if (response.success) {
                    // Update step
                    $('.upload-step').eq(2).removeClass('active').addClass('completed');
                    
                    // Show success
                    $('#uploadResult').html(`
                        <div class="alert alert-success">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x mr-3"></i>
                                <div>
                                    <h4 class="mb-2">Upload Successful!</h4>
                                    <p class="mb-3">${response.message}</p>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <img src="${response.image.thumbnail_url}" 
                                                 class="img-fluid rounded border">
                                        </div>
                                        <div class="col-md-8">
                                            <table class="table table-sm">
                                                <tr>
                                                    <th>Name:</th>
                                                    <td>${response.image.name}</td>
                                                </tr>
                                                <tr>
                                                    <th>Size:</th>
                                                    <td>${response.image.size}</td>
                                                </tr>
                                                <tr>
                                                    <th>Dimensions:</th>
                                                    <td>${response.image.dimensions || 'N/A'}</td>
                                                </tr>
                                                <tr>
                                                    <th>Path:</th>
                                                    <td><small>${response.image.path}</small></td>
                                                </tr>
                                            </table>
                                            <div class="mt-3">
                                                <a href="/images/${response.image.id}" class="btn btn-info">
                                                    <i class="fas fa-eye"></i> View Details
                                                </a>
                                                <a href="${response.image.url}" target="_blank" class="btn btn-success">
                                                    <i class="fas fa-external-link-alt"></i> Open
                                                </a>
                                                <a href="{{ route('images.index') }}" class="btn btn-primary">
                                                    <i class="fas fa-images"></i> Back to Gallery
                                                </a>
                                                <button onclick="uploadAnother()" class="btn btn-secondary">
                                                    <i class="fas fa-plus"></i> Upload Another
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                    
                } else {
                    $('#uploadResult').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            ${response.message}
                        </div>
                    `);
                    
                    $('.upload-step').eq(2).removeClass('active');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Upload failed. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                }
                
                $('#uploadResult').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        ${errorMessage}
                    </div>
                `);
                
                $('.upload-step').eq(2).removeClass('active');
            },
            complete: function() {
                uploadBtn.prop('disabled', false).html('<i class="fas fa-upload"></i> Upload Image');
                $('#uploadProgress').hide();
                progressBar.css('width', '0%').text('');
            }
        });
    });
    
    // Reset form
    window.resetForm = function() {
        $('#uploadForm')[0].reset();
        $('#previewContainer').hide();
        $('#uploadResult').empty();
        $('#uploadAreaText').html(`
            <i class="fas fa-cloud-upload-alt fa-4x text-muted mb-3"></i>
            <h5>Drag & Drop or Click to Upload</h5>
            <p class="text-muted">Supported: JPG, PNG, GIF, WebP, BMP, TIFF</p>
            <p class="text-muted"><small>Max file size: 50MB</small></p>
        `);
        
        // Reset steps
        $('.upload-step').removeClass('active completed');
        $('.upload-step').eq(0).addClass('active');
    }
    
    // Upload another
    window.uploadAnother = function() {
        resetForm();
        $('html, body').animate({
            scrollTop: 0
        }, 500);
    }
});
@endif
</script>
@endpush