{{-- resources/views/oai/harvest/selected-preview.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-check-circle"></i> Selected Records Preview
                        <small class="text-muted">{{ $totalSelected }} records selected</small>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <i class="fas fa-info-circle"></i>
                        <strong>Ready to import!</strong> Review your selected records below, then choose a collection and start the import.
                    </div>

                    <div class="selected-records-preview">
                        @foreach($selectedRecords as $record)
                        <div class="record-card mb-3 border rounded p-3">
                            <div class="row">
                                <div class="col-md-1">
                                    <span class="badge badge-primary">{{ $loop->iteration }}</span>
                                </div>
                                <div class="col-md-11">
                                    <h6 class="mb-1">{{ $record['metadata']['title'][0] ?? 'Untitled' }}</h6>
                                    <div class="row small text-muted">
                                        @if(isset($record['metadata']['creator']))
                                        <div class="col-md-4">
                                            <strong>Creator:</strong>
                                            @if(is_array($record['metadata']['creator']))
                                            {{ implode(', ', array_slice($record['metadata']['creator'], 0, 2)) }}
                                            @else
                                            {{ $record['metadata']['creator'] }}
                                            @endif
                                        </div>
                                        @endif
                                        @if(isset($record['metadata']['date'][0]))
                                        <div class="col-md-3">
                                            <strong>Date:</strong> {{ $record['metadata']['date'][0] }}
                                        </div>
                                        @endif
                                        @if(isset($record['metadata']['type'][0]))
                                        <div class="col-md-3">
                                            <strong>Type:</strong> {{ $record['metadata']['type'][0] }}
                                        </div>
                                        @endif
                                    </div>
                                    @if(isset($record['metadata']['description'][0]))
                                    <p class="mb-1 small">
                                        {{ Str::limit($record['metadata']['description'][0], 150) }}
                                    </p>
                                    @endif
                                    <small class="text-muted">
                                        <i class="fas fa-hashtag"></i> {{ Str::limit($record['identifier'], 70) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <form action="{{ route('oai.harvest.harvest-selected') }}" method="POST">
                        @csrf
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Import Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="collection_id">Target Collection *</label>
                                            <select name="collection_id" id="collection_id" class="form-control" required>
                                                <option value="">Select a collection...</option>
                                                @foreach($collections as $collection)
                                                <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="import_mode">Import Mode</label>
                                            <select name="import_mode" id="import_mode" class="form-control">
                                                <option value="new_only" selected>Import only new records</option>
                                                <option value="update_existing">Update existing records</option>
                                            </select>
                                            <small class="form-text text-muted">
                                                "Update existing" will update metadata if record already exists
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Import Actions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" 
                                                       name="auto_publish" id="auto_publish" checked>
                                                <label class="custom-control-label" for="auto_publish">
                                                    Auto-publish imported items
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" 
                                                       name="send_notification" id="send_notification">
                                                <label class="custom-control-label" for="send_notification">
                                                    Send notification when complete
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                                <i class="fas fa-cloud-upload-alt"></i> Import {{ $totalSelected }} Selected Records
                                            </button>
                                            
                                            <a href="{{ route('oai.harvest.search') }}" 
                                               class="btn btn-outline-secondary btn-block mt-2">
                                                <i class="fas fa-arrow-left"></i> Back to Search
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie"></i> Import Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span>Total Selected:</span>
                                <span class="badge badge-primary">{{ $totalSelected }}</span>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span>Expected Import Time:</span>
                                <span>{{ ceil($totalSelected * 0.5) }} seconds</span>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span>Repository:</span>
                                <span class="text-truncate" style="max-width: 150px;">
                                    {{ session('search_results.endpoint') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Tip:</strong> You can always import more records from the same search by going back.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.record-card {
    background-color: #f8f9fa;
    transition: all 0.2s;
}
.record-card:hover {
    background-color: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@endpush