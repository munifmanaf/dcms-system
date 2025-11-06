@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_items'] }}</h3>
                    <p>Total Items</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <a href="{{ route('items.index') }}" class="small-box-footer">View All <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['published_items'] }}</h3>
                    <p>Published Items</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('repository.index') }}" class="small-box-footer">View Public <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['pending_items'] }}</h3>
                    <p>Pending Review</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('items.index') }}?status=pending_review" class="small-box-footer">Review Items <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['total_collections'] }}</h3>
                    <p>Collections</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder"></i>
                </div>
                <a href="{{ route('collections.index') }}" class="small-box-footer">Manage Collections <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Items</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                @foreach($recentItems as $item)
                                <tr>
                                    <td style="width: 10%">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                    </td>
                                    <td>
                                        <a href="{{ route('items.edit', $item->id) }}" class="font-weight-bold">
                                            {{ $item->title }}
                                        </a>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-folder"></i> 
                                            {{ $item->collection->name }} • 
                                            <i class="fas fa-users"></i>
                                            {{ $item->collection->community->name }}
                                        </small>
                                    </td>
                                    <td style="width: 15%">
                                        @if($item->status == 'published')
                                            <span class="badge bg-success">Published</span>
                                        @elseif($item->status == 'pending_review')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td style="width: 15%" class="text-right">
                                        <small class="text-muted">
                                            {{ $item->created_at->format('M d') }}
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('items.create') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-plus"></i> Add New Item
                    </a>
                    <a href="{{ route('repository.index') }}" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-eye"></i> View Public Repository
                    </a>
                    <a href="{{ route('collections.index') }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-folder"></i> Manage Collections
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection