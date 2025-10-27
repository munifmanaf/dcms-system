@php
    $steps = [
        'draft' => ['icon' => 'edit', 'color' => 'secondary', 'label' => 'Draft'],
        'submitted' => ['icon' => 'paper-plane', 'color' => 'info', 'label' => 'Submitted'],
        'under_review' => ['icon' => 'search', 'color' => 'warning', 'label' => 'Under Review'],
        'approved' => ['icon' => 'check', 'color' => 'success', 'label' => 'Approved'],
        'published' => ['icon' => 'rocket', 'color' => 'primary', 'label' => 'Published'],
        'rejected' => ['icon' => 'times', 'color' => 'danger', 'label' => 'Rejected'],
    ];
    
    $currentStep = $item->workflow_state;
    $stepKeys = array_keys($steps);
    $currentIndex = array_search($currentStep, $stepKeys);
@endphp

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-project-diagram mr-2"></i>
            Workflow Status
        </h3>
    </div>
    <div class="card-body">
        <div class="steps">
            @foreach($steps as $state => $step)
                @php
                    $isActive = $currentStep === $state;
                    $isCompleted = $currentIndex > array_search($state, $stepKeys);
                    $isRejected = $currentStep === 'rejected' && $state === 'rejected';
                    $isFuture = array_search($state, $stepKeys) > $currentIndex && $currentStep !== 'rejected';
                @endphp
                
                <div class="step {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }} {{ $isRejected ? 'rejected' : '' }} {{ $isFuture ? 'future' : '' }}">
                    <div class="step-icon bg-{{ $step['color'] }} {{ $isCompleted ? 'bg-success' : '' }} {{ $isRejected ? 'bg-danger' : '' }} {{ $isFuture ? 'bg-secondary' : '' }}">
                        <i class="fas fa-{{ $step['icon'] }}"></i>
                    </div>
                    <div class="step-label">
                        {{ $step['label'] }}
                        @if($isActive)
                            <div class="badge badge-primary mt-1">Current</div>
                        @endif
                    </div>
                </div>
                
                @if(!$loop->last && $state !== 'rejected')
                    <div class="step-connector {{ $isCompleted ? 'bg-success' : 'bg-secondary' }}"></div>
                @endif
            @endforeach
        </div>
        
        <!-- Additional Status Information -->
        <div class="mt-3 p-3 bg-light rounded">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        <strong>Created:</strong> {{ $item->created_at->format('M j, Y g:i A') }}
                    </small>
                </div>
                @if($item->submitted_at)
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="fas fa-paper-plane mr-1"></i>
                        <strong>Submitted:</strong> {{ $item->submitted_at->format('M j, Y g:i A') }}
                    </small>
                </div>
                @endif
                @if($item->published_at)
                <div class="col-md-6">
                    <small class="text-success">
                        <i class="fas fa-rocket mr-1"></i>
                        <strong>Published:</strong> {{ $item->published_at->format('M j, Y g:i A') }}
                    </small>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        @if(in_array($item->workflow_state, ['draft', 'submitted', 'under_review']))
        <div class="mt-3">
            <div class="btn-group btn-group-sm">
                @if($item->workflow_state === 'draft' && auth()->user()->can('submit', $item))
                <form action="{{ route('workflow.submit', $item) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane mr-1"></i>Submit for Review
                    </button>
                </form>
                @endif
                
                @if(auth()->user()->hasAnyRole(['manager', 'admin']))
                <form action="{{ route('workflow.quick-approve', $item) }}" method="POST" class="d-inline ml-1">
                    @csrf
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Quick approve and publish this item?')">
                        <i class="fas fa-bolt mr-1"></i>Quick Approve
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.steps {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    margin-bottom: 20px;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 2;
    flex: 1;
}

.step-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-bottom: 8px;
    font-size: 18px;
    transition: all 0.3s ease;
}

.step-label {
    font-size: 12px;
    font-weight: 600;
    text-align: center;
    max-width: 80px;
}

.step-connector {
    flex-grow: 1;
    height: 4px;
    margin: 0 5px;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.step.active .step-icon {
    animation: pulse 2s infinite;
    box-shadow: 0 0 0 5px rgba(0, 123, 255, 0.3);
}

.step.completed .step-icon {
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.3);
}

.step.rejected .step-icon {
    animation: shake 0.5s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Responsive design */
@media (max-width: 768px) {
    .steps {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .step {
        flex-direction: row;
        margin-bottom: 10px;
        width: 100%;
    }
    
    .step-icon {
        margin-right: 15px;
        margin-bottom: 0;
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .step-connector {
        display: none;
    }
    
    .step-label {
        text-align: left;
        max-width: none;
    }
}
</style>