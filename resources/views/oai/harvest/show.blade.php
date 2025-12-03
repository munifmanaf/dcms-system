{{-- resources/views/oai/harvest/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Harvest Details #{{ $harvestLog->id }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('oai.harvest.history') }}" class="btn btn-sm btn-default">
                            <i class="fas fa-arrow-left"></i> Back to History
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Basic Information -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Harvest Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Harvest ID:</th>
                                            <td>#{{ $harvestLog->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                @switch($harvestLog->status)
                                                    @case('completed')
                                                        <span class="badge badge-success">Completed</span>
                                                        @break
                                                    @case('processing')
                                                        <span class="badge badge-info">Processing</span>
                                                        @break
                                                    @case('failed')
                                                        <span class="badge badge-danger">Failed</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ $harvestLog->status }}</span>
                                                @endswitch
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
                                                @if($harvestLog->duration)
                                                    {{ $harvestLog->duration->format('%Hh %Im %Ss') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Initiated by:</th>
                                            <td>{{ $harvestLog->user->name ?? 'Unknown' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Repository Information -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Repository Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Endpoint:</th>
                                            <td>
                                                <a href="{{ $harvestLog->endpoint }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 200px;">
                                                    {{ $harvestLog->endpoint }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Metadata Format:</th>
                                            <td>{{ $harvestLog->metadata_prefix }}</td>
                                        </tr>
                                        <tr>
                                            <th>Set/Collection:</th>
                                            <td>{{ $harvestLog->set_spec ?? 'All sets' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date Range:</th>
                                            <td>
                                                @if($harvestLog->from_date && $harvestLog->until_date)
                                                    {{ $harvestLog->from_date->format('Y-m-d') }} to {{ $harvestLog->until_date->format('Y-m-d') }}
                                                @elseif($harvestLog->from_date)
                                                    From {{ $harvestLog->from_date->format('Y-m-d') }}
                                                @elseif($harvestLog->until_date)
                                                    Until {{ $harvestLog->until_date->format('Y-m-d') }}
                                                @else
                                                    <span class="text-muted">All dates</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Row -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Harvest Statistics</h5>
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
                                                    <span class="progress-description">
                                                        Successfully imported
                                                    </span>
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
                                                    <span class="progress-description">
                                                        Already existed
                                                    </span>
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
                                                    <span class="progress-description">
                                                        Import failed
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box bg-info">
                                                <span class="info-box-icon">
                                                    <i class="fas fa-chart-line"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Success Rate</span>
                                                    <span class="info-box-number">
                                                        {{ $harvestLog->success_rate > 0 ? number_format($harvestLog->success_rate, 1) . '%' : '0%' }}
                                                    </span>
                                                    <div class="progress">
                                                        <div class="progress-bar" style="width: {{ $harvestLog->success_rate }}%"></div>
                                                    </div>
                                                    <span class="progress-description">
                                                        Based on imported/total
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Information -->
                    @if($harvestLog->error_message)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-exclamation-triangle"></i> Error Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-3 border rounded">{{ $harvestLog->error_message }}</pre>
                                    @if($harvestLog->status === 'failed' && $harvestLog->resumption_token)
                                    <div class="mt-3">
                                        <a href="{{ route('oai.harvest.resume', $harvestLog->id) }}" 
                                           class="btn btn-warning">
                                            <i class="fas fa-play"></i> Resume Harvest from Failure Point
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Imported Items -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Imported Items ({{ $items->total() }})</h5>
                                    <div class="card-tools">
                                        <a href="{{ route('items.index', ['harvest_log_id' => $harvestLog->id]) }}" 
                                           class="btn btn-sm btn-primary">
                                            View All Items
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($items->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Title</th>
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
                                                        @if($item->author)
                                                        <br>
                                                        <small class="text-muted">{{ $item->author }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info">{{ $item->item_type ?? 'Unknown' }}</span>
                                                    </td>
                                                    <td>
                                                        {{ $item->import_date->format('Y-m-d H:i') }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('items.show', $item->id) }}" 
                                                           class="btn btn-sm btn-info">
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
                                    @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        No items were imported in this harvest or all items were skipped/duplicates.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Parameters -->
                    @if($harvestLog->parameters)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Harvest Parameters</h5>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-3 border rounded">{{ json_encode($harvestLog->parameters, JSON_PRETTY_PRINT) }}</pre>
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
    min-height: 100px;
    margin-bottom: 0;
}
.info-box .info-box-icon {
    width: 80px;
    font-size: 2rem;
}
.info-box .info-box-content {
    padding: 10px;
}
</style>
@endpush