{{-- resources/views/loc/islamic/import-preview.blade.php --}}
<div class="modal fade" id="importPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye"></i> Preview Selected Records
                    <small class="text-light">(<span id="previewCount">0</span> records)</small>
                </h5>
                <button type="button" class="close text-light" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-success" id="confirmImport">
                    <i class="fas fa-check"></i> Confirm Import
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Preview selected records
    $('#previewSelected').click(function() {
        let selectedIndices = [];
        $('.record-checkbox:checked').each(function() {
            selectedIndices.push($(this).val());
        });
        
        if (selectedIndices.length === 0) {
            toastr.error('Please select records first');
            return;
        }
        
        // Load preview content
        $.ajax({
            url: '{{ route("loc.islamic.preview-selected") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                selected_indices: selectedIndices
            },
            beforeSend: function() {
                $('#previewContent').html(`
                    <div class="text-center py-5">
                        <i class="fas fa-spinner fa-spin fa-3x"></i>
                        <p class="mt-3">Loading preview...</p>
                    </div>
                `);
            },
            success: function(response) {
                $('#previewContent').html(response);
                $('#previewCount').text(selectedIndices.length);
                $('#importPreviewModal').modal('show');
            },
            error: function() {
                toastr.error('Failed to load preview');
            }
        });
    });
    
    // Confirm import
    $('#confirmImport').click(function() {
        $('#importPreviewModal').modal('hide');
        $('#selectForm').submit();
    });
});
</script>