{{-- resources/views/images/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upload Image</h3>
                </div>
                <div class="card-body">
                    <form id="image-upload-form" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group">
                            <label for="image">Select Image</label>
                            <div class="custom-file">
                                <input type="file" 
                                       class="custom-file-input" 
                                       id="image" 
                                       name="image"
                                       accept="image/*">
                                <label class="custom-file-label" for="image">Choose file</label>
                            </div>
                            <small class="form-text text-muted">
                                Maximum file size: 5MB. Allowed formats: JPG, PNG, GIF, WebP
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="add_watermark" 
                                       name="add_watermark" 
                                       value="1">
                                <label class="custom-control-label" for="add_watermark">
                                    Add watermark to image
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div id="preview-container" class="d-none">
                                <h5>Preview</h5>
                                <img id="image-preview" 
                                     class="img-fluid rounded" 
                                     style="max-height: 300px;">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="upload-btn">
                                <i class="fas fa-upload"></i> Upload Image
                            </button>
                            <a href="{{ route('images.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                        
                        <div class="progress d-none" id="upload-progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: 0%"></div>
                        </div>
                        
                        <div id="upload-result"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Preview selected image
    $('#image').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result);
                $('#preview-container').removeClass('d-none');
            }
            
            reader.readAsDataURL(file);
        }
    });
    
    // Handle form submission
    $('#image-upload-form').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const uploadBtn = $('#upload-btn');
        const progressBar = $('#upload-progress .progress-bar');
        
        // Disable upload button
        uploadBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
        $('#upload-progress').removeClass('d-none');
        
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
                    }
                });
                
                return xhr;
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    
                    // Reset form
                    $('#image-upload-form')[0].reset();
                    $('#preview-container').addClass('d-none');
                    $('.custom-file-label').text('Choose file');
                    
                    // Redirect to image gallery
                    setTimeout(function() {
                        window.location.href = '{{ route("images.index") }}';
                    }, 1500);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Upload failed';
                
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                toastr.error(errorMessage);
            },
            complete: function() {
                uploadBtn.prop('disabled', false).html('<i class="fas fa-upload"></i> Upload Image');
                $('#upload-progress').addClass('d-none');
                progressBar.css('width', '0%').text('');
            }
        });
    });
});
</script>
@endpush