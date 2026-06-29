<div x-data="{ activeFilter: @entangle('filterMK') }"
    class="bg-[var(--main-table-color)] table-border text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">

    <div x-transition:enter="transition ease-out duration-1000"
        x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
        class="table-border flex items-end justify-between border-b mb-4 gap-4">
        <div class="min-w-0 flex-1 overflow-hidden">
            @include('livewire.global.search-and-filters.filter-mode', [
                'filterByFunc' => 'filterByMK',
                'filterString' => 'filterMK',
                'totalTab' => $stats['mk-prodi'],
                'totalTab1' => $stats['mk-opsi'],
                'totalTab2' => $stats['mk-wajib'],
                'totalTab3' => $stats['mk-pilihan'],
                'totalTab4' => $stats['mk-uni'],
                'tab1String' => 'mk-all',
                'tab2String' => 'mk-wajib',
                'tab3String' => 'mk-pilihan',
                'tab4String' => 'mk-universitas',
                'tabName' => Auth::user()->prodi,
                'tab1Name' => 'Semua MK',
                'tab2Name' => 'Wajib',
                'tab3Name' => 'Pilihan',
                'tab4Name' => 'Universitas',
            ])
        </div>
        <div class="shrink-0">
            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150],
                'key' => 'page-control-mk',
                'autoSmall' => 'lg',
            ])
        </div>
    </div>


    <div class="grid grid-cols-1 grid-rows-1 relative isolate z-40">

        <div x-show="activeFilter == '' || activeFilter == 'mk-universitas'"
            x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 w-full grid grid-cols-1 sm:grid-cols-7 gap-x-3 gap-y-2 items-center">
            <div class="sm:col-span-7 relative">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Mata Kuliah...',
                    'searchMode' => $searchMode,
                    'searchValues' => ['simple', 'full'],
                    'searchOptions' => ['Cari Kode Mata Kuliah', 'Pencarian Kompleks'],
                ])
            </div>
        </div>

        <div x-show="activeFilter !== '' && activeFilter !== 'mk-universitas'"
            x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4" class="col-start-1 row-start-1 w-full">
            <div class=" grid grid-cols-1 sm:grid-cols-7 gap-x-3 gap-y-2 items-center">
                <div class="sm:col-span-4 relative">
                    @include('livewire.global.search-and-filters.main-search', [
                        'placeholder' => 'Cari Mata Kuliah...',
                        'searchMode' => $searchMode,
                        'searchValues' => ['simple', 'full'],
                        'searchOptions' => ['Cari Kode Mata Kuliah', 'Pencarian Kompleks'],
                    ])
                </div>

                <div class="order-3 sm:order-2 sm:col-span-3 relative">
                    <livewire:global.search-filters.prodi-search-filter lazy />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full">

                <div class="sm:col-span-4 relative">
                    <livewire:global.search-filters.departemen-search-filter lazy />
                </div>

                <div class="sm:col-span-4 relative">
                    <livewire:global.search-filters.fakultas-search-filter lazy />
                </div>
            </div>
        </div>
    </div>
</div>
