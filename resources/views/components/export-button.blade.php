@props([
    'route' => '#',
    'label' => 'Export',
    'params' => [],
])

@php
    // Merge provided parameters with current request query string if needed
    $url = $route !== '#' ? route($route, array_merge(request()->query(), $params)) : '#';
@endphp

<a href="{{ $url }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 rounded-[var(--radius-s)] border border-line bg-white px-3.5 py-2 text-xs font-semibold text-ink shadow-sm hover:bg-paper transition-colors']) }}>
    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
    </svg>
    <span>{{ $label }}</span>
</a>
