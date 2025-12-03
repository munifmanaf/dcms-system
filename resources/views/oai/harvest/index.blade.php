{{-- resources/views/oai/harvest/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cloud-download-alt"></i> OAI-PMH Harvester
                    </h3>
                </div>
                <div class="card-body">
                    <form id="harvestForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="endpoint">OAI-PMH Endpoint URL *</label>
                                    <div class="input-group">
                                        <input type="url" name="endpoint" id="endpoint" 
                                               class="form-control" required 
                                               placeholder="https://example.org/oai">
                                        <div class="input-group-append">
                                            <button type="button" id="testConnection" class="btn btn-outline-info">
                                                <i class="fas fa-plug"></i> Test
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        Enter the OAI-PMH endpoint URL of the repository
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div id="repositoryInfo" class="alert alert-info" style="display: none;">
                            <h5><i class="fas fa-info-circle"></i> Repository Information</h5>
                            <div id="repoDetails"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Popular Repositories</label>
                                    <select class="form-control" id="popularRepos">
                                        <option value="">Select a repository...</option>
                                        @foreach($popularRepositories as $url => $name)
                                        <option value="{{ $url }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="metadata_prefix">Metadata Format *</label>
                                    <select name="metadata_prefix" id="metadata_prefix" class="form-control" required>
                                        <option value="oai_dc" selected>Dublin Core (oai_dc)</option>
                                        <option value="mods">MODS</option>
                                        <option value="marcxml">MARCXML</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="set">Set (Collection)</label>
                                    <select name="set" id="set" class="form-control">
                                        <option value="">All sets</option>
                                        <!-- Sets will be loaded dynamically -->
                                    </select>
                                    <button type="button" id="loadSets" class="btn btn-sm btn-outline-secondary mt-1">
                                        <i class="fas fa-sync"></i> Load Sets
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="from_date">From Date</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="until_date">Until Date</label>
                                    <input type="date" name="until_date" id="until_date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="language">Language</label>
                                    <select name="language" id="language" class="form-control">
                                        <option value="">Any Language</option>
                                        <option value="arabic">Arabic</option>
                                        <option value="persian">Persian</option>
                                        <option value="english">English</option>
                                        <option value="urdu">Urdu</option>
                                        <option value="turkish">Turkish</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="limit">Preview Limit</label>
                                    <input type="number" name="limit" id="limit" 
                                           class="form-control" value="10" min="1" max="50">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="button" id="previewHarvest" class="btn btn-primary">
                                <i class="fas fa-eye"></i> Preview Records
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Harvest History -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Recent Harvests
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Repository</th>
                                    <th>Date</th>
                                    <th>Records</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($harvestLogs as $log)
                                <tr>
                                    <td>
                                        <small>{{ Str::limit($log->endpoint, 40) }}</small>
                                    </td>
                                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <span class="badge badge-success">{{ $log->imported_records }}</span>
                                        <span class="badge badge-warning">{{ $log->skipped_records }}</span>
                                        <span class="badge badge-danger">{{ $log->failed_records }}</span>
                                    </td>
                                    <td>
                                        @switch($log->status)
                                            @case('completed')
                                                <span class="badge badge-success">Completed</span>
                                                @break
                                            @case('processing')
                                                <span class="badge badge-info">Processing</span>
                                                @break
                                            @case('failed')
                                                <span class="badge badge-danger">Failed</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ $log->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="{{ route('oai.harvest.show', $log->id) }}" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($log->status === 'processing' && $log->resumption_token)
                                        <a href="{{ route('oai.harvest.resume', $log->id) }}" 
                                           class="btn btn-sm btn-warning" title="Resume Harvest">
                                            <i class="fas fa-play"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('oai.harvest.history') }}" class="btn btn-outline-primary btn-sm">
                        View All History
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Collections Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-folder"></i> Collections
                    </h3>
                </div>
                <div class="card-body">
                    <p>Select where to import harvested items:</p>
                    <div class="list-group">
                        @foreach($collections as $collection)
                        <div class="list-group-item">
                            <div class="form-check">
                                <input class="form-check-input collection-radio" 
                                       type="radio" 
                                       name="collection_id" 
                                       value="{{ $collection->id }}"
                                       id="collection_{{ $collection->id }}"
                                       {{ $loop->first ? 'checked' : '' }}>
                                <label class="form-check-label" for="collection_{{ $collection->id }}">
                                    {{ $collection->name }}
                                    @if($collection->description)
                                    <small class="text-muted d-block">{{ Str::limit($collection->description, 50) }}</small>
                                    @endif
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Instructions Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Instructions
                    </h3>
                </div>
                <div class="card-body">
                    <ol class="pl-3">
                        <li>Enter OAI-PMH endpoint URL</li>
                        <li>Test connection to verify</li>
                        <li>Select metadata format</li>
                        <li>Optional: Select specific set/collection</li>
                        <li>Optional: Set date range</li>
                        <li>Preview records before import</li>
                        <li>Select target collection</li>
                        <li>Start harvest</li>
                    </ol>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Note:</strong> Large harvests may take time. Use batch size to control import.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Records</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="previewContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="startHarvest" class="btn btn-primary">Start Harvest</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .record-preview {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 10px;
    }
    .record-preview h6 {
        color: #007bff;
    }
    .metadata-item {
        margin-bottom: 5px;
    }
    .metadata-label {
        font-weight: bold;
        color: #6c757d;
        min-width: 100px;
        display: inline-block;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Load popular repository
    $('#popularRepos').change(function() {
        if ($(this).val()) {
            $('#endpoint').val($(this).val());
            $('#testConnection').click();
        }
    });

    // Test connection
    $('#testConnection').click(function() {
        let endpoint = $('#endpoint').val();
        if (!endpoint) {
            toastr.error('Please enter an endpoint URL');
            return;
        }

        $.ajax({
            url: '{{ route("oai.harvest.test-connection") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                endpoint: endpoint
            },
            beforeSend: function() {
                $('#testConnection').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Testing...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Connection successful!');
                    
                    // Display repository info
                    let repo = response.data;
                    let html = `
                        <p><strong>${repo.repositoryName}</strong></p>
                        <p><small>Protocol: ${repo.protocolVersion} | Granularity: ${repo.granularity}</small></p>
                        <p><small>Earliest: ${repo.earliestDatestamp} | Admin: ${repo.adminEmail}</small></p>
                    `;
                    $('#repoDetails').html(html);
                    $('#repositoryInfo').show();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Connection failed. Please check the URL and try again.');
            },
            complete: function() {
                $('#testConnection').prop('disabled', false)
                    .html('<i class="fas fa-plug"></i> Test');
            }
        });
    });

    // Load sets
    $('#loadSets').click(function() {
        let endpoint = $('#endpoint').val();
        if (!endpoint) {
            toastr.error('Please enter an endpoint URL first');
            return;
        }

        $.ajax({
            url: '{{ route("oai.harvest.get-sets") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                endpoint: endpoint
            },
            beforeSend: function() {
                $('#loadSets').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Loading...');
            },
            success: function(response) {
                if (response.success) {
                    let sets = response.data;
                    let options = '<option value="">All sets</option>';
                    
                    sets.forEach(function(set) {
                        options += `<option value="${set.setSpec}">${set.setName}</option>`;
                    });
                    
                    $('#set').html(options);
                    toastr.success(`Loaded ${sets.length} set(s)`);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Failed to load sets');
            },
            complete: function() {
                $('#loadSets').prop('disabled', false)
                    .html('<i class="fas fa-sync"></i> Load Sets');
            }
        });
    });

    // Preview harvest
    $('#previewHarvest').click(function() {
        let formData = $('#harvestForm').serialize();
        
        $.ajax({
            url: '{{ route("oai.harvest.preview") }}',
            method: 'POST',
            data: formData,
            beforeSend: function() {
                $('#previewHarvest').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Loading...');
            },
            success: function(response) {
                $('#previewContent').html(response);
                $('#previewModal').modal('show');
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('Failed to preview records');
                }
            },
            complete: function() {
                $('#previewHarvest').prop('disabled', false)
                    .html('<i class="fas fa-eye"></i> Preview Records');
            }
        });
    });

    // Start harvest
    $('#startHarvest').click(function() {
        let collectionId = $('.collection-radio:checked').val();
        if (!collectionId) {
            toastr.error('Please select a collection');
            return;
        }

        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('collection_id', collectionId);
        formData.append('import_mode', 'preview');
        formData.append('batch_size', $('#limit').val());

        $.ajax({
            url: '{{ route("oai.harvest.harvest") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#startHarvest').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Importing...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#previewModal').modal('hide');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('Harvest failed');
                }
            },
            complete: function() {
                $('#startHarvest').prop('disabled', false)
                    .html('Start Harvest');
            }
        });
    });
});
</script>
@endpush