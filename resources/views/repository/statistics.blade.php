@extends('layouts.app')

@section('title', 'Statistics - ' . $repository->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Repository Analytics Dashboard</h3>
                    <div class="card-tools">
                        <span class="badge bg-success">Live Data</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Summary Stats -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ number_format($stats['total_items']) }}</h3>
                                    <p>Total Items</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ number_format($stats['published_items']) }}</h3>
                                    <p>Published Items</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ number_format($stats['total_collections']) }}</h3>
                                    <p>Collections</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-folder"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ number_format($stats['total_communities']) }}</h3>
                                    <p>Communities</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row mt-4">
                        <!-- Workflow Status -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><i class="fas fa-tasks"></i> Workflow Status</h4>
                                </div>
                                <div class="card-body text-center">
                                    <canvas id="workflowChart" width="350" height="400"></canvas>
                                    <div class="mt-2">
                                        @foreach($stats['items_by_state'] as $state => $count)
                                        <span class="badge bg-{{ $state == 'published' ? 'success' : ($state == 'draft' ? 'secondary' : 'warning') }} mr-2">
                                            {{ ucfirst($state) }}: {{ $count }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Types -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><i class="fas fa-file"></i> File Types</h4>
                                </div>
                                <div class="card-body text-center">
                                    <canvas id="fileTypeChart" width="350" height="400"></canvas>
                                    <div class="mt-2">
                                        @foreach($stats['file_types'] as $type => $percentage)
                                        @if($percentage > 0)
                                        <span class="badge bg-light text-dark mr-1 mb-1">
                                            {{ $type }}: {{ $percentage }}%
                                        </span>
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Growth -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><i class="fas fa-chart-line"></i> Monthly Growth</h4>
                                </div>
                                <div class="card-body text-center">
                                    <canvas id="growthChart" width="350" height="400"></canvas>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Total: {{ array_sum($stats['monthly_growth']) }} items this year
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Tables -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><i class="fas fa-trophy"></i> Top Collections</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Collection</th>
                                                    <th>Community</th>
                                                    <th>Published Items</th>
                                                    <th>% of Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($stats['top_collections'] as $collection)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $collection->name }}</strong>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $collection->community->name }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $collection->items_count }}</span>
                                                    </td>
                                                    <td style="width: 40%">
                                                        <div class="progress progress-sm">
                                                            @if($stats['published_items'] > 0)
                                                            <div class="progress-bar bg-gradient-primary" 
                                                                 style="width: {{ ($collection->items_count / $stats['published_items']) * 100 }}%">
                                                                {{ number_format(($collection->items_count / $stats['published_items']) * 100, 1) }}%
                                                            </div>
                                                            @endif
                                                        </div>
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
                                    <h4 class="card-title"><i class="fas fa-clock"></i> Recent Additions</h4>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        @foreach($stats['recent_items'] as $item)
                                        <a href="{{ route('items.show', $item->id) }}" 
                                           class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ Str::limit($item->title, 60) }}</h6>
                                                <small class="text-muted">{{ $item->created_at->format('M d') }}</small>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-folder"></i> {{ $item->collection->name }}
                                                @if($item->collection->community)
                                                • <i class="fas fa-users"></i> {{ $item->collection->community->name }}
                                                @endif
                                            </small>
                                        </a>
                                        @endforeach
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Loading real data charts...');
    
    if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded');
        return;
    }

    // Workflow Status Chart - Real Data
    try {
        const workflowCtx = document.getElementById('workflowChart').getContext('2d');
        new Chart(workflowCtx, {
            type: 'doughnut',
            data: {
                labels: ['Published', 'Draft', 'Pending Review'],
                datasets: [{
                    data: [
                        {{ $stats['items_by_state']['published'] }},
                        {{ $stats['items_by_state']['draft'] }},
                        {{ $stats['items_by_state']['pending_review'] }}
                    ],
                    backgroundColor: ['#28a745', '#6c757d', '#ffc107']
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false
            }
        });
    } catch (e) {
        console.error('Workflow chart error:', e);
    }

    // File Type Chart - Real Data
    try {
        const fileTypeCtx = document.getElementById('fileTypeChart').getContext('2d');
        new Chart(fileTypeCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_keys($stats['file_types'])) !!},
                datasets: [{
                    data: {!! json_encode(array_values($stats['file_types'])) !!},
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#20c997']
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false
            }
        });
    } catch (e) {
        console.error('File type chart error:', e);
    }

    // Growth Chart - Real Data
    try {
        const growthCtx = document.getElementById('growthChart').getContext('2d');
        new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($stats['monthly_growth'])) !!},
                datasets: [{
                    label: 'Cumulative Items',
                    data: {!! json_encode(array_values($stats['monthly_growth'])) !!},
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    } catch (e) {
        console.error('Growth chart error:', e);
    }

    console.log('All real data charts created!');
});
</script>
@endsection