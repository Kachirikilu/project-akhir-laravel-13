<button type="button" x-cloak x-data="{
    sortField: @entangle('sortField'),
    sortDirection: @entangle('sortDirection'),
    clicked: false,
    async doSort() {
        this.clicked = true
        await $wire.sortBy('{{ $sortFieldString }}')
        this.clicked = false
    }
}" @click.prevent="doSort()" 
    class="relative cursor-pointer flex items-center pt-2.5 pb-3 px-3 text-sm font-medium transition-all duration-300 whitespace-nowrap outline-none bg-transparent group {{ $isCenter ?? false ? 'justify-center' : 'justify-start' }}"
    :class="sortField === '{{ $sortFieldString }}' || clicked
        ? 'text-[var(--focus-color)] font-semibold' 
        : 'text-[var(--contrast-second-text)] hover:text-[var(--focus-color)]'">

    <div class="flex items-center gap-2">
        
        <span class="tracking-wider text-xs uppercase transition-colors duration-300">
            {{ strtoupper($headString ?? str($sortFieldString)->replace(['-', '_'], ' ')) }}
        </span>

        <span 
            class="text-xs transition-all duration-300 ease-in-out inline-block"
            :class="[
                (sortField === '{{ $sortFieldString }}' || clicked) 
                    ? 'opacity-100 transform text-[var(--focus-color)]' 
                    : 'opacity-0 group-hover:opacity-60 text-[var(--contrast-second-text)] group-hover:text-[var(--focus-color)]',
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
            : 'scale-x-0 group-hover:scale-x-100'"
    ></span>
</button>