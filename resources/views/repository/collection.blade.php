@extends('layouts.app')

@section('title', $collection->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-folder"></i> {{ $collection->name }}
                    </h3>
                    <small class="text-muted">From {{ $collection->community->name }}</small>
                </div>
                <div class="card-body">
                    <p class="lead">{{ $collection->description }}</p>
                    
                    <div class="row">
                        @foreach($items as $item)
                        <div class="col-md-4 mb-3">
                            <div class="card card-default">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('items.show', $item->id) }}">
                                            {{ $item->title }}
                                        </a>
                                    </h5>
                                    @if(isset($item->metadata['dc_creator']))
                                    <p class="card-text">
                                        <small class="text-muted">
                                            {{ implode(', ', $item->metadata['dc_creator']) }}
                                        </small>
                                    </p>
                                    @endif
                                    <p class="card-text">
                                        <small class="text-muted">
                                            {{ $item->created_at->format('M d, Y') }}
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection