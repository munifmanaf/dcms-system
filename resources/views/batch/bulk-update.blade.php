@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bulk Update</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('batch.bulk-update.process') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="action">Bulk Action:</label>
                            <select name="action" id="action" class="form-control">
                                <option value="status_update">Update Status</option>
                                <option value="category_update">Update Category</option>
                                <option value="metadata_update">Update Metadata</option>
                                <option value="delete">Delete Items</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="items">Select Items (comma-separated IDs or select from list):</label>
                            <textarea name="items" id="items" class="form-control" rows="4" placeholder="Enter item IDs separated by commas"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="update_value">Update Value:</label>
                            <input type="text" name="update_value" id="update_value" class="form-control" placeholder="Enter new value">
                        </div>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Apply Bulk Update
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection