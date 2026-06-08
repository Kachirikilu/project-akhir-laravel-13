@props(['type' => 'md'])

@php
    $types = [
        'sm' => 'h-6 text-xs px-3',
        'md' => 'h-7 text-sm px-4',
        'lg' => 'h-8 text-base px-5',
    ];
@endphp

<span
    {{ $attributes->merge(['class' => 'whitespace-nowrap inline-flex items-center font-mono text-sm font-semibold rounded-md border border-[var(--border-table-color)] bg-[var(--second-table-color)] text-[var(--focus-color)] shadow-sm ' . $types[$type]]) }}>
    {{ $slot }}
</span>
