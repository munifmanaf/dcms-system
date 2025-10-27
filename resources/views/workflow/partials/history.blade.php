<div class="workflow-history">
    @if($item->workflowHistory && $item->workflowHistory->count() > 0)
        <div class="workflow-timeline">
            @foreach($item->workflowHistory as $history)
            <div class="timeline-item {{ $history->getStatusClass() }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 font-weight-bold">{{ $history->getActionText() }}</h6>
                        <p class="mb-1 small">{{ $history->notes ?? 'No additional notes' }}</p>
                        <small class="text-muted">
                            <i class="fas fa-user mr-1"></i>
                            {{ $history->user->name ?? 'System' }}
                        </small>
                    </div>
                    <div class="text-right">
                        <small class="text-muted">
                            {{ $history->created_at->format('M j, Y g:i A') }}
                        </small>
                        <br>
                        <span class="badge badge-{{ $history->getStatusColor() }} badge-sm">
                            {{ $history->getStatusText() }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <!-- Sample history for demonstration -->
        <div class="workflow-timeline">
            <!-- Creation Event -->
            <div class="timeline-item success">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 font-weight-bold">Item Created</h6>
                        <p class="mb-1 small">Initial draft created</p>
                        <small class="text-muted">
                            <i class="fas fa-user mr-1"></i>
                            {{ $item->user->name ?? 'System' }}
                        </small>
                    </div>
                    <div class="text-right">
                        <small class="text-muted">
                            {{ $item->created_at->format('M j, Y g:i A') }}
                        </small>
                        <br>
                        <span class="badge badge-success badge-sm">
                            Completed
                        </span>
                    </div>
                </div>
            </div>

            <!-- Status Changes -->
            @if($item->is_approved && $item->approved_at)
            <div class="timeline-item info">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 font-weight-bold">Item Approved</h6>
                        <p class="mb-1 small">Final approval granted</p>
                        <small class="text-muted">
                            <i class="fas fa-user mr-1"></i>
                            {{ $item->approvedByUser->name ?? 'Approver' }}
                        </small>
                    </div>
                    <div class="text-right">
                        <small class="text-muted">
                            {{ $item->approved_at->format('M j, Y g:i A') }}
                        </small>
                        <br>
                        <span class="badge badge-info badge-sm">
                            Approved
                        </span>
                    </div>
                </div>
            </div>
            @endif

            @if($item->is_published)
            <div class="timeline-item success">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 font-weight-bold">Item Published</h6>
                        <p class="mb-1 small">Made available to public</p>
                        <small class="text-muted">
                            <i class="fas fa-user mr-1"></i>
                            {{ $item->user->name ?? 'Publisher' }}
                        </small>
                    </div>
                    <div class="text-right">
                        <small class="text-muted">
                            {{ $item->updated_at->format('M j, Y g:i A') }}
                        </small>
                        <br>
                        <span class="badge badge-success badge-sm">
                            Published
                        </span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Current Status -->
            <div class="timeline-item {{ $item->is_published ? 'success' : ($item->is_approved ? 'info' : 'warning') }} current">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 font-weight-bold">Current Status</h6>
                        <p class="mb-1 small">
                            @if($item->is_published)
                                Item is live and accessible
                            @elseif($item->is_approved)
                                Item approved, ready for publication
                            @else
                                Item in draft stage
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <small class="text-muted">Now</small>
                        <br>
                        <span class="badge badge-{{ $item->getWorkflowStatusColor() }} badge-sm">
                            {{ $item->getWorkflowStatusText() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <p class="text-muted mb-2">
                <i class="fas fa-info-circle mr-1"></i>
                This is sample history. Implement workflow history tracking to see real events.
            </p>
            <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#addHistoryModal">
                <i class="fas fa-plus mr-1"></i> Add Manual Entry
            </button>
        </div>
    @endif
</div>

<!-- Add History Modal -->
<div class="modal fade" id="addHistoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Workflow History Entry</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('workflow.add-history', $item) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="action">Action Type</label>
                        <select name="action" id="action" class="form-control" required>
                            <option value="created">Created</option>
                            <option value="submitted">Submitted</option>
                            <option value="technical_review">Technical Review</option>
                            <option value="content_review">Content Review</option>
                            <option value="approved">Approved</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                            <option value="updated">Updated</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" 
                                  placeholder="Add any relevant notes about this workflow action..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>