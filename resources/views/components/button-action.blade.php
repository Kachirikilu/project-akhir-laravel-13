@props(['color' => 'blue', 'type' => 'md'])

@php
    $colors = [
        'amber' => 'border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/35',
        'blue'  => 'border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/35',
        'emerald' => 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/35',
    ];
    $types = [
        'sm' => 'h-6 text-xs px-2',
        'md' => 'h-8 text-sm px-3',
        'lg' => 'h-10 text-base px-4'
    ]
@endphp

<button {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-1.5 rounded-lg border font-medium shadow-sm cursor-pointer transition-all duration-200 " . $colors[$color] . " " . $types[$type]]) }}>
    {{ $slot }}
</button>