{{-- resources/views/oai/harvest/preview.blade.php --}}
<div class="container-fluid">
    <h5>Preview: {{ count($records) }} Records</h5>
    <p class="text-muted">Total available: {{ $totalAvailable ?? 'Unknown' }}</p>
    
    @foreach($records as $record)
    <div class="record-preview">
        <h6>
            {{ $record['metadata']['title'] ?? 'Untitled' }}
            <small class="text-muted">({{ $record['identifier'] }})</small>
        </h6>
        
        <div class="row">
            @if(isset($record['metadata']['creator']))
            <div class="col-md-6 metadata-item">
                <span class="metadata-label">Creator:</span>
                @if(is_array($record['metadata']['creator']))
                {{ implode(', ', $record['metadata']['creator']) }}
                @else
                {{ $record['metadata']['creator'] }}
                @endif
            </div>
            @endif
            
            @if(isset($record['metadata']['date']))
            <div class="col-md-6 metadata-item">
                <span class="metadata-label">Date:</span> {{ $record['metadata']['date'] }}
            </div>
            @endif
            
            @if(isset($record['metadata']['publisher']))
            <div class="col-md-6 metadata-item">
                <span class="metadata-label">Publisher:</span> {{ $record['metadata']['publisher'] }}
            </div>
            @endif
            
            @if(isset($record['metadata']['type']))
            <div class="col-md-6 metadata-item">
                <span class="metadata-label">Type:</span> {{ $record['metadata']['type'] }}
            </div>
            @endif
            
            @if(isset($record['metadata']['description']))
            <div class="col-md-12 metadata-item">
                <span class="metadata-label">Description:</span>
                @if(is_array($record['metadata']['description']))
                {{ implode(' ', $record['metadata']['description']) }}
                @else
                {{ Str::limit($record['metadata']['description'], 200) }}
                @endif
            </div>
            @endif
        </div>
        
        <small class="text-muted">
            <i class="fas fa-calendar"></i> {{ $record['datestamp'] }}
            @if($record['setSpec'])
            | <i class="fas fa-folder"></i> {{ $record['setSpec'] }}
            @endif
        </small>
    </div>
    @endforeach
    
    @if(empty($records))
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> No records found with the specified criteria.
    </div>
    @endif
</div>