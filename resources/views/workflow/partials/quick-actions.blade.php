<div class="quick-actions">
    <div class="list-group">
        <!-- View Item -->
        <a href="{{ route('items.show', $item) }}" class="list-group-item list-group-item-action">
            <div class="d-flex align-items-center">
                <i class="fas fa-eye text-primary mr-3"></i>
                <div>
                    <strong>View Item</strong>
                    <small class="d-block text-muted">See full item details</small>
                </div>
            </div>
        </a>

        <!-- Edit Item -->
        <a href="{{ route('items.edit', $item) }}" class="list-group-item list-group-item-action">
            <div class="d-flex align-items-center">
                <i class="fas fa-edit text-warning mr-3"></i>
                <div>
                    <strong>Edit Content</strong>
                    <small class="d-block text-muted">Modify item details</small>
                </div>
            </div>
        </a>

        <!-- Download File -->
        @if($item->file_path)
        <a href="{{ route('items.download', $item) }}" class="list-group-item list-group-item-action">
            <div class="d-flex align-items-center">
                <i class="fas fa-download text-success mr-3"></i>
                <div>
                    <strong>Download File</strong>
                    <small class="d-block text-muted">Get the attached file</small>
                </div>
            </div>
        </a>
        @endif

        <!-- Copy Link -->
        @if($item->is_published)
        <a href="#" class="list-group-item list-group-item-action" onclick="copyItemLink()">
            <div class="d-flex align-items-center">
                <i class="fas fa-link text-info mr-3"></i>
                <div>
                    <strong>Copy Public Link</strong>
                    <small class="d-block text-muted">Share this item</small>
                </div>
            </div>
        </a>
        @endif

        <!-- Statistics -->
        <div class="list-group-item">
            <div class="d-flex align-items-center">
                <i class="fas fa-chart-bar text-secondary mr-3"></i>
                <div>
                    <strong>Statistics</strong>
                    <small class="d-block text-muted">
                        Created: {{ $item->created_at->diffForHumans() }}
                    </small>
                    <small class="d-block text-muted">
                        Updated: {{ $item->updated_at->diffForHumans() }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="list-group-item">
            <div class="d-flex align-items-center">
                <i class="fas fa-file-export text-dark mr-3"></i>
                <div>
                    <strong>Export</strong>
                    <div class="btn-group btn-group-sm mt-1">
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyItemLink() {
    const link = "{{ route('items.show', $item) }}";
    navigator.clipboard.writeText(link).then(function() {
        toastr.success('Link copied to clipboard!');
    }, function() {
        toastr.error('Failed to copy link');
    });
}
</script>
@endpush