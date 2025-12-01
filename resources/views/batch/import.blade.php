@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-import mr-2"></i>
                        Import Data
                    </h3>
                </div>
                <form action="{{ route('batch.import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="data_type">Import Data Type *</label>
                            <select name="data_type" id="data_type" class="form-control" required>
                                <option value="">Select data type...</option>
                                <option value="collections">Collections</option>
                                {{-- <option value="items">Items</option> --}}
                                <option value="users">Users</option>
                            </select>
                        </div>

                        <div class="form-group" id="collection_field" style="display: none;">
                            <label for="collection_id">Target Collection *</label>
                            <select name="collection_id" id="collection_id" class="form-control">
                                <option value="">Select collection...</option>
                                @foreach($collections as $collection)
                                <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="file">Data File *</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="file" accept=".csv" required>
                                <label class="custom-file-label" for="file">Choose file (CSV)</label>
                            </div>
                            <small class="form-text text-muted">
                                Maximum file size: 10MB. Supported formats: CSV
                            </small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload mr-1"></i> Start Import
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
        collectionField.querySelector('select').required = true;
    } else {
        collectionField.style.display = 'none';
        collectionField.querySelector('select').required = false;
    }
});

// File input label
document.getElementById('file').addEventListener('change', function(e) {
    var fileName = e.target.files[0].name;
    var nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
});
</script>
@endsection