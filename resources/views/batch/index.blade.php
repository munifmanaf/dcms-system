@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-layer-group mr-2"></i>
                        Batch Operations Dashboard
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Manage large-scale data operations efficiently with batch processing tools.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Export Card -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-export mr-2"></i>
                        Export Data
                    </h3>
                </div>
                <div class="card-body">
                    <p>Export collections, items, and users in multiple formats.</p>
                    <ul class="mb-3">
                        <li>CSV and Excel formats</li>
                        <li>Collection-specific exports</li>
                        <li>Full metadata included</li>
                    </ul>
                    <a href="{{ route('batch.export.form') }}" class="btn btn-primary">
                        <i class="fas fa-download mr-1"></i> Start Export
                    </a>
                </div>
            </div>
        </div>

        <!-- Import Card -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-import mr-2"></i>
                        Import Data
                    </h3>
                </div>
                <div class="card-body">
                    <p>Bulk import data from CSV or Excel files.</p>
                    <ul class="mb-3">
                        <li>Support for CSV/Excel files</li>
                        <li>Collection mapping</li>
                        <li>Validation & error reporting</li>
                    </ul>
                    <a href="{{ route('batch.import.form') }}" class="btn btn-success">
                        <i class="fas fa-upload mr-1"></i> Start Import
                    </a>
                </div>
            </div>
        </div>

        <!-- Bulk Update Card -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>
                        Bulk Update
                    </h3>
                </div>
                <div class="card-body">
                    <p>Apply mass updates to multiple items at once.</p>
                    <ul class="mb-3">
                        <li>Status updates</li>
                        <li>Publish/unpublish</li>
                        <li>Collection transfers</li>
                    </ul>
                    <a href="{{ route('batch.bulk-update.form') }}" class="btn btn-warning">
                        <i class="fas fa-cogs mr-1"></i> Bulk Update
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection