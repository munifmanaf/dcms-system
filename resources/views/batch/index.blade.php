@extends('layouts.app')

@section('title', 'Batch Operations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-layer-group"></i> Batch Operations
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Export Section -->
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fas fa-download"></i> Export Items
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Export selected items to CSV format with full metadata.</p>
                                    
                                    <form action="{{ route('batch.export') }}" method="POST" id="exportForm">
                                        @csrf
                                        <div class="form-group">
                                            <label>Select Items to Export:</label>
                                            <select name="item_ids[]" id="exportItems" class="form-control select2" multiple required>
                                                @foreach($items as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->title }} ({{ $item->collection->name }})
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-file-export"></i> Export to CSV
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Import Section -->
                        <div class="col-md-6">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fas fa-upload"></i> Import Items
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Import items from CSV file. <a href="{{ asset('templates/import_template.csv') }}">Download template</a></p>
                                    
                                    <form action="{{ route('batch.import') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <label for="collection_id">Target Collection:</label>
                                            <select name="collection_id" id="collection_id" class="form-control" required>
                                                <option value="">Select Collection</option>
                                                @foreach($collections as $collection)
                                                <option value="{{ $collection->id }}">
                                                    {{ $collection->community->name }} - {{ $collection->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="csv_file">CSV File:</label>
                                            <input type="file" name="csv_file" class="form-control-file" accept=".csv,.txt" required>
                                            <small class="text-muted">Max file size: 1MB</small>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-file-import"></i> Import Items
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Status Update -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fas fa-sync-alt"></i> Bulk Status Update
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('batch.status-update') }}" method="POST" id="statusForm">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Select Items:</label>
                                                    <select name="item_ids[]" id="statusItems" class="form-control select2" multiple required>
                                                        @foreach($items as $item)
                                                        <option value="{{ $item->id }}">
                                                            {{ $item->title }} - {{ ucfirst($item->workflow_state) }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>New Status:</label>
                                                    <select name="workflow_state" class="form-control" required>
                                                        <option value="draft">Draft</option>
                                                        <option value="pending_review">Pending Review</option>
                                                        <option value="published">Published</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-tasks"></i> Update Status
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="row mt-4">
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-info">
                                <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Items</span>
                                    <span class="info-box-number">{{ $totalItems }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-success">
                                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Published</span>
                                    <span class="info-box-number">{{ $publishedItems }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-warning">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pending Review</span>
                                    <span class="info-box-number">{{ $pendingItems }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-secondary">
                                <span class="info-box-icon"><i class="fas fa-edit"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Draft</span>
                                    <span class="info-box-number">{{ $draftItems }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container .select2-selection--multiple {
    min-height: 38px;
}
.info-box {
    cursor: pointer;
    transition: transform 0.2s;
}
.info-box:hover {
    transform: translateY(-2px);
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select items...",
        allowClear: true
    });

    // Auto-select all items for export
    $('#exportForm').on('submit', function() {
        if ($('#exportItems').val().length === 0) {
            alert('Please select at least one item to export');
            return false;
        }
    });

    // Show confirmation for bulk status update
    $('#statusForm').on('submit', function() {
        const selectedItems = $('#statusItems').val();
        if (!selectedItems || selectedItems.length === 0) {
            alert('Please select items to update');
            return false;
        }
        return confirm(`Are you sure you want to update ${selectedItems.length} items?`);
    });
});
</script>
@endsection