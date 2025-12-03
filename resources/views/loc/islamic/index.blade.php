{{-- resources/views/loc/islamic/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">
                        <i class="fas fa-book-quran"></i> Islamic Digital Collections
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-light">Guaranteed Working</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Warning Alert for LoC -->
                    @if(session('warning'))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                    </div>
                    @endif
                    
                    <!-- Quick Search Notification -->
                    @if(session('quick_search'))
                    <div class="alert alert-info">
                        <i class="fas fa-bolt"></i> Quick search loaded! Search for "{{ session('quick_search')['keyword'] }}" 
                        in {{ $collections[session('quick_search')['collection']]['name'] ?? 'selected collection' }}.
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Search Islamic Collections</h5>
                                </div>
                                <div class="card-body">
                                    <form id="locSearchForm" action="{{ route('loc.islamic.search') }}" method="POST">
                                        @csrf
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="collection">Select Repository *</label>
                                                    <select name="collection" id="collection" class="form-control" required>
                                                        <!-- Guaranteed Working -->
                                                        <optgroup label="✅ Guaranteed Working">
                                                            @foreach($collections as $key => $collection)
                                                            @if($collection['working'])
                                                            <option value="{{ $key }}" 
                                                                    data-working="true" 
                                                                    data-description="{{ $collection['description'] }}"
                                                                    {{ session('quick_search.collection') == $key ? 'selected' : '' }}>
                                                                {{ $collection['name'] }}
                                                            </option>
                                                            @endif
                                                            @endforeach
                                                        </optgroup>
                                                        
                                                        <!-- May Not Work -->
                                                        <optgroup label="⚠️ May Be Blocked">
                                                            @foreach($collections as $key => $collection)
                                                            @if(!$collection['working'])
                                                            <option value="{{ $key }}" 
                                                                    data-working="false" 
                                                                    data-description="{{ $collection['description'] }}">
                                                                {{ $collection['name'] }}
                                                            </option>
                                                            @endif
                                                            @endforeach
                                                        </optgroup>
                                                    </select>
                                                    <div id="collectionWarning" class="alert alert-warning mt-2" style="display: none;">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        <strong>Warning:</strong> This repository may be blocked in your region. 
                                                        Try Zenodo or DOAJ for guaranteed access.
                                                    </div>
                                                    <button type="button" id="testCollection" class="btn btn-sm btn-outline-info mt-1">
                                                        <i class="fas fa-plug"></i> Test Connection
                                                    </button>
                                                    <div id="connectionResult" class="mt-2" style="display: none;"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="keyword">Search Query *</label>
                                                    <input type="text" name="keyword" id="keyword" 
                                                           class="form-control" 
                                                           placeholder="e.g., quran tafsir, hadith, islamic law"
                                                           value="{{ session('quick_search.keyword') ?? '' }}"
                                                           required>
                                                    <small class="form-text text-muted">
                                                        Use OR for multiple terms: <code>quran OR tafsir</code>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
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
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="subject">Islamic Subject</label>
                                                    <select name="subject" id="subject" class="form-control">
                                                        <option value="">Any Subject</option>
                                                        <option value="quran" {{ session('quick_search.subject') == 'quran' ? 'selected' : '' }}>Quran & Tafsir</option>
                                                        <option value="hadith" {{ session('quick_search.subject') == 'hadith' ? 'selected' : '' }}>Hadith Sciences</option>
                                                        <option value="fiqh">Islamic Jurisprudence</option>
                                                        <option value="aqeedah">Islamic Theology</option>
                                                        <option value="seerah">Prophetic Biography</option>
                                                        <option value="sufism">Islamic Spirituality</option>
                                                        <option value="history">Islamic History</option>
                                                        <option value="science">Islamic Science</option>
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
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-search"></i> Search Islamic Collections
                                            </button>
                                            <a href="{{ route('oai.harvest.index') }}" class="btn btn-outline-secondary">
                                                Other Repositories
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Quick Search Box -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-bolt"></i> Quick Islamic Searches
                                    </h5>
                                </div>
                                {{-- <div class="card-body">
                                    <div class="btn-group flex-wrap">
                                        <a href="{{ route('loc.islamic.quick-search', 'quran') }}" class="btn btn-outline-primary mb-1">
                                            <i class="fas fa-book-quran"></i> Quran Studies
                                        </a>
                                        <a href="{{ route('loc.islamic.quick-search', 'hadith') }}" class="btn btn-outline-primary mb-1">
                                            <i class="fas fa-book"></i> Hadith Collections
                                        </a>
                                        <a href="{{ route('loc.islamic.quick-search', 'fiqh') }}" class="btn btn-outline-primary mb-1">
                                            <i class="fas fa-balance-scale"></i> Islamic Law
                                        </a>
                                        <a href="{{ route('loc.islamic.quick-search', 'history') }}" class="btn btn-outline-primary mb-1">
                                            <i class="fas fa-landmark"></i> Islamic History
                                        </a>
                                        <a href="{{ route('loc.islamic.quick-search', 'science') }}" class="btn btn-outline-primary mb-1">
                                            <i class="fas fa-microscope"></i> Islamic Science
                                        </a>
                                    </div>
                                    <p class="text-muted small mt-2">
                                        <i class="fas fa-info-circle"></i> Quick searches use Zenodo/DOAJ for guaranteed results.
                                    </p>
                                </div> --}}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Repository Status -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-check-circle"></i> Recommended Repositories
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        @foreach($collections as $key => $collection)
                                        <div class="list-group-item {{ $collection['working'] ? 'list-group-item-success' : 'list-group-item-warning' }}">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">
                                                    @if($collection['working'])
                                                    <i class="fas fa-check-circle text-success"></i>
                                                    @else
                                                    <i class="fas fa-exclamation-triangle text-warning"></i>
                                                    @endif
                                                    {{ $collection['name'] }}
                                                </h6>
                                                @if($collection['working'])
                                                <span class="badge badge-success">Working</span>
                                                @else
                                                <span class="badge badge-warning">May Be Blocked</span>
                                                @endif
                                            </div>
                                            <p class="mb-1 small">{{ $collection['description'] }}</p>
                                            <small class="text-muted">{{ $collection['endpoint'] }}</small>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-lightbulb"></i>
                                        <strong>Tip:</strong> Start with Zenodo or DOAJ for guaranteed access to Islamic content.
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Imports -->
                            @if($harvestLogs->count() > 0)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-history"></i> Recent Imports
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        @foreach($harvestLogs as $log)
                                        <a href="{{ route('loc.harvest.show', $log->id) }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">
                                                    {{ Str::limit($log->endpoint, 30) }}
                                                </h6>
                                                <span class="badge badge-success">{{ $log->imported_records }}</span>
                                            </div>
                                            <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                        </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Show/hide warning based on repository selection
    $('#collection').change(function() {
        let selectedOption = $(this).find('option:selected');
        let isWorking = selectedOption.data('working');
        let description = selectedOption.data('description');
        
        if (!isWorking) {
            $('#collectionWarning').show();
        } else {
            $('#collectionWarning').hide();
        }
    });
    
    // Initialize
    $('#collection').trigger('change');
    
    // Test connection
    $('#testCollection').click(function() {
        let collectionSelect = $('#collection');
        let selectedOption = collectionSelect.find('option:selected');
        let endpoint = selectedOption.val();
        
        if (!endpoint) {
            toastr.error('Please select a repository first');
            return;
        }
        
        // Get collection from our list
        let collections = @json($collections);
        let collection = collections[endpoint];
        
        if (!collection) {
            toastr.error('Collection not found');
            return;
        }
        
        // Show testing status
        $('#testCollection').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin"></i> Testing...');
        
        $.ajax({
            url: '{{ route("loc.islamic.test-connection") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                endpoint: collection.endpoint
            },
            success: function(response) {
                let html = '';
                
                if (response.success) {
                    let info = response.info;
                    html = `
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle"></i> Connection Successful!</h6>
                            <p class="mb-1"><strong>Repository:</strong> ${info.repositoryName || collection.name}</p>
                            <p class="mb-0"><strong>Status:</strong> Ready to search</p>
                        </div>
                    `;
                } else {
                    html = `
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-times-circle"></i> Connection Failed</h6>
                            <p>${response.message}</p>
                            ${response.alternatives ? `
                            <hr>
                            <p><strong>Try These Instead:</strong></p>
                            <ul class="mb-0">
                                ${Object.values(response.alternatives).map(alt => 
                                    `<li><strong>${alt.name}</strong> - ${alt.description}</li>`
                                ).join('')}
                            </ul>
                            ` : ''}
                        </div>
                    `;
                }
                
                $('#connectionResult').html(html).show();
            },
            error: function() {
                $('#connectionResult').html(`
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-times-circle"></i> Connection Error</h6>
                        <p>Failed to connect to the server. The repository may be blocked.</p>
                        <p class="mb-0"><strong>Suggestion:</strong> Try Zenodo or DOAJ instead.</p>
                    </div>
                `).show();
            },
            complete: function() {
                $('#testCollection').prop('disabled', false)
                    .html('<i class="fas fa-plug"></i> Test Connection');
            }
        });
    });
});
</script>
@endpush