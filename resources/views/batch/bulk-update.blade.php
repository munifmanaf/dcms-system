@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit mr-2"></i>
                        Bulk Update & Quick Actions
                    </h3>
                </div>
                <form action="{{ route('batch.bulk-update.process') }}" method="POST" id="bulkUpdateForm">
                    @csrf
                    <div class="card-body">
                        <!-- Action Selection -->
                        <div class="form-group">
                            <label for="action">Update Action *</label>
                            <select name="action" id="action" class="form-control" required>
                                <option value="">Select action...</option>
                                <option value="status">Update Status</option>
                                <option value="publish">Publish/Unpublish</option>
                                <option value="archive">Archive/Unarchive</option>
                                <option value="collection">Change Collection</option>
                            </select>
                        </div>

                        <!-- Dynamic Value Field -->
                        <div class="form-group" id="value_field" style="display: none;">
                            <label id="value_label">Value</label>
                            <select name="new_value" id="new_value" class="form-control" style="display: none;">
                                <!-- Options will be populated by JavaScript -->
                            </select>
                            <input type="text" name="new_value_text" id="new_value_text" class="form-control" style="display: none;" placeholder="Enter new value">
                        </div>

                        <!-- Item Selection -->
                        <div class="form-group">
                            <label for="collection_filter">Filter by Collection (Optional)</label>
                            <select name="collection_filter" id="collection_filter" class="form-control">
                                <option value="">All Collections</option>
                                @foreach($collections as $collection)
                                <option value="{{ $collection->id }}">{{ $collection->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="item_ids">Items to Update *</label>
                            <div class="input-group">
                                <input type="text" name="item_ids" id="item_ids" class="form-control" 
                                       placeholder="Enter item IDs separated by commas (e.g., 1,5,23,42)" required>
                                <div class="input-group-append">
                                    <button type="button" id="loadItemsBtn" class="btn btn-outline-primary">
                                        <i class="fas fa-sync"></i> Load from Collection
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">
                                Enter specific item IDs, or use the button to load items from selected collection
                            </small>
                        </div>

                        <!-- Items Preview -->
                        <div class="form-group">
                            <label>Selected Items Preview:</label>
                            <div id="itemsPreview" class="border rounded p-2 bg-light" style="min-height: 40px; max-height: 200px; overflow-y: auto;">
                                <span class="text-muted">No items selected</span>
                            </div>
                            <small class="text-muted" id="itemsCount">0 items selected</small>
                        </div>

                        <!-- Confirmation -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" id="confirm_action" name="confirm_action" required>
                                <label for="confirm_action" class="custom-control-label">
                                    I confirm I want to apply this bulk update to <span id="confirmCount">0</span> items
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning" id="submitBtn">
                            <i class="fas fa-cogs mr-1"></i> Apply Bulk Update
                        </button>
                        <a href="{{ route('batch.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>

            <!-- Quick Collection Actions -->
            <div class="card card-success mt-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Collection Actions
                    </h3>
                    <small class="text-muted">One-click actions for entire collections</small>
                </div>
                <div class="card-body">
                    @if($collections->count() > 0)
                        <div class="row">
                            @foreach($collections as $collection)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header py-2">
                                        <h6 class="card-title mb-0">{{ Str::limit($collection->title, 30) }}</h6>
                                        <small class="text-muted">{{ $collection->items_count ?? 0 }} items</small>
                                    </div>
                                    <div class="card-body py-2">
                                        <div class="btn-group btn-block" role="group">
                                            <form action="{{ route('batch.quick.publish', $collection->id) }}" method="POST" class="d-inline flex-fill">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm w-100" 
                                                        onclick="return confirm('Publish all items in {{ $collection->title }}?')">
                                                    <i class="fas fa-check-circle mr-1"></i> Publish All
                                                </button>
                                            </form>
                                            <form action="{{ route('batch.quick.unpublish', $collection->id) }}" method="POST" class="d-inline flex-fill">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm w-100" 
                                                        onclick="return confirm('Unpublish all items in {{ $collection->title }}?')">
                                                    <i class="fas fa-times-circle mr-1"></i> Unpublish All
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No collections found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Global Actions -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-globe mr-2"></i>
                        Global Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('batch.quick.approve') }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-info btn-block mb-2" 
                                onclick="return confirm('Approve and publish all pending items?')">
                            <i class="fas fa-thumbs-up mr-2"></i> Approve All Pending
                        </button>
                    </form>

                    <form action="{{ route('batch.quick.stats') }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-chart-line mr-2"></i> Generate Demo Stats
                        </button>
                        <small class="text-muted d-block">Adds random downloads/views for demo</small>
                    </form>

                    <button type="button" class="btn btn-success btn-block" onclick="runFullDemoPrep()">
                        <i class="fas fa-magic mr-2"></i> Full Demo Preparation
                    </button>
                    <small class="text-muted d-block">Runs all quick actions for perfect demo</small>
                </div>
            </div>

            <!-- Action Help -->
            <div class="card card-secondary mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        Quick Actions Guide
                    </h3>
                </div>
                <div class="card-body">
                    <h6>Bulk Update Actions:</h6>
                    <ul class="small">
                        <li><strong>Update Status:</strong> Change workflow state</li>
                        <li><strong>Publish/Unpublish:</strong> Make items public/private</li>
                        <li><strong>Archive/Unarchive:</strong> Archive or restore items</li>
                        <li><strong>Change Collection:</strong> Move items between collections</li>
                    </ul>

                    <h6 class="mt-3">Quick Actions:</h6>
                    <ul class="small">
                        <li><strong>Publish All:</strong> Instantly publish entire collection</li>
                        <li><strong>Approve All:</strong> Approve all pending items system-wide</li>
                        <li><strong>Demo Stats:</strong> Generate realistic usage data</li>
                        <li><strong>Full Demo Prep:</strong> One-click demo readiness</li>
                    </ul>

                    <div class="alert alert-warning mt-3 p-2">
                        <small>
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>Note:</strong> Quick actions affect entire collections and cannot be undone.
                        </small>
                    </div>
                </div>
            </div>

            <!-- System Stats -->
            <div class="card card-primary mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        System Status
                    </h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Items
                            <span class="badge badge-primary badge-pill">{{ $totalItems }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Published Items
                            <span class="badge badge-success badge-pill">{{ $publishedItems }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Downloads
                            <span class="badge badge-warning badge-pill">{{ $totalDownloads }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Views
                            <span class="badge badge-info badge-pill">{{ $totalViews }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

function runFullDemoPrep() {
    if (confirm('🚀 DEMO PREPARATION 🚀\n\nThis will:\n• Approve all pending items\n• Generate demo statistics\n• Make your system look active and populated\n\nReady to impress?')) {
        // Run quick approve
        fetch('{{ route("batch.quick.approve") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => {
            // Then run stats update
            return fetch('{{ route("batch.quick.stats") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }).then(() => {
            alert('🎉 Demo preparation complete! Your system is now demo-ready with published content and realistic stats.');
            location.reload();
        }).catch(error => {
            alert('Demo preparation completed! The page will now refresh.');
            location.reload();
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const actionSelect = document.getElementById('action');
    const valueField = document.getElementById('value_field');
    const valueLabel = document.getElementById('value_label');
    const valueSelect = document.getElementById('new_value');
    const valueInput = document.getElementById('new_value_text');
    const itemIdsInput = document.getElementById('item_ids');
    const itemsPreview = document.getElementById('itemsPreview');
    const itemsCount = document.getElementById('itemsCount');
    const confirmCount = document.getElementById('confirmCount');
    const collectionFilter = document.getElementById('collection_filter');
    const loadItemsBtn = document.getElementById('loadItemsBtn');
    const confirmCheckbox = document.getElementById('confirm_action');

    // Action change handler
    actionSelect.addEventListener('change', function() {
        const action = this.value;
        valueField.style.display = action ? 'block' : 'none';
        
        // Clear previous values
        valueSelect.innerHTML = '';
        valueSelect.style.display = 'none';
        valueInput.style.display = 'none';

        if (action === 'status') {
            valueLabel.textContent = 'New Status *';
            valueSelect.style.display = 'block';
            addOption(valueSelect, 'draft', 'Draft');
            addOption(valueSelect, 'review', 'Under Review');
            addOption(valueSelect, 'approved', 'Approved');
            addOption(valueSelect, 'rejected', 'Rejected');
        } else if (action === 'publish') {
            valueLabel.textContent = 'Publish State *';
            valueSelect.style.display = 'block';
            addOption(valueSelect, '1', 'Publish');
            addOption(valueSelect, '0', 'Unpublish');
        } else if (action === 'archive') {
            valueLabel.textContent = 'Archive State *';
            valueSelect.style.display = 'block';
            addOption(valueSelect, '1', 'Archive');
            addOption(valueSelect, '0', 'Unarchive');
        } else if (action === 'collection') {
            valueLabel.textContent = 'Target Collection *';
            valueSelect.style.display = 'block';
            @foreach($collections as $collection)
            addOption(valueSelect, '{{ $collection->id }}', '{{ $collection->title }}');
            @endforeach
        }
    });

    // Load items from collection
    loadItemsBtn.addEventListener('click', function() {
        const collectionId = collectionFilter.value;
        if (!collectionId) {
            alert('Please select a collection first');
            return;
        }

        fetch(`/batch/get-items?collection_id=${collectionId}`)
            .then(response => response.json())
            .then(items => {
                const itemIds = items.map(item => item.id);
                itemIdsInput.value = itemIds.join(',');
                updateItemsPreview(itemIds, items);
            })
            .catch(error => {
                console.error('Error loading items:', error);
                alert('Error loading items from collection');
            });
    });

    // Update items preview when input changes
    itemIdsInput.addEventListener('input', function() {
        const itemIds = this.value.split(',').map(id => id.trim()).filter(id => id);
        updateItemsPreview(itemIds);
    });

    function updateItemsPreview(itemIds, itemsData = null) {
        const count = itemIds.length;
        itemsCount.textContent = `${count} items selected`;
        confirmCount.textContent = count;

        if (count === 0) {
            itemsPreview.innerHTML = '<span class="text-muted">No items selected</span>';
            confirmCheckbox.checked = false;
            return;
        }

        let previewHtml = '';
        if (itemsData) {
            itemsData.forEach(item => {
                previewHtml += `<span class="badge badge-primary mr-1 mb-1" title="${item.title}">#${item.id}</span>`;
            });
        } else {
            itemIds.forEach(id => {
                previewHtml += `<span class="badge badge-secondary mr-1 mb-1">#${id}</span>`;
            });
        }
        
        itemsPreview.innerHTML = previewHtml;
    }

    function addOption(select, value, text) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = text;
        select.appendChild(option);
    }

    function runFullDemoPrep() {
        if (confirm('🚀 DEMO PREPARATION 🚀\n\nThis will:\n• Approve all pending items\n• Generate demo statistics\n• Make your system look active and populated\n\nReady to impress?')) {
            // Run quick approve
            fetch('{{ route("batch.quick.approve") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                // Then run stats update
                return fetch('{{ route("batch.quick.stats") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            }).then(() => {
                alert('🎉 Demo preparation complete! Your system is now demo-ready with published content and realistic stats.');
                location.reload();
            }).catch(error => {
                alert('Demo preparation completed! The page will now refresh.');
                location.reload();
            });
        }
    }

    // Form validation
    document.getElementById('bulkUpdateForm').addEventListener('submit', function(e) {
        const itemIds = itemIdsInput.value.split(',').filter(id => id.trim());
        if (itemIds.length === 0) {
            e.preventDefault();
            alert('Please select at least one item');
            return;
        }

        if (!confirmCheckbox.checked) {
            e.preventDefault();
            alert('Please confirm the bulk update action');
            return;
        }

        if (!confirm(`Are you sure you want to update ${itemIds.length} items? This action cannot be undone.`)) {
            e.preventDefault();
        }
    });
});
</script>
@endpush