@if ($withTh ?? true)
    <th 
        rowspan="{{ $rowspan ?? 1 }}" 
        colspan="{{ $colspan ?? 1 }}" 
        class="px-6 py-3 {{ ($isSubHeader ?? false) ? 'bg-gray-100/50' : '' }}
        {{ (($isBorderX ?? false)  || ($isMain ?? false) || ($sortField == $sortFieldString)) ? 'border-l border-r' : '' }}
        {{ ($isBorderL ?? false) ? 'border-l' : '' }}
        {{ ($isBorderR ?? false) ? 'border-r' : '' }}
        border-gray-300 
        "
    >
@endif

{{-- @php
    $isActive = $sortField == $sortFieldString;
@endphp

@if ($withTh ?? true)
    <th 
        rowspan="{{ $rowspan ?? 1 }}" 
        colspan="{{ $colspan ?? 1 }}" 
        class="px-6 py-3 transition-colors duration-200
            {{ (($isBorderX ?? false) || ($isMain ?? false) || $isActive) ? 'border-l border-r' : '' }}
            {{ ($isBorderL ?? false) ? 'border-l' : '' }}
            {{ ($isBorderR ?? false) ? 'border-r' : '' }}

            @if ($isActive) 
                border-blue-300 bg-blue-50
            @else 
                border-gray-200 bg-gray-50
                {{ ($isSubHeader ?? false) ? 'bg-gray-100/80' : '' }}
            @endif
        "
    >
@endif --}}

    <button x-data="{
        sortField: @entangle('sortField'),
        sortDirection: @entangle('sortDirection'),
        localDir: '{{ $sortDirection }}',
        clicked: false,
    
        init() {
            this.$watch('sortDirection', v => this.localDir = v)
        },
    
        async doSort() {
    
            this.clicked = true
    
            if (this.sortField === '{{ $sortFieldString }}') {
                this.localDir = this.localDir === 'asc' ? 'desc' : 'asc'
            } else {
                this.localDir = 'asc'
            }
    
            await $wire.sortBy('{{ $sortFieldString }}')
    
            this.clicked = false
        }
    }" @click="doSort()"
        class="w-full cursor-pointer group flex {{ $isCenter ?? false ? 'justify-center' : '' }} gap-1 text-xs font-medium text-gray-500 uppercase hover:text-[var(--hover-focus-color)] whitespace-nowrap transition-colors duration-200">

        <span :class="clicked ? 'text-[var(--focus-color)] font-bold' : '{{ ($isMain ?? false) ? 'font-bold text-gray-900' : '' }}'">
            {{ $headString ?? $sortFieldString }}
        </span>

        <span
            :class="[
                (sortField === '{{ $sortFieldString }}' || clicked) ?
                'opacity-100 text-[var(--focus-color)] font-bold' :
                'opacity-0 group-hover:opacity-80 text-gray-400',
            
                sortField === '{{ $sortFieldString }}' && localDir === 'desc' ?
                'rotate-180' :
                'rotate-0'
            ]"
            class="inline-block transition-all transition-transform duration-300 ease-in-out">↑</span>

    </button>

@if ($withTh ?? true)
    </th>
@endif
