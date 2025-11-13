@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-folder mr-2"></i>
                        Collection Performance Report
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.export', ['type' => 'collections']) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-download mr-1"></i> Export CSV
                        </a>
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-default ml-2">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Stats -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Collection Performance Overview</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Collection</th>
                                    <th>Items</th>
                                    <th>Total Downloads</th>
                                    <th>Total Views</th>
                                    <th>Avg Downloads per Item</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($collectionStats as $collection)
                                @php
                                    $performance = 'Low';
                                    $badgeClass = 'secondary';
                                    if ($collection['avg_downloads'] > 10) {
                                        $performance = 'High';
                                        $badgeClass = 'success';
                                    } elseif ($collection['avg_downloads'] > 5) {
                                        $performance = 'Medium';
                                        $badgeClass = 'warning';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $collection['title'] }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $collection['item_count'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ number_format($collection['total_downloads']) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ number_format($collection['total_views']) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $collection['avg_downloads'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $badgeClass }}">{{ $performance }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <td><strong>Totals</strong></td>
                                    <td><strong>{{ $collectionStats->sum('item_count') }}</strong></td>
                                    <td><strong>{{ number_format($collectionStats->sum('total_downloads')) }}</strong></td>
                                    <td><strong>{{ number_format($collectionStats->sum('total_views')) }}</strong></td>
                                    <td><strong>{{ $collectionStats->average('avg_downloads') > 0 ? number_format($collectionStats->average('avg_downloads'), 1) : 0 }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Details -->
    <div class="row mt-4">
        @foreach($collections as $collection)
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ Str::limit($collection->title, 25) }}</h3>
                    <div class="card-tools">
                        <span class="badge bg-primary">{{ $collection->items_count }} items</span>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $collectionStat = $collectionStats->firstWhere('id', $collection->id);
                    @endphp
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <div class="text-muted">Downloads</div>
                                <div class="h4 text-success">
                                    {{ $collectionStat ? number_format($collectionStat['total_downloads']) : 0 }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div>
                                <div class="text-muted">Views</div>
                                <div class="h4 text-info">
                                    {{ $collectionStat ? number_format($collectionStat['total_views']) : 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($collectionStat && $collectionStat['item_count'] > 0)
                    <div class="mt-3">
                        <div class="progress-group">
                            Downloads per Item
                            <span class="float-right">
                                <b>{{ $collectionStat['avg_downloads'] }}</b>
                            </span>
                            <div class="progress progress-sm">
                                @php
                                    $progress = min(100, ($collectionStat['avg_downloads'] / max(1, $collectionStats->max('avg_downloads'))) * 100);
                                @endphp
                                <div class="progress-bar bg-primary" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <small class="text-muted">
                        Created: {{ $collection->created_at->format('M d, Y') }}
                    </small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Performance Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Performance Summary</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-folder"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Collections</span>
                                    <span class="info-box-number">{{ $collections->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-file"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Items</span>
                                    <span class="info-box-number">{{ $collectionStats->sum('item_count') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-download"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Downloads</span>
                                    <span class="info-box-number">{{ number_format($collectionStats->sum('total_downloads')) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-chart-line"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Avg Performance</span>
                                    <span class="info-box-number">{{ $collectionStats->average('avg_downloads') > 0 ? number_format($collectionStats->average('avg_downloads'), 1) : 0 }}</span>
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