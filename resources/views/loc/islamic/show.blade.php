{{-- resources/views/loc/islamic/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Import Details - #{{ $harvestLog->id }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('loc.islamic.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Harvest Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Import ID:</th>
                                            <td>#{{ $harvestLog->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                @if($harvestLog->status === 'completed')
                                                <span class="badge badge-success">Completed</span>
                                                @elseif($harvestLog->status === 'processing')
                                                <span class="badge badge-info">Processing</span>
                                                @elseif($harvestLog->status === 'failed')
                                                <span class="badge badge-danger">Failed</span>
                                                @else
                                                <span class="badge badge-secondary">{{ $harvestLog->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Started:</th>
                                            <td>{{ $harvestLog->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Completed:</th>
                                            <td>
                                                @if($harvestLog->completed_at)
                                                {{ $harvestLog->completed_at->format('Y-m-d H:i:s') }}
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Duration:</th>
                                            <td>
                                                @if($harvestLog->completed_at)
                                                {{ $harvestLog->created_at->diff($harvestLog->completed_at)->format('%Hh %Im %Ss') }}
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Imported by:</th>
                                            <td>{{ $harvestLog->user->name ?? 'Unknown' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Collection Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Source:</th>
                                            <td>Library of Congress</td>
                                        </tr>
                                        <tr>
                                            <th>Collection:</th>
                                            <td>
                                                @php
                                                    $params = json_decode($harvestLog->parameters, true) ?? [];
                                                    $collectionName = $params['collection'] ?? 'LoC Islamic Collection';
                                                @endphp
                                                {{ $collectionName }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Endpoint:</th>
                                            <td>
                                                <small class="text-truncate d-inline-block" style="max-width: 200px;">
                                                    {{ $harvestLog->endpoint }}
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Query:</th>
                                            <td>
                                                @php
                                                    $searchQuery = $params['keyword'] ?? 'All records';
                                                @endphp
                                                "{{ $searchQuery }}"
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Import Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box bg-success">
                                                <span class="info-box-icon">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Imported</span>
                                                    <span class="info-box-number">{{ $harvestLog->imported_records }}</span>
                                                    <div class="progress">
                                                        <div class="progress-bar" style="width: {{ $harvestLog->total_records > 0 ? ($harvestLog->imported_records / $harvestLog->total_records) * 100 : 0 }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box bg-warning">
                                                <span class="info-box-icon">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Skipped</span>
                                                    <span class="info-box-number">{{ $harvestLog->skipped_records }}</span>
                                                    <div class="progress">
                                                        <div class="progress-bar" style="width: {{ $harvestLog->total_records > 0 ? ($harvestLog->skipped_records / $harvestLog->total_records) * 100 : 0 }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box bg-danger">
                                                <span class="info-box-icon">
                                                    <i class="fas fa-times-circle"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Failed</span>
                                                    <span class="info-box-number">{{ $harvestLog->failed_records }}</span>
                                                    <div class="progress">
                                                        <div class="progress-bar" style="width: {{ $harvestLog->total_records > 0 ? ($harvestLog->failed_records / $harvestLog->total_records) * 100 : 0 }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box bg-info">
                                                <span class="info-box-icon">
                                                    <i class="fas fa-percentage"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Success Rate</span>
                                                    <span class="info-box-number">
                                                        @php
                                                            $successRate = $harvestLog->total_records > 0 ? 
                                                                ($harvestLog->imported_records / $harvestLog->total_records) * 100 : 0;
                                                        @endphp
                                                        {{ number_format($successRate, 1) }}%
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Imported Items -->
                    @if($items->count() > 0)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        Imported Items ({{ $items->total() }})
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Title</th>
                                                    <th>Author</th>
                                                    <th>Type</th>
                                                    <th>Imported On</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($items as $item)
                                                <tr>
                                                    <td>{{ $item->id }}</td>
                                                    <td>
                                                        <strong>{{ $item->title }}</strong>
                                                        @if($item->description)
                                                        <p class="small text-muted mb-0">
                                                            {{ Str::limit($item->description, 80) }}
                                                        </p>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->author ?? 'Unknown' }}</td>
                                                    <td>
                                                        <span class="badge badge-info">{{ $item->item_type ?? 'Unknown' }}</span>
                                                    </td>
                                                    <td>{{ $item->import_date->format('Y-m-d H:i') }}</td>
                                                    <td>
                                                        <a href="{{ route('items.show', $item->id) }}" 
                                                           class="btn btn-sm btn-info" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        {{ $items->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Error Information -->
                    @if($harvestLog->error_message)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="card-title">
                                        <i class="fas fa-exclamation-triangle"></i> Error Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-3 border rounded">{{ $harvestLog->error_message }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.info-box {
    min-height: 90px;
    margin-bottom: 0;
}
.info-box-icon {
    width: 70px;
    font-size: 1.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush