@if ($paginator->hasPages())
    <div class="flex items-center justify-between mt-4">
        <div class="text-sm text-gray-600">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </div>
        
        <div class="flex items-center space-x-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="px-3 py-1 text-sm text-blue-600 bg-white border border-gray-300 rounded hover:bg-gray-50">
                    Previous
                </a>
            @endif

            {{-- Page Numbers --}}
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
            @endphp

            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $current)
                    <span class="px-3 py-1 text-sm text-white bg-blue-600 rounded">{{ $page }}</span>
                @else
                    <a href="{{ $paginator->url($page) }}" 
                       class="px-3 py-1 text-sm text-blue-600 bg-white border border-gray-300 rounded hover:bg-gray-50">
                        {{ $page }}
                    </a>
                @endif
            @endfor

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="px-3 py-1 text-sm text-blue-600 bg-white border border-gray-300 rounded hover:bg-gray-50">
                    Next
                </a>
            @else
                <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded cursor-not-allowed">
                    Next
                </span>
            @endif
        </div>
    </div>
@endif