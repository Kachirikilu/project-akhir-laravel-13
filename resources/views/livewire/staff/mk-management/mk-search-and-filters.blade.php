<div x-data="{ activeFilter: @entangle('filterMK') }"
    class="bg-[var(--main-table-color)] table-border text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">

    <div x-transition:enter="transition ease-out duration-1000"
        x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
        class="table-border flex items-end justify-between border-b mb-4 gap-4">
        <div class="min-w-0 flex-1 overflow-hidden">
            @if (Auth::user()->admin)
                @include('livewire.global.search-and-filters.filter-mode', [
                    'filterByFunc' => 'filterByMK',
                    'filterString' => 'filterMK',
                    'totalTab' => $stats['mk-prodi'] ?? null,
                    'totalTab1' => $stats['mk-opsi'] ?? null,
                    'totalTab2' => $stats['mk-wajib'] ?? null,
                    'totalTab3' => $stats['mk-pilihan'] ?? null,
                    'totalTab4' => $stats['mk-uni'] ?? null,
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
            @elseif (Auth::user()->dosen)
                @include('livewire.global.search-and-filters.filter-mode', [
                    'filterByFunc' => 'filterByMK',
                    'filterString' => 'filterMK',
                    'totalTab' => $stats['mk-saya'] ?? null,
                    'totalTab1' => $stats['mk-prodi'] ?? null,
                    'totalTab2' => $stats['mk-opsi'] ?? null,
                    'totalTab3' => $stats['mk-wajib'] ?? null,
                    'totalTab4' => $stats['mk-pilihan'] ?? null,
                    'totalTab5' => $stats['mk-uni'] ?? null,
                    'tab1String' => 'mk-prodi',
                    'tab2String' => 'mk-all',
                    'tab3String' => 'mk-wajib',
                    'tab4String' => 'mk-pilihan',
                    'tab5String' => 'mk-universitas',
                    'tabName' => 'MK Saya',
                    'tab1Name' => Auth::user()->prodi,
                    'tab2Name' => 'Semua MK',
                    'tab3Name' => 'Wajib',
                    'tab4Name' => 'Pilihan',
                    'tab5Name' => 'Universitas',
                ])
            @endif
        </div>
        <div class="shrink-0">
            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150],
                'key' => 'page-control-mSk',
                'autoSmall' => 'lg',
            ])
        </div>
    </div>


    <div class="grid grid-cols-1 grid-rows-1 relative isolate z-40">

        <div x-show="activeFilter == '' || activeFilter == 'mk-saya' || activeFilter == 'mk-prodi' || activeFilter == 'mk-universitas'"
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
                    'searchValues' =>  ['simple', 'smart', 'complex'],
                    'searchOptions' => ['Cari Kode Mata Kuliah', 'Pencarian Cerdas', 'Pencarian Kompleks'],
                ])
            </div>
        </div>

        <div x-show="activeFilter !== '' && activeFilter !== 'mk-saya' && activeFilter !== 'mk-prodi' && activeFilter !== 'mk-universitas'"
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
                        'searchValues' =>  ['simple', 'smart', 'complex'],
                        'searchOptions' => ['Cari Kode Mata Kuliah', 'Pencarian Cerdas', 'Pencarian Kompleks'],
                    ])
                </div>

                <div class="order-3 sm:order-2 sm:col-span-3 relative">
                    <livewire:global.search-filters.prodi-search-filter wire:key="pr-search-filter" lazy />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full">

                <div class="sm:col-span-4 relative">
                    <livewire:global.search-filters.departemen-search-filter wire:key="dp-search-filter" lazy />
                </div>

                <div class="sm:col-span-4 relative">
                    <livewire:global.search-filters.fakultas-search-filter wire:key="fk-search-filter" lazy />
                </div>
            </div>
        </div>
    </div>
</div>
