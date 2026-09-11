@props([
    'name' => '',
    'id' => null,
    'placeholder' => 'Select an option',
    'allowClear' => false,
])

<div x-data>
    <select
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        placeholder="{{ $placeholder }}"
        data-allow-clear="{{ $allowClear ? 'true' : 'false' }}"
        x-select2="$attributes.get('x-model')"
        {{ $attributes->merge(['class' => 'w-full rounded-[var(--radius-s)] border border-line bg-paper text-sm text-ink']) }}
    >
        {{ $slot }}
    </select>
</div>
