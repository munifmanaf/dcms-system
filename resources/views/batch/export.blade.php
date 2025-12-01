@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-export mr-2"></i>
                        Export Data
                    </h3>
                </div>
                <form action="{{ route('batch.export.process') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="data_type">Data Type *</label>
                            <select name="data_type" id="data_type" class="form-control" required>
                                <option value="">Select data type...</option>
                                <option value="collections">Collections</option>
                                <option value="items">Items</option>
                                <option value="users">Users</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="format">Export Format *</label>
                            <select name="format" id="format" class="form-control" required>
                                <option value="csv">CSV</option>
                                <option value="xlsx">Excel</option>
                            </select>
                        </div>

                        <div class="form-group" id="collection_field" style="display: none;">
                            <label for="collection_id">Collection (Optional)</label>
                            <select name="collection_id" id="collection_id" class="form-control">
                                <option value="">All Collections</option>
                                @foreach($collections as $collection)
                                <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Leave empty to export all items</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-download mr-1"></i> Generate Export
                        </button>
                        <a href="{{ route('batch.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('data_type').addEventListener('change', function() {
    const collectionField = document.getElementById('collection_field');
    if (this.value === 'items') {
        collectionField.style.display = 'block';
    } else {
        collectionField.style.display = 'none';
    }
});
</script>
@endsection