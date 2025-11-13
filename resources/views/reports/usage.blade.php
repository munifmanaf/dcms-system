@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-2"></i>
                        Usage Statistics
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.export', ['type' => 'usage']) }}" class="btn btn-sm btn-success">
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

    <!-- Monthly Stats -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Downloads by Month</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Downloads</th>
                                    <th>Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usageData['downloads_by_month'] as $download)
                                @php
                                    $view = $usageData['views_by_month']->firstWhere('month', $download->month);
                                @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $download->month)->format('F Y') }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ number_format($download->downloads) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ number_format($view ? $view->views : 0) }}</span>
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
                    <h3 class="card-title">Quick Stats</h3>
                </div>
                <div class="card-body">
                    @php
                        $totalDownloads = $usageData['downloads_by_month']->sum('downloads');
                        $totalViews = $usageData['views_by_month']->sum('views');
                        $avgDownloadsPerMonth = $usageData['downloads_by_month']->average('downloads');
                    @endphp
                    <div class="info-box bg-gradient-info">
                        <span class="info-box-icon"><i class="fas fa-download"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Downloads</span>
                            <span class="info-box-number">{{ number_format($totalDownloads) }}</span>
                        </div>
                    </div>

                    <div class="info-box bg-gradient-success mt-3">
                        <span class="info-box-icon"><i class="fas fa-eye"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Views</span>
                            <span class="info-box-number">{{ number_format($totalViews) }}</span>
                        </div>
                    </div>

                    <div class="info-box bg-gradient-warning mt-3">
                        <span class="info-box-icon"><i class="fas fa-chart-bar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Avg Downloads/Month</span>
                            <span class="info-box-number">{{ number_format($avgDownloadsPerMonth, 1) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Items -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Most Downloaded Items</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Downloads</th>
                                    <th>Views</th>
                                    <th>Collection</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usageData['top_downloaded'] as $item)
                                <tr>
                                    <td>{{ Str::limit($item->title, 35) }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $item->download_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $item->view_count }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $item->collection->title ?? 'N/A' }}</small>
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
                    <h3 class="card-title">Most Viewed Items</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Views</th>
                                    <th>Downloads</th>
                                    <th>Collection</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usageData['top_viewed'] as $item)
                                <tr>
                                    <td>{{ Str::limit($item->title, 35) }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $item->view_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $item->download_count }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $item->collection->title ?? 'N/A' }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection