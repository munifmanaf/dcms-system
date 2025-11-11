@php
    $steps = [
        'draft' => [
            'icon' => 'fas fa-edit', 
            'label' => 'Draft', 
            'description' => 'Item is being prepared',
            'color' => '#6c757d'
        ],
        'pending_review' => [
            'icon' => 'fas fa-paper-plane', 
            'label' => 'Pending Review', 
            'description' => 'Awaiting approval',
            'color' => '#ffc107'
        ],
        'published' => [
            'icon' => 'fas fa-check-circle', 
            'label' => 'Published', 
            'description' => 'Live and accessible',
            'color' => '#28a745'
        ],
    ];

    $currentStep = $item->workflow_state;
    $stepKeys = array_keys($steps);
    $currentIndex = array_search($currentStep, $stepKeys);
    $currentIndex = $currentIndex !== false ? $currentIndex : 0;
    
    // Calculate progress
    $completedSteps = $currentIndex + 1;
    $totalSteps = count($steps);
    $progressPercentage = round(($completedSteps / $totalSteps) * 100);
@endphp

<div class="workflow-progress-container">
    <!-- Progress Header -->
    <div class="progress-header text-center mb-4">
        <h5 class="text-dark mb-2">
            <i class="fas fa-project-diagram mr-2"></i>Workflow Progress
        </h5>
        <div class="progress-overview">
            <span class="badge bg-{{ $currentStep == 'published' ? 'success' : ($currentStep == 'pending_review' ? 'warning' : 'secondary') }} badge-pill px-3 py-2">
                <i class="fas fa-{{ $currentStep == 'published' ? 'check-circle' : ($currentStep == 'pending_review' ? 'clock' : 'edit') }} mr-1"></i>
                {{ ucfirst($currentStep) }}
            </span>
            <div class="mt-2">
                <small class="text-muted">
                    {{ $completedSteps }} of {{ $totalSteps }} steps completed
                </small>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="progress mb-4" style="height: 8px;">
        <div class="progress-bar bg-success" role="progressbar" 
             style="width: {{ $progressPercentage }}%" 
             aria-valuenow="{{ $progressPercentage }}" 
             aria-valuemin="0" 
             aria-valuemax="100">
        </div>
    </div>

    <!-- Workflow Steps -->
    <div class="workflow-steps">
        @foreach($steps as $stepKey => $step)
            @php
                $stepIndex = array_search($stepKey, $stepKeys);
                $isCompleted = $stepIndex <= $currentIndex;
                $isCurrent = $currentStep === $stepKey;
            @endphp
            
            <div class="workflow-step {{ $isCompleted ? 'completed' : '' }} {{ $isCurrent ? 'current' : '' }} mb-3">
                <div class="step-indicator">
                    <div class="step-icon {{ $isCompleted ? 'bg-success' : ($isCurrent ? 'bg-primary' : 'bg-light border') }}">
                        @if($isCompleted)
                            <i class="fas fa-check text-white"></i>
                        @else
                            <i class="{{ $step['icon'] }} {{ $isCurrent ? 'text-white' : 'text-muted' }}"></i>
                        @endif
                    </div>
                    @if($stepIndex < count($steps) - 1)
                        <div class="step-connector {{ $isCompleted ? 'bg-success' : 'bg-light' }}"></div>
                    @endif
                </div>
                <div class="step-content">
                    <div class="step-title {{ $isCurrent ? 'text-primary font-weight-bold' : '' }}">
                        {{ $step['label'] }}
                    </div>
                    <div class="step-description text-muted small">
                        {{ $step['description'] }}
                    </div>
                    @if($isCurrent)
                        <div class="current-badge badge bg-primary mt-1">
                            <i class="fas fa-spinner fa-spin mr-1"></i>Current Step
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Quick Stats -->
    <div class="row text-center mt-4">
        <div class="col-6">
            <div class="stat-card">
                <div class="stat-number text-primary">{{ $completedSteps }}</div>
                <div class="stat-label small text-muted">Completed</div>
            </div>
        </div>
        <div class="col-6">
            <div class="stat-card">
                <div class="stat-number text-success">{{ $progressPercentage }}%</div>
                <div class="stat-label small text-muted">Progress</div>
            </div>
        </div>
    </div>
</div>

<style>
.workflow-step {
    display: flex;
    align-items: flex-start;
}
.step-indicator {
    position: relative;
    margin-right: 15px;
}
.step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    position: relative;
}
.step-connector {
    position: absolute;
    top: 40px;
    left: 50%;
    transform: translateX(-50%);
    width: 2px;
    height: 30px;
}
.step-content {
    flex: 1;
}
.workflow-steps {
    position: relative;
}
.stat-card {
    padding: 10px;
}
.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
}
</style>