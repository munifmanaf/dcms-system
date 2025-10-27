@extends('layouts.app')

@section('page_title', "Versions - {$item->title}")
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
    <li class="breadcrumb-item"><a href="{{ route('items.show', $item) }}">{{ $item->title }}</a></li>
    <li class="breadcrumb-item active">Versions</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i> Version History - {{ $item->title }}
        </h3>
        <div class="card-tools">
            <a href="{{ route('items.show', $item) }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Item
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($versions->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-history fa-3x text-muted mb-3"></i>
            <h4>No versions found</h4>
            <p class="text-muted">This item doesn't have any versions yet.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Version</th>
                        <th>Changes</th>
                        <th>File</th>
                        <th>Size</th>
                        <th>Created By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($versions as $version)
                    <tr>
                        <td>
                            <span class="badge badge-primary">v{{ $version->version_number }}</span>
                            @if($version->restored_from_id)
                            <span class="badge badge-info" title="Restored from version {{ $version->restoredFrom->version_number ?? 'unknown' }}">
                                <i class="fas fa-undo"></i> Restored
                            </span>
                            @endif
                        </td>
                        <td>
                            @if($version->changes)
                            {{ Str::limit($version->changes, 50) }}
                            @else
                            <span class="text-muted">No changes recorded</span>
                            @endif
                        </td>
                        <td>
                            @if($version->hasFile())
                            <i class="fas fa-file"></i> {{ Str::limit($version->file_name, 30) }}
                            @else
                            <span class="text-muted">No file</span>
                            @endif
                        </td>
                        <td>
                            @if($version->file_size)
                            {{ $version->formatted_file_size }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $version->user->name }}</td>
                        <td>
                            <span title="{{ $version->created_at->format('M j, Y g:i A') }}">
                                {{ $version->created_at->diffForHumans() }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                @if($version->hasFile())
                                <a href="{{ route('items.versions.download', $version) }}" 
                                   class="btn btn-outline-primary" title="Download this version">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif
                                
                                <a href="{{ route('items.versions.compare', [$item, $version]) }}" 
                                   class="btn btn-outline-info" title="Compare with current">
                                    <i class="fas fa-code-compare"></i>
                                </a>
                                
                                @if(!$loop->first) {{-- Don't allow restoring to current version --}}
                                <form action="{{ route('items.versions.restore', [$item, $version]) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning" 
                                            title="Restore to this version"
                                            onclick="return confirm('Are you sure you want to restore to this version? This will create a new version of the current state.')">
                                        <i class="fas fa-rotate-left"></i>
                                    </button>
                                </form>
                                @endif
                                
                                @if($versions->count() > 1) {{-- Don't allow deleting if only one version --}}
                                <form action="{{ route('items.versions.destroy', [$item, $version]) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" 
                                            title="Delete this version"
                                            onclick="return confirm('Are you sure you want to delete this version?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $versions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection