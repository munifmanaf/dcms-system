@extends('layouts.app')

@section('title', 'Repository Home')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Institutional Repository</h3>
                </div>
                <div class="card-body">
                    <p class="lead">Welcome to our digital repository containing research outputs and scholarly materials.</p>
                    
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-file"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Items</span>
                                    <span class="info-box-number">{{ \App\Models\Item::count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Communities</span>
                                    <span class="info-box-number">{{ \App\Models\Community::count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-folder"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Collections</span>
                                    <span class="info-box-number">{{ \App\Models\Collection::count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Communities & Collections -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Browse Communities & Collections</h3>
                </div>
                <div class="card-body">
                    @foreach(\App\Models\Community::with('collections')->get() as $community)
                    <div class="card card-outline card-info mb-3">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-users"></i> {{ $community->name }}
                            </h4>
                        </div>
                        <div class="card-body">
                            @foreach($community->collections as $collection)
                            <div class="mb-2">
                                <h5>
                                    <i class="fas fa-folder"></i> 
                                    <a href="{{ route('repository.collection', $collection->id) }}">
                                        {{ $collection->name }}
                                    </a>
                                </h5>
                                <p class="text-muted mb-1">{{ $collection->description }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection