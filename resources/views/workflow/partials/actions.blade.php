<div class="workflow-actions">
    <!-- Submit for Review -->
    @if($item->canBeSubmitted())
    <div class="action-group mb-3">
        <h6 class="text-primary">
            <i class="fas fa-paper-plane mr-2"></i>Start Workflow
        </h6>
        <form action="{{ route('workflow.submit', $item) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-play mr-1"></i> Submit for Review
            </button>
        </form>
        <small class="form-text text-muted">Submit this item to begin the review process</small>
    </div>
    @endif

    <!-- Review Actions -->
    @if($item->isInReview())
    <div class="action-group mb-3">
        <h6 class="text-warning">
            <i class="fas fa-clipboard-check mr-2"></i>Review Actions
        </h6>
        <div class="btn-group btn-group-sm" role="group">
            <form action="{{ route('workflow.technical-review', $item) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-warning">
                    <i class="fas fa-cogs mr-1"></i> Technical Review
                </button>
            </form>
            <form action="{{ route('workflow.content-review', $item) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-info">
                    <i class="fas fa-file-alt mr-1"></i> Content Review
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- Approval Actions -->
    @if($item->isReadyForApproval())
    <div class="action-group mb-3">
        <h6 class="text-success">
            <i class="fas fa-check-double mr-2"></i>Approval Actions
        </h6>
        <div class="btn-group btn-group-sm" role="group">
            <form action="{{ route('workflow.final-approve', $item) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check mr-1"></i> Final Approve
                </button>
            </form>
            <form action="{{ route('workflow.quick-approve', $item) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-success">
                    <i class="fas fa-bolt mr-1"></i> Quick Approve
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- Status Management -->
    <div class="action-group">
        <h6 class="text-secondary">
            <i class="fas fa-cog mr-2"></i>Status Management
        </h6>
        <div class="btn-group btn-group-sm" role="group">
            @if($item->is_published)
            <form action="{{ route('items.update-status', $item) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="draft">
                <button type="submit" class="btn btn-warning" title="Unpublish">
                    <i class="fas fa-eye-slash mr-1"></i> Unpublish
                </button>
            </form>
            @else
            <form action="{{ route('items.update-status', $item) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="published">
                <button type="submit" class="btn btn-success" title="Publish">
                    <i class="fas fa-eye mr-1"></i> Publish
                </button>
            </form>
            @endif

            <form action="{{ route('items.archive', $item) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-secondary" title="Archive">
                    <i class="fas fa-archive mr-1"></i> Archive
                </button>
            </form>
        </div>
    </div>
</div>