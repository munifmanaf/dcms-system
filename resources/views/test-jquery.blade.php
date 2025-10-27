@extends('layouts.app')

@section('content')
<div class="container">
    <h1>jQuery Test</h1>
    <button id="test-btn" class="btn btn-primary">Test jQuery</button>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    console.log('jQuery version:', $.fn.jquery);
    
    $('#test-btn').click(function() {
        alert('jQuery is working! Version: ' + $.fn.jquery);
    });
});
</script>
@endpush