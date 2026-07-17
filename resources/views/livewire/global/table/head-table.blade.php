@if ($withTh ?? true)
    <th wire:key="head-table-{{ $sortFieldString }}" rowspan="{{ $rowSpan ?? 1 }}" colspan="{{ $colSpan ?? 1 }}"
        class="bg-[var(--main-table-color)] table-border relative border-b p-6
        {{ $isSticky ?? false ? 'lg:sticky lg:left-0 lg:top-0 lg:z-30' : '' }}
        {{ ($isBorderX ?? false) || ($isMain ?? false) ? 'border-x' : '' }}
        {{ $isBorderL ?? false ? 'border-l' : '' }}
        {{ $isBorderR ?? false ? 'border-r' : '' }}
    ">
@endif

<div x-cloak x-data="{
    sortField: @entangle('sortField'),
    sortDirection: @entangle('sortDirection'),
    clicked: false,

    async doSort(direction) {
        this.clicked = true;
        await $wire.sortBy('{{ $sortFieldString }}', direction);
        this.clicked = false;
    }
}"
    class="w-full h-full flex {{ $isCenter ?? false ? 'justify-center' : '' }} items-center gap-1 text-xs sm:text-sm font-medium uppercase whitespace-nowrap">

    {{-- Judul --}}
    <span
        :class="{
            'text-[var(--focus-color)] {{ $isMain ?? false ? 'font-bold' : '' }}': (
                sortField === '{{ $sortFieldString }}' || clicked),
        
            'font-bold text-[var(--contrast-main-text)]':
                !(sortField === '{{ $sortFieldString }}' || clicked)
        }"
        class="{{ $isCenter ?? false ? 'ml-5' : '' }} transition-colors duration-300 cursor-default">
        {{ strtoupper($headString ?? str($sortFieldString)->replace(['-', '_'], ' ')) }}
    </span>

    {{-- Tombol ASC & DESC --}}
    <div class="flex flex-col">

        {{-- ASC --}}
        <button type="button" @click.stop.prevent="doSort('asc')"
            class="flex items-center justify-center w-4 h-3 rounded">

            <span
                :class="{
                    'text-[var(--focus-color)]': sortField === '{{ $sortFieldString }}' &&
                        sortDirection === 'asc',
                
                    'opacity-50 hover:opacity-100 {{ $isMain ?? false ? 'text-[var(--contrast-main-text)]' : 'text-[var(--contrast-second-text)]' }}':
                        !(sortField === '{{ $sortFieldString }}' &&
                            sortDirection === 'asc')
                }"
                class="cursor-pointer text-[9px] leading-none transition-all duration-300">
                ▲
            </span>

        </button>

        {{-- DESC --}}
        <button type="button" @click.stop.prevent="doSort('desc')"
            class="flex items-center justify-center w-4 h-3 rounded">

            <span
                :class="{
                    'text-[var(--focus-color)]': sortField === '{{ $sortFieldString }}' &&
                        sortDirection === 'desc',
                
                    'opacity-50 hover:opacity-100 {{ $isMain ?? false ? 'text-[var(--contrast-main-text)]' : 'text-[var(--contrast-second-text)]' }}':
                        !(sortField === '{{ $sortFieldString }}' &&
                            sortDirection === 'desc')
                }"
                class="cursor-pointer text-[9px] leading-none transition-all duration-300">
                ▼
            </span>

        </button>

    </div>

    {{-- Garis bawah aktif --}}
    <div class="absolute bottom-0 left-0 w-full h-[3px] bg-[var(--focus-color)] origin-left"
        x-show="sortField === '{{ $sortFieldString }}' || clicked"
        x-transition:enter="transition transform ease-out duration-200" x-transition:enter-start="scale-x-0"
        x-transition:enter-end="scale-x-100" x-transition:leave="transition transform ease-in duration-200"
        x-transition:leave-start="scale-x-100" x-transition:leave-end="scale-x-0">
    </div>

</div>

@if ($withTh ?? true)
    </th>
@endif
