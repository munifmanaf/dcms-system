@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>
                        DCMS Analytics Dashboard
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.export', ['type' => 'usage']) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-download mr-1"></i> Export Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($stats['total_items']) }}</h3>
                    <p>Total Items</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file"></i>
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
                    <h3>{{ number_format($stats['total_downloads']) }}</h3>
                    <p>Total Downloads</p>
                </div>
                <div class="icon">
                    <i class="fas fa-download"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($stats['total_views']) }}</h3>
                    <p>Total Views</p>
                </div>
                <div class="icon">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['popular_items'] as $item)
                                <tr>
                                    <td>{{ Str::limit($item->title, 40) }}</td>
                                    <td><span class="badge bg-primary">{{ $item->download_count }}</span></td>
                                    <td><span class="badge bg-secondary">{{ $item->view_count }}</span></td>
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
                    <h3 class="card-title">Recent Items</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Added</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['recent_items'] as $item)
                                <tr>
                                    <td>{{ Str::limit($item->title, 40) }}</td>
                                    <td>{{ $item->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($item->is_published)
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <span class="badge bg-warning">Draft</span>
                                        @endif
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

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Reports</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('reports.usage') }}" class="btn btn-info btn-block mb-3">
                                <i class="fas fa-chart-line mr-2"></i>Usage Statistics
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('reports.collections') }}" class="btn btn-success btn-block mb-3">
                                <i class="fas fa-folder mr-2"></i>Collection Reports
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('items.search') }}" class="btn btn-warning btn-block mb-3">
                                <i class="fas fa-search mr-2"></i>Advanced Search
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection