@php
    $isSorted = $sortField === $sortFieldString;
    $isActive = $isSorted || ($isMain ?? false);
@endphp

@if ($withTh ?? true)
    <th rowspan="{{ $rowSpan ?? 1 }}" colspan="{{ $colSpan ?? 1 }}"
        class="bg-[var(--main-table-color)] table-border p-6 relative border-b 
        {{ $isSticky ?? false ? 'sticky left-0 top-0 z-30' : '' }} 
        {{ ($isBorderX ?? false) || ($isMain ?? false) ? 'border-x' : '' }}">
@endif

<button type="button" 
    wire:click="sortBy('{{ $sortFieldString }}')"
    class="w-full h-full cursor-pointer group flex items-center gap-2 text-xs font-medium uppercase whitespace-nowrap {{ $isCenter ?? false ? 'justify-center' : '' }}">
    
    <span class="transition-colors duration-200 {{ $isSorted ? 'text-[var(--focus-color)] font-bold' : 'text-[var(--contrast-main-text)]' }}">
        {{ strtoupper($headString ?? str($sortFieldString)->replace(['-', '_'], ' ')) }}
    </span>

    <span class="inline-block transition-transform duration-200 {{ $isSorted && $sortDirection === 'desc' ? 'rotate-180' : '' }} {{ $isSorted ? 'text-[var(--focus-color)]' : 'opacity-0 group-hover:opacity-50' }}">
        ↑
    </span>

    @if ($isSorted)
        <div class="absolute bottom-0 left-0 w-full h-[3px] bg-[var(--focus-color)]"></div>
    @endif
</button>

@if ($withTh ?? true)
    </th>
@endif