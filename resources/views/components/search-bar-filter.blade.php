@props([
    'route',
    'placeholder' => 'Search records...',
    'name' => 'search'
])

<div class="panel bg-white p-4">
    <form action="{{ $route }}" method="GET" {{ $attributes->merge(['class' => 'flex flex-col gap-3']) }}>
        <div class="flex flex-col sm:flex-row items-end gap-3">
            {{-- Search Input --}}
            <div class="relative w-full flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text"
                       name="{{ $name }}"
                       value="{{ request($name) }}"
                       placeholder="{{ $placeholder }}"
                       class="w-full rounded-[var(--radius-s)] border border-line bg-paper pl-9 pr-3.5 py-2 text-sm text-ink placeholder:text-muted/60 focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
            </div>

            {{-- Optional Named Slot: Custom Filters (Select2, Date Pickers, etc.) --}}
            @if (isset($filters))
                <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                    {{ $filters }}
                </div>
            @endif

            {{-- Submit & Clear Buttons --}}
            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-[var(--radius-s)] bg-forest px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-colors cursor-pointer">
                    Search
                </button>

                @if (request()->hasAny([$name, 'category_id', 'status'])) {{-- Add query params here to show clear button --}}
                    <a href="{{ $route }}"
                       class="rounded-[var(--radius-s)] border border-line bg-white px-3 py-2 text-xs font-semibold text-muted hover:bg-paper transition-colors">
                        Clear
                    </a>
                @endif
            </div>

            {{-- Optional Named Slot: Actions outside/inside the form (e.g. Export Button) --}}
            @if (isset($actions))
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end border-t sm:border-t-0 pt-2 sm:pt-0 border-line">
                    {{ $actions }}
                </div>
            @endif
        </div>

        {{-- Default Slot for extra hidden inputs or full-width drawer filters --}}
        @if ($slot->isNotEmpty())
            <div class="pt-2">
                {{ $slot }}
            </div>
        @endif
    </form>
</div>
