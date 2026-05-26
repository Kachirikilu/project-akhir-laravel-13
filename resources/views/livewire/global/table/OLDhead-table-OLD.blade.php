@if ($withTh ?? true)
    <th class="px-6 py-3">
@endif

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
        class="w-full cursor-pointer group flex {{ $isCenter ?? false ? 'justify-center' : '' }} gap-1 text-xs font-medium text-gray-500 uppercase hover:text-indigo-600 whitespace-nowrap transition-colors duration-200">

        <span :class="clicked ? 'text-indigo-600 font-bold' : ''">
            {{ $headString ?? $sortFieldString }}
        </span>

        <span
            :class="[
                (sortField === '{{ $sortFieldString }}' || clicked) ?
                'opacity-100 text-indigo-600 font-bold' :
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
