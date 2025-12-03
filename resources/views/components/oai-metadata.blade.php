@props(['item'])

<div class="oai-metadata">
    @if($item->getDcValue('creator'))
        <div class="metadata-row">
            <strong>Creator:</strong>
            @if(is_array($item->getDcValue('creator')))
                {{ implode('; ', $item->getDcValue('creator')) }}
            @else
                {{ $item->getDcValue('creator') }}
            @endif
        </div>
    @endif
    
    @if($item->getDcValue('publisher'))
        <div class="metadata-row">
            <strong>Publisher:</strong> {{ $item->getDcValue('publisher') }}
        </div>
    @endif
    
    @if($item->getDcValue('date'))
        <div class="metadata-row">
            <strong>Date:</strong> {{ $item->getDcValue('date') }}
        </div>
    @endif
    
    @if($item->getDcValue('subject'))
        <div class="metadata-row">
            <strong>Subject:</strong>
            @if(is_array($item->getDcValue('subject')))
                {{ implode('; ', $item->getDcValue('subject')) }}
            @else
                {{ $item->getDcValue('subject') }}
            @endif
        </div>
    @endif
    
    @if($item->getDcValue('identifier'))
        <div class="metadata-row">
            <strong>Identifier:</strong>
            @if(is_array($item->getDcValue('identifier')))
                {{ implode(', ', $item->getDcValue('identifier')) }}
            @else
                {{ $item->getDcValue('identifier') }}
            @endif
        </div>
    @endif
    
    @if($item->source === 'oai-pmh' && $item->oai_identifier)
        <div class="metadata-row">
            <strong>OAI Identifier:</strong> {{ $item->oai_identifier }}
        </div>
    @endif
</div>

<style>
.oai-metadata {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
}
.metadata-row {
    margin-bottom: 5px;
}
</style>