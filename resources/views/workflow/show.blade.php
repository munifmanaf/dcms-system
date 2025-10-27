@extends('layouts.app')

@section('page_title', 'Workflow - ' . $item->title)

@section('content_header')
<style>
.workflow-progress-container {
    position: relative;
    padding: 20px;
}

.progress-track-container {
    position: relative;
    padding: 40px 20px;
}

.progress-track {
    display: flex;
    justify-content: space-between;
    position: relative;
    max-width: 1000px;
    margin: 0 auto;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    flex: 1;
    z-index: 2;
}

.step-connector {
    position: absolute;
    top: 25px;
    left: 50%;
    width: 100%;
    height: 4px;
    z-index: 1;
}

.connector-line {
    position: absolute;
    width: 100%;
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
}

.connector-progress {
    position: absolute;
    width: 0%;
    height: 4px;
    background: var(--step-color);
    border-radius: 2px;
    transition: width 1s ease-in-out;
}

.connector-progress.completed {
    width: 100%;
}

.step-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
}

.step-icon-completed {
    width: 50px;
    height: 50px;
    background: var(--step-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2em;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.step-icon-current {
    width: 60px;
    height: 60px;
    background: white;
    border: 3px solid var(--step-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--step-color);
    font-size: 1.3em;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    position: relative;
}

.step-icon-future {
    width: 50px;
    height: 50px;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 1.1em;
}

.pulse-animation {
    position: absolute;
    top: -5px;
    left: -5px;
    right: -5px;
    bottom: -5px;
    border: 2px solid var(--step-color);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.7;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.step-label {
    text-align: center;
    max-width: 120px;
}

.step-title {
    font-weight: 600;
    font-size: 0.9em;
    margin-bottom: 4px;
    color: #495057;
    transition: all 0.3s ease;
}

.step-title.current {
    color: var(--step-color);
    font-weight: 700;
    transform: scale(1.05);
}

.step-description {
    font-size: 0.75em;
    color: #6c757d;
    line-height: 1.3;
}

.current-indicator {
    font-size: 0.7em;
    color: var(--step-color);
    font-weight: 600;
    margin-top: 5px;
}

/* Progress completed states */
.progress-step.completed .step-icon-future {
    background: var(--step-color);
    color: white;
    border-color: var(--step-color);
}

.progress-step.completed .step-title {
    color: var(--step-color);
}

/* Stats Cards */
.stat-card {
    padding: 15px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #f1f3f4;
}

.stat-number {
    font-size: 1.8em;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.8em;
    color: #6c757d;
    font-weight: 500;
}

/* Next Action Card */
.next-action-card .card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.next-action-card .card-body {
    background: transparent;
}

.next-step-badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .progress-track {
        flex-direction: column;
        align-items: flex-start;
        padding-left: 70px;
    }

    .progress-step {
        flex-direction: row;
        margin-bottom: 30px;
        width: 100%;
    }

    .step-connector {
        top: 50%;
        left: -35px;
        width: 30px;
        height: 2px;
    }

    .connector-line,
    .connector-progress {
        height: 2px;
    }

    .step-circle {
        margin-bottom: 0;
        margin-right: 15px;
        width: 50px;
        height: 50px;
    }

    .step-icon-current {
        width: 50px;
        height: 50px;
    }

    .step-label {
        text-align: left;
        max-width: none;
        flex: 1;
    }

    .progress-stats .row {
        margin: 0 -5px;
    }

    .progress-stats .col-6 {
        padding: 0 5px;
    }

    .stat-card {
        padding: 10px;
    }

    .stat-number {
        font-size: 1.4em;
    }
}

/* Hover Effects */
.progress-step:hover .step-circle {
    transform: scale(1.1);
}

.progress-step:hover .step-title {
    color: var(--step-color);
}
</style>
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-tasks mr-2"></i>Workflow: {{ $item->title }}
        </h1>
        <a href="{{ route('items.show', $item) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back to Item
        </a>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Workflow Progress -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-project-diagram mr-2"></i>Workflow Progress
                </h3>
            </div>
            <div class="card-body">
                <!-- Workflow Steps -->
                <div class="steps">
                    @include('workflow.partials.progress-steps', ['item' => $item])
                </div>

                <!-- Current Status -->
                <div class="current-status mt-4 p-3 bg-light rounded">
                    <h5 class="mb-3">
                        <i class="fas fa-info-circle mr-2"></i>Current Status
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <span class="badge badge-{{ $item->getWorkflowStatusColor() }} ml-2">
                                {{ $item->getWorkflowStatusText() }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <strong>Last Updated:</strong>
                            <span class="ml-2">{{ $item->updated_at->format('M j, Y g:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workflow Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-play-circle mr-2"></i>Available Actions
                </h3>
            </div>
            <div class="card-body">
                @include('workflow.partials.actions', ['item' => $item])
            </div>
        </div>

        <!-- Workflow History -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>Workflow History
                </h3>
            </div>
            <div class="card-body">
                @include('workflow.partials.history', ['item' => $item])
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Item Summary -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt mr-2"></i>Item Summary
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Title:</strong>
                    <p class="mb-1">{{ $item->title }}</p>
                </div>
                
                <div class="mb-3">
                    <strong>Description:</strong>
                    <p class="mb-1 text-muted">{{ Str::limit($item->description, 100) }}</p>
                </div>

                <div class="mb-3">
                    <strong>Collection:</strong>
                    <p class="mb-1">{{ $item->collection->name }}</p>
                </div>

                <div class="mb-3">
                    <strong>File:</strong>
                    @if($item->file_name)
                    <p class="mb-1">
                        <i class="fas fa-file mr-1"></i>{{ $item->file_name }}
                    </p>
                    @else
                    <p class="mb-1 text-muted">No file attached</p>
                    @endif
                </div>

                <div class="mb-3">
                    <strong>Categories:</strong>
                    <div class="mt-1">
                        @foreach($item->categories as $category)
                        <span class="badge badge-secondary">{{ $category->name }}</span>
                        @endforeach
                    </div>
                </div>

                {{-- In the Item Summary section --}}
                <div class="mb-3">
                    <strong>Created By:</strong>
                    <p class="mb-1">
                        @if($item->user)
                            {{ $item->user->name }} ({{ $item->user->email }})
                        @else
                            <span class="text-muted">Unknown User</span>
                        @endif
                    </p>
                </div>

                <div class="mb-3">
                    <strong>Approved By:</strong>
                    <p class="mb-1">
                        @if($item->approvedByUser)
                            {{ $item->approvedByUser->name }} 
                            @if($item->approved_at)
                                on {{ $item->approved_at->format('M j, Y g:i A') }}
                            @endif
                        @else
                            <span class="text-muted">Not approved yet</span>
                        @endif
                    </p>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('items.edit', $item) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit mr-1"></i> Edit Item
                    </a>
                    <a href="{{ route('items.show', $item) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye mr-1"></i> View Details
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-2"></i>Quick Actions
                </h3>
            </div>
            <div class="card-body">
                @include('workflow.partials.quick-actions', ['item' => $item])
            </div>
        </div>
    </div>
</div>
@stop
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress connectors
    const progressSteps = document.querySelectorAll('.progress-step.completed');
    
    progressSteps.forEach((step, index) => {
        const connector = step.querySelector('.connector-progress');
        if (connector) {
            setTimeout(() => {
                connector.style.width = '100%';
            }, index * 300);
        }
    });

    // Add hover effects
    const steps = document.querySelectorAll('.progress-step');
    steps.forEach(step => {
        step.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        step.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Update progress percentage with animation
    const progressPercentage = {{ $item->getWorkflowProgressPercentage() }};
    const statNumber = document.querySelector('.stat-number.text-success');
    if (statNumber) {
        let current = 0;
        const increment = progressPercentage / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= progressPercentage) {
                current = progressPercentage;
                clearInterval(timer);
            }
            statNumber.textContent = Math.round(current) + '%';
        }, 30);
    }
});
</script>
@push('css')
<style>
.step-item {
    padding: 15px;
    border-left: 4px solid #e9ecef;
    margin-bottom: 10px;
    background: #f8f9fa;
    border-radius: 0 5px 5px 0;
}

.step-item.completed {
    border-left-color: #28a745;
    background: #d4edda;
}

.step-item.current {
    border-left-color: #007bff;
    background: #d1ecf1;
}

.step-item.pending {
    border-left-color: #6c757d;
    background: #f8f9fa;
}

.step-icon {
    font-size: 1.5em;
    margin-right: 10px;
}

.workflow-timeline {
    position: relative;
    padding-left: 30px;
}

.workflow-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -23px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
    border: 2px solid white;
}

.timeline-item.success::before { background: #28a745; }
.timeline-item.warning::before { background: #ffc107; }
.timeline-item.danger::before { background: #dc3545; }
.timeline-item.info::before { background: #17a2b8; }
</style>
@endpush