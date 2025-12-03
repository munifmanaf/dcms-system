{{-- resources/views/loc/islamic/preview.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i> Collection Preview
                        @if(isset($collection))
                        - {{ $collection['name'] }}
                        @endif
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('loc.islamic.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Search
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($collection))
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Collection Information</h5>
                                <p><strong>Collection:</strong> {{ $collection['name'] }}</p>
                                <p><strong>Description:</strong> {{ $collection['description'] ?? 'Library of Congress Islamic Collection' }}</p>
                                <p><strong>Endpoint:</strong> <small>{{ $collection['endpoint'] }}</small></p>
                                <p><strong>Preview:</strong> Showing {{ $total }} sample records</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(isset($records) && count($records) > 0)
                    <div class="row">
                        @foreach($records as $record)
                        @php
                            $metadata = $record['metadata'] ?? [];
                            $title = is_array($metadata['title'] ?? []) ? ($metadata['title'][0] ?? 'Untitled') : ($metadata['title'] ?? 'Untitled');
                            $description = is_array($metadata['description'] ?? []) ? ($metadata['description'][0] ?? '') : ($metadata['description'] ?? '');
                            $creators = is_array($metadata['creator'] ?? []) ? $metadata['creator'] : (isset($metadata['creator']) ? [$metadata['creator']] : []);
                            $date = is_array($metadata['date'] ?? []) ? ($metadata['date'][0] ?? '') : ($metadata['date'] ?? '');
                            $type = is_array($metadata['type'] ?? []) ? ($metadata['type'][0] ?? '') : ($metadata['type'] ?? '');
                        @endphp
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0" style="height: 40px; overflow: hidden;">
                                        {{ Str::limit($title, 80) }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if($description)
                                    <p class="card-text small" style="height: 60px; overflow: hidden;">
                                        {{ Str::limit($description, 120) }}
                                    </p>
                                    @endif
                                    
                                    <div class="mt-2">
                                        @if(!empty($creators))
                                        <div class="mb-1">
                                            <strong class="small">Creator(s):</strong>
                                            @foreach(array_slice($creators, 0, 2) as $creator)
                                            <span class="badge badge-secondary">{{ $creator }}</span>
                                            @endforeach
                                            @if(count($creators) > 2)
                                            <small class="text-muted">+{{ count($creators) - 2 }} more</small>
                                            @endif
                                        </div>
                                        @endif
                                        
                                        <div class="row small text-muted">
                                            @if($date)
                                            <div class="col-6">
                                                <strong>Date:</strong> {{ $date }}
                                            </div>
                                            @endif
                                            @if($type)
                                            <div class="col-6">
                                                <strong>Type:</strong> {{ $type }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <small class="text-muted">
                                        <i class="fas fa-database"></i> Library of Congress
                                    </small>
                                    <button type="button" class="btn btn-sm btn-outline-primary float-right view-details" 
                                            data-record="{{ json_encode($record) }}">
                                        <i class="fas fa-search"></i> Details
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('loc.islamic.index') }}" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search This Collection
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <h5>No Records Available</h5>
                        <p>No records found in this collection for preview.</p>
                        <a href="{{ route('loc.islamic.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Collections
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Record Details Modal -->
<div class="modal fade" id="recordDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="recordDetailsContent">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // View record details
    $('.view-details').click(function() {
        let record = $(this).data('record');
        let metadata = record.metadata || {};
        
        let html = `
            <h6>${metadata.title ? (Array.isArray(metadata.title) ? metadata.title[0] : metadata.title) : 'Untitled'}</h6>
            <hr>
            
            <div class="row">
                <div class="col-md-12">
                    <h6>Basic Information</h6>
                    <table class="table table-sm">
                        ${metadata.description ? `
                        <tr>
                            <th width="30%">Description:</th>
                            <td>${Array.isArray(metadata.description) ? metadata.description[0] : metadata.description}</td>
                        </tr>
                        ` : ''}
                        
                        ${metadata.creator && metadata.creator.length > 0 ? `
                        <tr>
                            <th>Creator(s):</th>
                            <td>
                                ${Array.isArray(metadata.creator) ? metadata.creator.join(', ') : metadata.creator}
                            </td>
                        </tr>
                        ` : ''}
                        
                        ${metadata.date ? `
                        <tr>
                            <th>Date:</th>
                            <td>${Array.isArray(metadata.date) ? metadata.date[0] : metadata.date}</td>
                        </tr>
                        ` : ''}
                        
                        ${metadata.type ? `
                        <tr>
                            <th>Type:</th>
                            <td>${Array.isArray(metadata.type) ? metadata.type[0] : metadata.type}</td>
                        </tr>
                        ` : ''}
                        
                        ${metadata.language ? `
                        <tr>
                            <th>Language:</th>
                            <td>${Array.isArray(metadata.language) ? metadata.language[0] : metadata.language}</td>
                        </tr>
                        ` : ''}
                    </table>
                </div>
            </div>
            
            ${metadata.subject && metadata.subject.length > 0 ? `
            <div class="row">
                <div class="col-md-12">
                    <h6>Subjects</h6>
                    <div>
                        ${Array.isArray(metadata.subject) ? 
                            metadata.subject.map(subj => `<span class="badge badge-info mr-1">${subj}</span>`).join('') : 
                            `<span class="badge badge-info">${metadata.subject}</span>`}
                    </div>
                </div>
            </div>
            ` : ''}
            
            ${metadata.identifier && metadata.identifier.length > 0 ? `
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Identifiers</h6>
                    <ul class="list-unstyled">
                        ${Array.isArray(metadata.identifier) ? 
                            metadata.identifier.map(id => `<li><small>${id}</small></li>`).join('') : 
                            `<li><small>${metadata.identifier}</small></li>`}
                    </ul>
                </div>
            </div>
            ` : ''}
            
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Source Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <th width="30%">Source:</th>
                            <td>Library of Congress</td>
                        </tr>
                        <tr>
                            <th>Collection:</th>
                            <td>{{ $collection['name'] ?? 'Islamic Collection' }}</td>
                        </tr>
                        <tr>
                            <th>Record ID:</th>
                            <td><small>${record.identifier || 'N/A'}</small></td>
                        </tr>
                        <tr>
                            <th>Date Stamp:</th>
                            <td>${record.datestamp || 'N/A'}</td>
                        </tr>
                    </table>
                </div>
            </div>
        `;
        
        $('#recordDetailsContent').html(html);
        $('#recordDetailsModal').modal('show');
    });
});
</script>
@endpush