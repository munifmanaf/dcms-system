@extends('layouts.app')

@section('title', 'Edit Item')

@section('content_header')
    <h1>Edit Item</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <!-- resources/views/items/edit.blade.php -->
                <form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- File input with current file info -->
                    <div class="form-group">
                        <label for="file">File</label>
                        <input type="file" name="file" id="file" class="form-control-file">
                        <small class="form-text text-muted">
                            Current file: 
                            @if($item->file_path)
                                <a href="{{ Storage::url($item->file_path) }}" target="_blank">
                                    {{ $item->file_name }} ({{ number_format($item->file_size / 1024, 2) }} KB)
                                </a>
                                <br>Leave empty to keep current file.
                            @else
                                No file uploaded
                            @endif
                        </small>
                        @error('file')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Other form fields -->
                    <div class="form-group">
                        <label for="title">Title *</label>
                        <input type="text" name="title" id="title" class="form-control" 
                            value="{{ old('title', $item->title) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" 
                                rows="3">{{ old('description', $item->description) }}</textarea>
                    </div>

                    <!-- Collection selection -->
                    <div class="form-group">
                        <label for="collection_id">Collection *</label>
                        <select name="collection_id" id="collection_id" class="form-control" required>
                            <option value="">Select Collection</option>
                            @foreach($collections as $collection)
                                <option value="{{ $collection->id }}" 
                                    {{ old('collection_id', $item->collection_id) == $collection->id ? 'selected' : '' }}>
                                    {{ $collection->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Categories -->
                    <div class="form-group">
                        <label for="categories">Categories</label>
                        <select name="categories[]" id="categories" class="form-control" multiple>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ in_array($category->id, old('categories', $item->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Publication status -->
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="is_published" id="is_published" class="form-check-input" 
                                value="1" {{ old('is_published', $item->is_published) ? 'checked' : '' }}>
                            <label for="is_published" class="form-check-label">Publish this item</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Item</button>
                    <a href="{{ route('items.index') }}" class="btn btn-secondary">Cancel</a>
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
// This will work because jQuery is loaded in app.blade.php
$(document).ready(function() {
    console.log('jQuery is working! Version:', $.fn.jquery);
    
    // Add metadata field
    $('#add-metadata').on('click', function() {
        console.log('Add button clicked');
        var fieldHtml = `
            <div class="metadata-field row mb-2">
                <div class="col-md-5">
                    <input type="text" name="metadata_keys[]" class="form-control" placeholder="Field name">
                </div>
                <div class="col-md-5">
                    <input type="text" name="metadata_values[]" class="form-control" placeholder="Field value">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-metadata">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#metadata-fields').append(fieldHtml);
    });

    // Remove metadata field
    $(document).on('click', '.remove-metadata', function() {
        $(this).closest('.metadata-field').remove();
    });
});
</script>
@endpush
{{-- Test if jQuery is Loading
Create a test route to check:

php
// In routes/web.php
Route::get('/test-jquery', function() {
    return view('test-jquery');
});
Create resources/views/test-jquery.blade.php:

blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>jQuery Test</h1>
    <button id="test-btn" class="btn btn-primary">Test jQuery</button>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    console.log('jQuery version:', $.fn.jquery);
    
    $('#test-btn').click(function() {
        alert('jQuery is working! Version: ' + $.fn.jquery);
    });
});
</script>
@endpush --}}
