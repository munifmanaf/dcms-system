@php
    $fileType = $fileType ?? '';
@endphp

@if($fileType)
    @if(str_contains($fileType, 'image'))
    <span class="badge badge-info badge-sm">
        <i class="fas fa-image mr-1"></i>Image
    </span>
    @elseif(str_contains($fileType, 'pdf'))
    <span class="badge badge-danger badge-sm">
        <i class="fas fa-file-pdf mr-1"></i>PDF
    </span>
    @elseif(str_contains($fileType, 'word') || str_contains($fileType, 'document'))
    <span class="badge badge-primary badge-sm">
        <i class="fas fa-file-word mr-1"></i>Document
    </span>
    @elseif(str_contains($fileType, 'excel') || str_contains($fileType, 'spreadsheet'))
    <span class="badge badge-success badge-sm">
        <i class="fas fa-file-excel mr-1"></i>Spreadsheet
    </span>
    @elseif(str_contains($fileType, 'video'))
    <span class="badge badge-warning badge-sm">
        <i class="fas fa-file-video mr-1"></i>Video
    </span>
    @elseif(str_contains($fileType, 'audio'))
    <span class="badge badge-secondary badge-sm">
        <i class="fas fa-file-audio mr-1"></i>Audio
    </span>
    @else
    <span class="badge badge-secondary badge-sm">
        <i class="fas fa-file mr-1"></i>File
    </span>
    @endif
@else
    <span class="badge badge-light border badge-sm">
        <i class="fas fa-sticky-note mr-1"></i>Text
    </span>
@endif