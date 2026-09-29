@php
    $alpine = $alpine ?? false;
    $withDiv = $withDiv ?? false;

    if ($withDiv) {
        $withTh = false;
    } else {
        $withTh = $withTh ?? true;
    }

    $main = $isMain ?? false;
    $borderX = $isBorderX ?? false;
    $borderL = $isBorderL ?? false;
    $borderR = $isBorderR ?? false;
@endphp

@if ($withTh)
    <th wire:key="head-table-{{ $sortFieldString }}" rowspan="{{ $rowSpan ?? 1 }}" colspan="{{ $colSpan ?? 1 }}"
        class="bg-[var(--main-table-color)] table-border relative border-b p-6
        {{ $isSticky ?? false ? 'lg:sticky lg:left-0 lg:top-0 lg:z-[30]' : '' }}
        {{ $borderX || $main ? 'border-x' : '' }}
        {{ $borderL ? 'border-l' : '' }}
        {{ $borderR ? 'border-r' : '' }}
    ">
@endif

@if ($withDiv)
    <div class="{{ $borderX || $main ? 'border-x' : '' }}
        {{ $borderL ? 'border-l' : '' }}
        {{ $borderR ? 'border-r' : '' }} {{ ($divStyle ?? false) ? $divStyle : '' }} p-0 table-head shrink-0 relative shrink-0 flex items-stretch justify-center table-border h-full self-stretch">
@endif

<div x-cloak x-data="{
    @if ($alpine) get sortField() { return this.$store.{{ $alpine }}.sortField },
        set sortField(val) { this.$store.{{ $alpine }}.sortField = val },
        get sortDirection() { return this.$store.{{ $alpine }}.sortDirection },
        set sortDirection(val) { this.$store.{{ $alpine }}.sortDirection = val },
    @else
        sortField: @entangle('sortField'),
        sortDirection: @entangle('sortDirection'), @endif

    clicked: false,

    async doSort(direction) {
        this.clicked = true;

        @if ($alpine)
            if (direction) {
                this.sortField = '{{ $sortFieldString }}';
                this.sortDirection = direction;
            } else {
                if (this.sortField === '{{ $sortFieldString }}') {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortField = '{{ $sortFieldString }}';
                    const defaultDescFields = ['pertemuan_ke', 'total_absensi', 'metode', 'bobot', 'id'];
                    this.sortDirection = defaultDescFields.includes('{{ $sortFieldString }}') ? 'desc' : 'asc';
                }
            }
        @else
            await $wire.sortBy('{{ $sortFieldString }}', direction); 
        @endif

        this.clicked = false;
    }
}"
    {{-- PERBAIKAN: Ubah 'h-10' menjadi 'h-full' agar konten menyatu penuh dari atas ke bawah --}}
    class="w-full h-full {{ $withDiv ? (($rowSpan ?? false) ? 'py-12' : 'py-5')  : '' }} px-6 flex {{ $isCenter ?? false ? 'justify-center' : '' }} items-center gap-1 text-xs sm:text-sm font-medium uppercase whitespace-nowrap">
    {{-- class="w-full {{ $withDiv ? 'min-h-10 px-2' : 'h-full' }} flex {{ $isCenter ?? false ? 'justify-center' : '' }} items-center gap-1 text-xs sm:text-sm font-medium uppercase whitespace-nowrap"> --}}

    {{-- Judul --}}
    <span @click.stop.prevent="doSort()"
        :class="{
            'text-[var(--focus-color)] {{ $main ? 'font-bold' : '' }}': (
                sortField === '{{ $sortFieldString }}' || clicked),
        
            'font-bold text-[var(--contrast-main-text)]':
                !(sortField === '{{ $sortFieldString }}' || clicked)
        }"
        class="transition-colors duration-[300ms] cursor-pointer select-none">
        {{ strtoupper($headString ?? str($sortFieldString)->replace(['-', '_'], ' ')) }}
    </span>

    {{-- Tombol ASC & DESC --}}
    <div class="flex flex-col shrink-0">

        {{-- ASC --}}
        <button type="button" @click.stop.prevent="doSort('asc')"
            class="flex items-center justify-center w-4 h-3 rounded outline-none">

            <span
                :class="{
                    'text-[var(--focus-color)] font-bold': sortField === '{{ $sortFieldString }}' &&
                        sortDirection === 'asc',
                
                    'opacity-30 hover:opacity-100 {{ $main ? 'text-[var(--contrast-main-text)]' : 'text-[var(--contrast-second-text)]' }}':
                        !(sortField === '{{ $sortFieldString }}' &&
                            sortDirection === 'asc')
                }"
                class="cursor-pointer text-[9px] leading-none transition-all duration-[300ms]">
                ▲
            </span>

        </button>

        {{-- DESC --}}
        <button type="button" @click.stop.prevent="doSort('desc')"
            class="flex items-center justify-center w-4 h-3 rounded outline-none">

            <span
                :class="{
                    'text-[var(--focus-color)] font-bold': sortField === '{{ $sortFieldString }}' &&
                        sortDirection === 'desc',
                
                    'opacity-30 hover:opacity-100 {{ $main ? 'text-[var(--contrast-main-text)]' : 'text-[var(--contrast-second-text)]' }}':
                        !(sortField === '{{ $sortFieldString }}' &&
                            sortDirection === 'desc')
                }"
                class="cursor-pointer text-[9px] leading-none transition-all duration-[300ms]">
                ▼
            </span>

        </button>

    </div>

    {{-- Garis bawah aktif --}}
    <div class="absolute bottom-0 left-0 w-full h-[3px] bg-[var(--focus-color)] origin-left"
        x-show="sortField === '{{ $sortFieldString }}' || clicked"
        x-transition:enter="transition transform ease-out duration-[200ms]" x-transition:enter-start="scale-x-0"
        x-transition:enter-end="scale-x-100" x-transition:leave="transition transform ease-in duration-[200ms]"
        x-transition:leave-start="scale-x-100" x-transition:leave-end="scale-x-0">
    </div>

</div>

@if ($withDiv)
    </div>
@endif

@if ($withTh)
    </th>
@endif