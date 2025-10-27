@extends('layouts.app')

@section('page_title', 'Categories')
@section('breadcrumb', 'Categories')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Document Categories</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCategoryModal">
                <i class="fas fa-plus"></i> Add Category
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($categories as $category)
            <div class="col-md-4 mb-4">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">{{ $category->name }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool edit-category" 
                                    data-toggle="modal" data-target="#editCategoryModal"
                                    data-id="{{ $category->id }}"
                                    data-name="{{ $category->name }}"
                                    data-description="{{ $category->description }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-default" title="Delete" onclick="confirmDelete(event)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($category->description)
                        <p class="card-text">{{ $category->description }}</p>
                        @else
                        <p class="card-text text-muted">No description</p>
                        @endif
                        <div class="mt-3">
                            <span class="badge badge-info">
                                <i class="fas fa-file"></i> {{ $category->documents_count }} documents
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($categories->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
            <h4>No categories found</h4>
            <p class="text-muted">Create categories to organize your documents.</p>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                Create Category
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Category</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Category Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Category</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name">Category Name *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.edit-category').click(function() {
        var categoryId = $(this).data('id');
        var categoryName = $(this).data('name');
        var categoryDescription = $(this).data('description');
        
        $('#edit_name').val(categoryName);
        $('#edit_description').val(categoryDescription);
        
        // Update form action
        $('#editCategoryForm').attr('action', '/categories/' + categoryId);
    });
});
</script>
@endpush