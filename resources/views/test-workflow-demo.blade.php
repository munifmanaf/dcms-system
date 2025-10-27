@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-vial mr-2"></i>
                        Workflow Status Component - Demo Mode
                    </h3>
                </div>
                <div class="card-body">
                    <p class="mb-3">Select an item to see the workflow status component in different states:</p>
                    
                    <div class="row">
                        @foreach($items as $item)
                        <div class="col-md-2 mb-2">
                            <a href="{{ route('test.workflow.status.item', $item) }}" 
                               class="btn btn-block btn-{{ $item->workflow_state === 'published' ? 'success' : ($item->workflow_state === 'rejected' ? 'danger' : 'primary') }} btn-sm">
                                {{ ucfirst($item->workflow_state) }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Item Details -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt mr-2"></i>
                        {{ $currentItem->title }}
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $currentItem->workflow_state === 'published' ? 'success' : ($currentItem->workflow_state === 'rejected' ? 'danger' : 'primary') }}">
                            {{ ucfirst($currentItem->workflow_state) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <p><strong>Description:</strong> {{ $currentItem->description }}</p>
                    <p><strong>Category:</strong> {{ $currentItem->category }}</p>
                    <p><strong>Submitter:</strong> {{ $currentItem->submitter->name ?? 'Demo User' }}</p>
                    
                    @if($currentItem->content)
                    <div class="mt-3">
                        <strong>Content Preview:</strong>
                        <div class="border p-3 rounded bg-light">
                            {{ Str::limit($currentItem->content, 200) }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Workflow Status Component -->
            @include('components.workflow-status', ['item' => $currentItem])
        </div>
    </div>
</div>
@endsection