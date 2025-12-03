{{-- resources/views/loc/islamic/preview-selected.blade.php --}}
@php
    $records = session('loc_islamic_search.records', []);
    $selectedIndices = request('selected_indices', []);
    $selectedRecords = [];
    
    foreach ($selectedIndices as $index) {
        if (isset($records[$index])) {
            $selectedRecords[] = $records[$index];
        }
    }
@endphp

<div class="container-fluid">
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Previewing {{ count($selectedRecords) }} selected records from Library of Congress.
        These records will be imported into your collection.
    </div>
    
    <div class="row">
        @foreach($selectedRecords as $index => $record)
        @php
            $metadata = $record['metadata'] ?? [];
            $title = is_array($metadata['title'] ?? []) ? ($metadata['title'][0] ?? 'Untitled') : ($metadata['title'] ?? 'Untitled');
            $description = is_array($metadata['description'] ?? []) ? ($metadata['description'][0] ?? '') : ($metadata['description'] ?? '');
            $creators = is_array($metadata['creator'] ?? []) ? $metadata['creator'] : (isset($metadata['creator']) ? [$metadata['creator']] : []);
            $date = is_array($metadata['date'] ?? []) ? ($metadata['date'][0] ?? '') : ($metadata['date'] ?? '');
            $type = is_array($metadata['type'] ?? []) ? ($metadata['type'][0] ?? '') : ($metadata['type'] ?? '');
            $language = is_array($metadata['language'] ?? []) ? ($metadata['language'][0] ?? '') : ($metadata['language'] ?? '');
        @endphp
        <div class="col-md-12 mb-3">
            <div class="card border-primary">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <span class="badge badge-primary">{{ $loop->iteration }}</span>
                        {{ $title }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            @if($description)
                            <p class="mb-2">{{ Str::limit($description, 200) }}</p>
                            @endif
                            
                            <div class="row small text-muted">
                                @if(!empty($creators))
                                <div class="col-md-6">
                                    <strong>Creator(s):</strong><br>
                                    @foreach($creators as $creator)
                                    <span class="badge badge-secondary">{{ $creator }}</span>
                                    @endforeach
                                </div>
                                @endif
                                
                                <div class="col-md-6">
                                    @if($date)
                                    <strong>Date:</strong> {{ $date }}<br>
                                    @endif
                                    @if($type)
                                    <strong>Type:</strong> {{ $type }}<br>
                                    @endif
                                    @if($language)
                                    <strong>Language:</strong> {{ $language }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Import Details</h6>
                                    <ul class="list-unstyled small">
                                        <li><strong>Source:</strong> Library of Congress</li>
                                        <li><strong>Accession:</strong> LOC-{{ date('Ymd') }}-{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</li>
                                        <li><strong>Status:</strong> <span class="text-success">Ready to import</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    @if(empty($selectedRecords))
    <div class="alert alert-warning text-center">
        <i class="fas fa-exclamation-triangle"></i>
        No records selected for preview.
    </div>
    @endif
</div>