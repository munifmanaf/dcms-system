{{-- resources/views/oai/harvest/search-results.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-search"></i> Search Results
                        <small class="text-muted">
                            Found {{ $total }} records
                            @if($keyword)
                            for "{{ $keyword }}"
                            @endif
                        </small>
                    </h3>
                </div>
                <div class="card-body">
                    <form id="selectForm" action="{{ route('oai.harvest.select-records') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="selectAll">
                                    <label class="custom-control-label" for="selectAll">Select All</label>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-primary" id="selectButton">
                                    <i class="fas fa-check-circle"></i> Select for Import
                                </button>
                                <a href="{{ route('oai.harvest.search') }}" class="btn btn-default">
                                    <i class="fas fa-search"></i> New Search
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">Select</th>
                                        <th>Title</th>
                                        <th>Creator</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Identifier</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($records as $record)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input record-checkbox" 
                                                       name="selected_records[]" value="{{ $record['identifier'] }}" 
                                                       id="record_{{ $loop->index }}">
                                                <label class="custom-control-label" for="record_{{ $loop->index }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $record['metadata']['title'][0] ?? 'Untitled' }}</strong>
                                            @if(isset($record['metadata']['description'][0]))
                                            <p class="text-muted small mb-0">
                                                {{ Str::limit($record['metadata']['description'][0], 100) }}
                                            </p>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($record['metadata']['creator']))
                                                @if(is_array($record['metadata']['creator']))
                                                {{ implode(', ', array_slice($record['metadata']['creator'], 0, 2)) }}
                                                @if(count($record['metadata']['creator']) > 2)
                                                <small class="text-muted">+{{ count($record['metadata']['creator']) - 2 }} more</small>
                                                @endif
                                                @else
                                                {{ $record['metadata']['creator'] }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $record['metadata']['date'][0] ?? $record['datestamp'] }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $record['metadata']['type'][0] ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted d-block">
                                                {{ Str::limit($record['identifier'], 50) }}
                                            </small>
                                            @if(isset($record['metadata']['identifier']))
                                            <small class="text-muted d-block">
                                                @if(is_array($record['metadata']['identifier']))
                                                {{ $record['metadata']['identifier'][0] }}
                                                @else
                                                {{ $record['metadata']['identifier'] }}
                                                @endif
                                            </small>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="alert alert-warning">
                                                No records found with the specified criteria.
                                            </div>
                                            <a href="{{ route('oai.harvest.search') }}" class="btn btn-primary">
                                                <i class="fas fa-search"></i> Try Different Search
                                            </a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if(!empty($records))
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Selected <span id="selectedCount">0</span> out of {{ $total }} records.
                                    Click "Select for Import" to proceed.
                                </div>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let totalRecords = {{ count($records) }};
    let selectedCount = 0;

    // Select all checkbox
    $('#selectAll').change(function() {
        $('.record-checkbox').prop('checked', $(this).prop('checked'));
        selectedCount = $(this).prop('checked') ? totalRecords : 0;
        updateSelectedCount();
    });

    // Individual checkbox
    $(document).on('change', '.record-checkbox', function() {
        if (!$(this).prop('checked')) {
            $('#selectAll').prop('checked', false);
        }
        
        selectedCount = $('.record-checkbox:checked').length;
        if (selectedCount === totalRecords) {
            $('#selectAll').prop('checked', true);
        }
        
        updateSelectedCount();
    });

    // Update selected count
    function updateSelectedCount() {
        $('#selectedCount').text(selectedCount);
        
        if (selectedCount > 0) {
            $('#selectButton').prop('disabled', false)
                .html(`<i class="fas fa-check-circle"></i> Import ${selectedCount} Selected`);
        } else {
            $('#selectButton').prop('disabled', true)
                .html(`<i class="fas fa-check-circle"></i> Select for Import`);
        }
    }

    // Form submission
    $('#selectForm').submit(function(e) {
        if (selectedCount === 0) {
            e.preventDefault();
            toastr.error('Please select at least one record to import.');
            return false;
        }
    });

    // Initialize count
    updateSelectedCount();
});
</script>
@endpush