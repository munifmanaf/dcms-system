@php
    $steps = [
        'draft' => [
            'icon' => 'fas fa-edit', 
            'label' => 'Draft', 
            'description' => 'Item is being prepared',
            'color' => '#6c757d'
        ],
        'submitted' => [
            'icon' => 'fas fa-paper-plane', 
            'label' => 'Submitted', 
            'description' => 'Awaiting initial review',
            'color' => '#17a2b8'
        ],
        'technical_review' => [
            'icon' => 'fas fa-cogs', 
            'label' => 'Technical Review', 
            'description' => 'Technical assessment',
            'color' => '#ffc107'
        ],
        'content_review' => [
            'icon' => 'fas fa-file-alt', 
            'label' => 'Content Review', 
            'description' => 'Content quality check',
            'color' => '#fd7e14'
        ],
        'approved' => [
            'icon' => 'fas fa-check-circle', 
            'label' => 'Approved', 
            'description' => 'Ready for publication',
            'color' => '#28a745'
        ],
        'published' => [
            'icon' => 'fas fa-globe', 
            'label' => 'Published', 
            'description' => 'Live and accessible',
            'color' => '#007bff'
        ],
    ];

    $currentStep = $item->getCurrentWorkflowStep();
    $stepKeys = array_keys($steps);
    $currentIndex = array_search($currentStep, $stepKeys);
    $currentIndex = $currentIndex !== false ? $currentIndex : 0;
@endphp

<div class="workflow-progress-container">
    <!-- Progress Header -->
    <div class="progress-header text-center mb-4">
        <h4 class="text-dark mb-2">
            <i class="fas fa-project-diagram mr-2"></i>Workflow Progress
        </h4>
        <div class="progress-overview">
            <span class="badge badge-{{ $item->getWorkflowStatusColor() }} badge-pill px-3 py-2">
                <i class="fas fa-{{ $item->getWorkflowStatusIcon() }} mr-1"></i>
                {{ $item->getWorkflowStatusText() }}
            </span>
            <div class="mt-2">
                <small class="text-muted">
                    {{ $item->getCompletedStepsCount() }} of {{ count($steps) }} steps completed
                </small>
            </div>
        </div>
    </div>

    <!-- Animated Progress Bar -->
    <div class="progress-track-container mb-4">
        <div class="progress-track">
            @foreach($steps as $stepKey => $step)
                @php
                    $stepIndex = array_search($stepKey, $stepKeys);
                    $isCompleted = $item->isWorkflowStepCompleted($stepKey);
                    $isCurrent = $currentStep === $stepKey;
                    $isFuture = $stepIndex > $currentIndex;
                @endphp
                
                <div class="progress-step {{ $isCompleted ? 'completed' : '' }} {{ $isCurrent ? 'current' : '' }} {{ $isFuture ? 'future' : '' }}"
                     style="--step-color: {{ $step['color'] }}; --step-index: {{ $stepIndex }};">
                    
                    <!-- Step Connector Line -->
                    @if($stepIndex < count($steps) - 1)
                    <div class="step-connector">
                        <div class="connector-line"></div>
                        <div class="connector-progress {{ $isCompleted ? 'completed' : '' }}"></div>
                    </div>
                    @endif

                    <!-- Step Circle -->
                    <div class="step-circle">
                        @if($isCompleted)
                            <div class="step-icon-completed">
                                <i class="fas fa-check"></i>
                            </div>
                        @elseif($isCurrent)
                            <div class="step-icon-current">
                                <i class="{{ $step['icon'] }}"></i>
                                <div class="pulse-animation"></div>
                            </div>
                        @else
                            <div class="step-icon-future">
                                <i class="{{ $step['icon'] }}"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Step Label -->
                    <div class="step-label">
                        <div class="step-title {{ $isCurrent ? 'current' : '' }}">
                            {{ $step['label'] }}
                        </div>
                        <div class="step-description">
                            {{ $step['description'] }}
                        </div>
                        @if($isCurrent)
                        <div class="current-indicator">
                            <i class="fas fa-spinner fa-spin mr-1"></i>In Progress
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Progress Stats -->
    <div class="progress-stats">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number text-primary">{{ $item->getCompletedStepsCount() }}</div>
                    <div class="stat-label">Steps Done</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number text-info">{{ count($steps) - $item->getCompletedStepsCount() }}</div>
                    <div class="stat-label">Steps Left</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number text-success">{{ $item->getWorkflowProgressPercentage() }}%</div>
                    <div class="stat-label">Complete</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number text-warning">{{ $item->getEstimatedCompletion() }}</div>
                    <div class="stat-label">Est. Time</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Next Action -->
    @if($currentStep !== 'published')
    <div class="next-action-card mt-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <h6 class="mb-2">
                    <i class="fas fa-arrow-circle-right text-primary mr-2"></i>
                    Next Action Required
                </h6>
                <p class="mb-2 text-muted small">
                    {{ $item->getNextActionDescription() }}
                </p>
                <div class="next-step-badge">
                    {{ $item->getNextStepName() }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>