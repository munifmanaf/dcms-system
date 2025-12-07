{{-- resources/views/oai/harvest/search.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-search"></i> Search OAI-PMH Repository
                    </h3>
                </div>
                <div class="card-body">
                    <form id="searchForm" action="{{ route('oai.harvest.search-perform') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="endpoint">Repository URL *</label>
                                    <div class="input-group">
                                        <input type="url" name="endpoint" id="endpoint" 
                                               class="form-control" required 
                                               placeholder="https://example.org/oai"
                                               value="{{ old('endpoint') }}">
                                        <div class="input-group-append">
                                            <button type="button" id="testConnection" class="btn btn-outline-info">
                                                <i class="fas fa-plug"></i> Test
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Popular Repositories</label>
                                    <select class="form-control" id="popularRepos">
                                        <option value="">Quick select...</option>
                                        @foreach($popularRepositories as $url => $name)
                                        <option value="{{ $url }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="repositoryInfo" class="alert alert-info" style="display: none;">
                            <h5><i class="fas fa-info-circle"></i> Connected Repository</h5>
                            <div id="repoDetails"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="keyword">Search Keyword</label>
                                    <input type="text" name="keyword" id="keyword" 
                                           class="form-control" 
                                           placeholder="Search in titles, descriptions, subjects..."
                                           value="{{ old('keyword') }}">
                                    <small class="form-text text-muted">
                                        Leave empty to browse all records
                                    </small>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="set">Collection/Set</label>
                                    <select name="set" id="set" class="form-control">
                                        <option value="">All collections</option>
                                    </select>
                                    <button type="button" id="loadSets" class="btn btn-sm btn-outline-secondary mt-1">
                                        <i class="fas fa-sync"></i> Load Collections
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="from_date">From Date</label>
                                    <input type="date" name="from_date" id="from_date" 
                                           class="form-control" value="{{ old('from_date') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="until_date">Until Date</label>
                                    <input type="date" name="until_date" id="until_date" 
                                           class="form-control" value="{{ old('until_date') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="publisher">Publisher</label>
                                    <input type="text" name="publisher" id="publisher" 
                                           class="form-control" 
                                           value="{{ old('publisher') }}">
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
                                        <option value="english">English</option>
                                        <option value="malay">Malay</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_results">Max Results</label>
                                    <select name="max_results" id="max_results" class="form-control">
                                        <option value="20">20 records</option>
                                        <option value="50" selected>50 records</option>
                                        <option value="100">100 records</option>
                                        <option value="200">200 records</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search Repository
                            </button>
                            <a href="{{ route('oai.harvest.index') }}" class="btn btn-default">
                                <i class="fas fa-backward"></i> Back to Bulk Harvest
                            </a>
                        </div>
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
            toastr.error('Please enter a repository URL');
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
                    
                    let repo = response.data;
                    let html = `
                        <p><strong>${repo.repositoryName}</strong></p>
                        <p><small>Protocol: ${repo.protocolVersion}</small></p>
                    `;
                    $('#repoDetails').html(html);
                    $('#repositoryInfo').show();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Connection failed. Please check the URL.');
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
            toastr.error('Please enter a repository URL first');
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
                    let options = '<option value="">All collections</option>';
                    
                    sets.forEach(function(set) {
                        options += `<option value="${set.setSpec}">${set.setName}</option>`;
                    });
                    
                    $('#set').html(options);
                    toastr.success(`Loaded ${sets.length} collection(s)`);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Failed to load collections');
            },
            complete: function() {
                $('#loadSets').prop('disabled', false)
                    .html('<i class="fas fa-sync"></i> Load Collections');
            }
        });
    });
});
</script>
@endpush