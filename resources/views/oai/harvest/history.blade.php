{{-- resources/views/oai/harvest/history.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Harvest History
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('oai.harvest.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-cloud-download-alt"></i> New Harvest
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" class="form-inline">
                                <div class="form-group mr-2">
                                    <label for="status" class="mr-2">Status:</label>
                                    <select name="status" id="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                        <option value="">All</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <label for="date_from" class="mr-2">From:</label>
                                    <input type="date" name="date_from" id="date_from" 
                                           class="form-control form-control-sm" 
                                           value="{{ request('date_from') }}"
                                           onchange="this.form.submit()">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="date_to" class="mr-2">To:</label>
                                    <input type="date" name="date_to" id="date_to" 
                                           class="form-control form-control-sm" 
                                           value="{{ request('date_to') }}"
                                           onchange="this.form.submit()">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <a href="{{ route('oai.harvest.history') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Repository</th>
                                    <th>Date</th>
                                    <th>Records</th>
                                    <th>Status</th>
                                    <th>Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($harvestLogs as $log)
                                <tr>
                                    <td>#{{ $log->id }}</td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;">
                                            <strong>{{ $log->metadata_prefix }}</strong><br>
                                            <small class="text-muted">{{ Str::limit($log->endpoint, 40) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div>Start: {{ $log->created_at->format('Y-m-d H:i') }}</div>
                                            @if($log->completed_at)
                                            <div>End: {{ $log->completed_at->format('Y-m-d H:i') }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="badge badge-success">{{ $log->imported_records }}</span>
                                            <span class="badge badge-warning">{{ $log->skipped_records }}</span>
                                            <span class="badge badge-danger">{{ $log->failed_records }}</span>
                                            <small class="text-muted">Total: {{ $log->total_records }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @switch($log->status)
                                            @case('completed')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Completed
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $log->success_rate > 0 ? number_format($log->success_rate, 1) . '%' : '0%' }}
                                                </small>
                                                @break
                                            @case('processing')
                                                <span class="badge badge-info">
                                                    <i class="fas fa-spinner fa-spin"></i> Processing
                                                </span>
                                                @break
                                            @case('failed')
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times-circle"></i> Failed
                                                </span>
                                                <br>
                                                @if($log->error_message)
                                                <small class="text-danger" title="{{ $log->error_message }}">
                                                    {{ Str::limit($log->error_message, 30) }}
                                                </small>
                                                @endif
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ $log->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($log->duration)
                                            {{ $log->duration->format('%Hh %Im %Ss') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('oai.harvest.show', $log->id) }}" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($log->status === 'processing' && $log->resumption_token)
                                        <a href="{{ route('oai.harvest.resume', $log->id) }}" 
                                           class="btn btn-sm btn-warning" title="Resume Harvest">
                                            <i class="fas fa-play"></i>
                                        </a>
                                        @endif
                                        @if($log->items_count > 0)
                                        <a href="{{ route('items.index', ['harvest_log_id' => $log->id]) }}" 
                                           class="btn btn-sm btn-success" title="View Imported Items">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> No harvest history found.
                                            <a href="{{ route('oai.harvest.index') }}" class="btn btn-sm btn-primary ml-2">
                                                Start Your First Harvest
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            {{ $harvestLogs->links() }}
                        </div>
                    </div>

                    <!-- Statistics Card -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-bar"></i> Harvest Statistics
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info">
                                                    <i class="fas fa-database"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Harvests</span>
                                                    <span class="info-box-number">
                                                        {{ $totalHarvests }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Successful</span>
                                                    <span class="info-box-number">
                                                        {{ $successfulHarvests }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-primary">
                                                    <i class="fas fa-file-import"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Imported</span>
                                                    <span class="info-box-number">
                                                        {{ $totalImported }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning">
                                                    <i class="fas fa-percentage"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Success Rate</span>
                                                    <span class="info-box-number">
                                                        {{ $successRate > 0 ? number_format($successRate, 1) . '%' : '0%' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

@push('styles')
<style>
.info-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: 0.25rem;
    background-color: #fff;
    display: flex;
    margin-bottom: 1rem;
    min-height: 80px;
    padding: .5rem;
    position: relative;
    width: 100%;
}
.info-box-icon {
    border-radius: 0.25rem;
    align-items: center;
    display: flex;
    font-size: 1.875rem;
    justify-content: center;
    text-align: center;
    width: 70px;
}
.info-box-content {
    flex: 1;
    padding: 5px 10px;
}
.info-box-text {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-transform: uppercase;
    font-weight: 400;
    font-size: 14px;
}
.info-box-number {
    display: block;
    font-weight: 700;
    font-size: 18px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-refresh if any harvest is processing
    let hasProcessing = {{ $harvestLogs->contains('status', 'processing') ? 'true' : 'false' }};
    
    if (hasProcessing) {
        // Refresh every 10 seconds if processing
        setTimeout(function() {
            window.location.reload();
        }, 10000);
        
        // Show refresh notification
        toastr.info('Auto-refresh enabled for processing harvests', 'Info', {
            timeOut: 8000
        });
    }
});
</script>
@endpush