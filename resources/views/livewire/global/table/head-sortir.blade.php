@php
    $alpineStore = $alpine ?? false;
@endphp

<button type="button" x-cloak x-data="{
    {{-- KONDISIONAL BINDING STATE SORTIR --}}
    @if($alpineStore)
        get sortField() { return this.$store.{{ $alpineStore }}.sortField },
        set sortField(val) { this.$store.{{ $alpineStore }}.sortField = val },
        get sortDirection() { return this.$store.{{ $alpineStore }}.sortDirection },
        set sortDirection(val) { this.$store.{{ $alpineStore }}.sortDirection = val },
    @else
        sortField: @entangle('sortField'),
        sortDirection: @entangle('sortDirection'),
    @endif

    clicked: false,

    async doSort() {
        this.clicked = true;

        @if($alpineStore)
            {{-- LOGIKA SORTIR LOKAL ALPINE STORE --}}
            if (this.sortField === '{{ $sortFieldString }}') {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = '{{ $sortFieldString }}';
                this.sortDirection = 'asc';
            }
        @else
            {{-- LOGIKA SORTIR LIVEWIRE BACKEND --}}
            await $wire.sortBy('{{ $sortFieldString }}');
        @endif

        this.clicked = false;
    }
}" @click.prevent="doSort()" 
    class="relative cursor-pointer flex items-center pt-2.5 pb-3 px-3 text-xs sm:text-sm font-medium transition-all duration-300 whitespace-nowrap outline-none bg-transparent group {{ $isCenter ?? false ? 'justify-center' : 'justify-start' }}"
    :class="sortField === '{{ $sortFieldString }}' || clicked
        ? 'text-[var(--focus-color)] font-semibold' 
        : 'text-[var(--contrast-second-text)] hover:text-[var(--focus-color)] active:text-[var(--focus-color)]/90'">

    <div class="flex items-center gap-2">
        <span class="tracking-wider text-[9px] sm:text-xs uppercase transition-colors duration-300">
            {{ strtoupper($headString ?? str($sortFieldString)->replace(['-', '_'], ' ')) }}
        </span>

        <span 
            class="text-[9px] sm:text-xs transition-all duration-300 ease-in-out inline-block"
            :class="[
                (sortField === '{{ $sortFieldString }}' || clicked) 
                    ? 'opacity-100 transform text-[var(--focus-color)]' 
                    : 'opacity-0 group-hover:opacity-60 text-[var(--contrast-second-text)] group-hover:text-[var(--focus-color)] active:text-[var(--focus-color)]/90',
                (sortField === '{{ $sortFieldString }}' && sortDirection === 'desc') 
                    ? 'rotate-180' 
                    : 'rotate-0'
            ]">
            ↑
        </span>
    </div>

    <span class="absolute bottom-0 left-0 w-full h-[1px] bg-[var(--border-table-color)]"></span>

    <span 
        class="bg-[var(--focus-color)] absolute bottom-0 left-0 h-[3px] transition-transform duration-300 ease-out origin-left w-full z-10"
        :class="sortField === '{{ $sortFieldString }}' || clicked
            ? 'scale-x-100' 
            : 'scale-x-0 group-hover:scale-x-100 group-active:scale-x-100'"
    ></span>
</button>