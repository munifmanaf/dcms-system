<!-- resources/views/repository/statistics.blade.php -->
@extends('layouts.app')

@section('title', 'Statistics - ' . $repository->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Repository Statistics</h3>
                    <div class="card-tools">
                        <button class="btn btn-tool" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <a href="{{ route('api.statistics.export') }}" class="btn btn-tool">
                            <i class="fas fa-download"></i> Export
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Summary Stats -->
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-info">
                                <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Items</span>
                                    <span class="info-box-number">{{ number_format($stats['total_items']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-success">
                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Communities</span>
                                    <span class="info-box-number">{{ number_format($stats['total_communities']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-warning">
                                <span class="info-box-icon"><i class="fas fa-folder"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Collections</span>
                                    <span class="info-box-number">{{ number_format($stats['total_collections']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box bg-gradient-danger">
                                <span class="info-box-icon"><i class="fas fa-download"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Downloads</span>
                                    <span class="info-box-number">{{ number_format($stats['total_downloads']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Collections -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Top Collections</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Collection</th>
                                                    <th>Items</th>
                                                    <th>% of Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($stats['top_collections'] as $collection)
                                                <tr>
                                                    <td>{{ $collection->name }}</td>
                                                    <td>{{ $collection->items_count }}</td>
                                                    <td>
                                                        <div class="progress progress-xs">
                                                            <div class="progress-bar bg-primary" 
                                                                 style="width: {{ ($collection->items_count / $stats['total_items']) * 100 }}%"></div>
                                                        </div>
                                                        <small>{{ number_format(($collection->items_count / $stats['total_items']) * 100, 1) }}%</small>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">File Type Distribution</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="fileTypeChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Growth -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Monthly Growth</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="growthChart" height="100"></canvas>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// File Type Chart
const fileTypeCtx = document.getElementById('fileTypeChart').getContext('2d');
new Chart(fileTypeCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($stats['file_types'])) !!},
        datasets: [{
            data: {!! json_encode(array_values($stats['file_types'])) !!},
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1']
        }]
    }
});

// Growth Chart
const growthCtx = document.getElementById('growthChart').getContext('2d');
new Chart(growthCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_keys($stats['monthly_growth'])) !!},
        datasets: [{
            label: 'New Items',
            data: {!! json_encode(array_values($stats['monthly_growth'])) !!},
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            fill: true
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection