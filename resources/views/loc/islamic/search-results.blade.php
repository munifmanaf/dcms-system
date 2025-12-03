{{-- resources/views/loc/islamic/search-results.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header {{ $noResults ? 'bg-warning' : 'bg-success' }} text-white">
                    <h3 class="card-title">
                        <i class="fas fa-search"></i> Search Results - {{ $collection['name'] }}
                        @if(!$noResults)
                        <small class="text-light">({{ $total }} records found)</small>
                        @endif
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('loc.islamic.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> New Search
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Summary -->
                    <div class="alert {{ $noResults ? 'alert-warning' : 'alert-info' }}">
                        <i class="fas fa-info-circle"></i>
                        <strong>Search Query:</strong> "{{ $searchKeyword ?? $searchParams['keyword'] ?? 'All records' }}"
                        @if(!empty($searchParams['subject']))
                        | <strong>Subject:</strong> {{ $searchParams['subject'] }}
                        @endif
                        @if(!empty($searchParams['language']))
                        | <strong>Language:</strong> {{ $searchParams['language'] }}
                        @endif
                        
                        @if($noResults)
                        <div class="mt-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>No records found</strong> with the specified criteria.
                        </div>
                        @endif
                    </div>

                    @if($noResults)
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-4x text-muted mb-4"></i>
                        <h4>No Results Found</h4>
                        <p class="text-muted mb-4">
                            No Islamic content was found in {{ $collection['name'] }} 
                            with your search criteria.
                        </p>
                        
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Suggestions:</h5>
                                        <ul class="text-left">
                                            <li>Try different keywords (e.g., "quran" instead of "quranic")</li>
                                            <li>Use broader search terms</li>
                                            <li>Try searching in Zenodo or DOAJ</li>
                                            <li>Remove language or subject filters</li>
                                        </ul>
                                        <div class="mt-3">
                                            <a href="{{ route('loc.islamic.index') }}" class="btn btn-primary">
                                                <i class="fas fa-redo"></i> Try Different Search
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Results Table -->
                    <form id="selectForm" action="{{ route('loc.islamic.import') }}" method="POST">
                        @csrf
                        <input type="hidden" name="import_mode" value="new_only">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="selectAll">
                                    <label class="custom-control-label" for="selectAll">Select All Records</label>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <span class="mr-3">
                                    Selected: <span id="selectedCount" class="badge badge-primary">0</span> / {{ $total }}
                                </span>
                                <button type="submit" id="importBtn" class="btn btn-success btn-sm" disabled>
                                    <i class="fas fa-download"></i> Import Selected
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="checkAll">
                                        </th>
                                        <th>Title & Description</th>
                                        <th>Author/Creator</th>
                                        <th>Date/Type</th>
                                        <th>Source</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($records as $index => $record)
                                    @php
                                        $metadata = $record['metadata'] ?? [];
                                        $title = is_array($metadata['title'] ?? []) ? ($metadata['title'][0] ?? 'Untitled') : ($metadata['title'] ?? 'Untitled');
                                        $description = is_array($metadata['description'] ?? []) ? ($metadata['description'][0] ?? '') : ($metadata['description'] ?? '');
                                        $creators = is_array($metadata['creator'] ?? []) ? $metadata['creator'] : (isset($metadata['creator']) ? [$metadata['creator']] : []);
                                        $date = is_array($metadata['date'] ?? []) ? ($metadata['date'][0] ?? '') : ($metadata['date'] ?? '');
                                        $type = is_array($metadata['type'] ?? []) ? ($metadata['type'][0] ?? '') : ($metadata['type'] ?? '');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input record-checkbox" 
                                                       name="selected_records[]" value="{{ $index }}" 
                                                       id="record_{{ $index }}">
                                                <label class="custom-control-label" for="record_{{ $index }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <strong class="text-primary">{{ $title }}</strong>
                                            @if($description)
                                            <p class="mb-0 small text-muted" style="max-height: 60px; overflow: hidden;">
                                                {{ Str::limit($description, 150) }}
                                            </p>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($creators))
                                                @foreach($creators as $creator)
                                                <span class="badge badge-secondary mb-1 d-block">{{ $creator }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($date)
                                            <span class="badge badge-info">{{ $date }}</span>
                                            @endif
                                            @if($type)
                                            <br><small class="text-muted">{{ $type }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted d-block">
                                                {{ $collection['name'] }}
                                            </small>
                                            <small class="text-muted d-block">
                                                {{ $record['datestamp'] ?? '' }}
                                            </small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    @if(!$noResults)
    let totalRecords = {{ $total }};
    let selectedCount = 0;
    
    // Select all checkbox
    $('#checkAll, #selectAll').change(function() {
        let isChecked = $(this).prop('checked');
        $('.record-checkbox').prop('checked', isChecked);
        selectedCount = isChecked ? totalRecords : 0;
        updateSelectionUI();
    });
    
    // Individual checkbox
    $(document).on('change', '.record-checkbox', function() {
        if (!$(this).prop('checked')) {
            $('#checkAll, #selectAll').prop('checked', false);
        }
        
        selectedCount = $('.record-checkbox:checked').length;
        
        // Update select all if all are checked
        if (selectedCount === totalRecords) {
            $('#checkAll, #selectAll').prop('checked', true);
        }
        
        updateSelectionUI();
    });
    
    // Update UI based on selection
    function updateSelectionUI() {
        $('#selectedCount').text(selectedCount);
        
        if (selectedCount > 0) {
            $('#importBtn').prop('disabled', false)
                .html(`<i class="fas fa-download"></i> Import ${selectedCount} Selected`);
        } else {
            $('#importBtn').prop('disabled', true)
                .html(`<i class="fas fa-download"></i> Import Selected`);
        }
    }
    
    // Form submission
    $('#selectForm').submit(function(e) {
        if (selectedCount === 0) {
            e.preventDefault();
            toastr.error('Please select at least one record to import');
            return false;
        }
        
        // Show confirmation
        if (!confirm(`Are you sure you want to import ${selectedCount} records?`)) {
            e.preventDefault();
            return false;
        }
        
        // Show loading
        $('#importBtn').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin"></i> Importing...');
    });
    
    // Initialize
    updateSelectionUI();
    @endif
});
</script>
@endpush