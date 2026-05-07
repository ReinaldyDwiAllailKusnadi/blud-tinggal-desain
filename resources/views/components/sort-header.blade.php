@props(['column', 'label', 'sortBy', 'sortDir'])

@php
    $isActive = $sortBy === $column;
    $nextDir = ($isActive && $sortDir === 'asc') ? 'desc' : 'asc';
    $params = array_merge(request()->query(), ['sort_by' => $column, 'sort_dir' => $nextDir]);
@endphp

<a href="{{ request()->url() . '?' . http_build_query($params) }}" class="inline-flex items-center gap-1 hover:text-yellow-300 transition-colors">
    {{ $label }}
    @if ($isActive)
        @if ($sortDir === 'asc')
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 6.414l-3.293 3.293a1 1 0 01-1.414 0z"/></svg>
        @else
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 13.586l3.293-3.293a1 1 0 011.414 0z"/></svg>
        @endif
    @else
        <svg class="w-3 h-3 opacity-30" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
    @endif
</a>
