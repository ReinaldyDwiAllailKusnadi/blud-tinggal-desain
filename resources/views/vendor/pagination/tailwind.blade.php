@if ($paginator->hasPages())
<nav class="flex items-center justify-between mt-4 px-2">
    <div class="text-sm text-gray-600 dark:text-slate-400">
        Menampilkan {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} dari {{ $paginator->total() }} data
    </div>
    <div class="flex items-center gap-1">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 rounded text-gray-400 bg-gray-100 dark:bg-slate-700 dark:text-slate-500 cursor-not-allowed text-sm">&laquo;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 rounded text-gray-700 bg-gray-100 hover:bg-blue-100 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 text-sm">&laquo;</a>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-1 text-gray-500 text-sm">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1 rounded bg-blue-600 text-white text-sm font-semibold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 rounded text-gray-700 bg-gray-100 hover:bg-blue-100 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 text-sm">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 rounded text-gray-700 bg-gray-100 hover:bg-blue-100 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 text-sm">&raquo;</a>
        @else
            <span class="px-3 py-1 rounded text-gray-400 bg-gray-100 dark:bg-slate-700 dark:text-slate-500 cursor-not-allowed text-sm">&raquo;</span>
        @endif
    </div>
</nav>
@endif
