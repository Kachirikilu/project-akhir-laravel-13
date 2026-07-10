<div x-data="{ activeFilter: @entangle('filterStatus') }"
    class="bg-[var(--main-table-color)]/70 border-[var(--border-table-color)]/20 table-border text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">

    <div class="table-border flex items-end justify-between border-b mb-4 gap-4">
        <div class="min-w-0 flex-1 overflow-hidden">
            @include('livewire.global.search-and-filters.filter-mode', [
                'filterByFunc' => 'filterByStatus',
                'filterString' => 'filterStatus',
                'totalTab' => $stats[($role ?? null ? $role : 'user') . '-prodi'],
                'totalTab1' => $stats[($role ?? null ? $role : 'user') . '-opsi'],
                'totalTab2' => $stats[($role ?? null ? $role : 'user') . '-aktif'],
                'totalTab3' => $stats[($role ?? null ? $role : 'user') . '-non-aktif'],
                'tab1String' => ($role ?? null ? $role : 'user') . '-all',
                'tab2String' => ($role ?? null ? $role : 'user') . '-aktif',
                'tab3String' => ($role ?? null ? $role : 'user') . '-non-aktif',
                'tabName' => Auth::user()->prodi ?? 'Program Studi Saya',
                'tab1Name' => 'Semua Status',
                'tab2Name' => 'Aktif',
                'tab3Name' => 'Tidak Aktif',
            ])
        </div>
        {{-- <div class="shrink-0">
            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
                'key' => 'page-control-user',
                'autoSmall' => 'md',
            ])
        </div> --}}
        <div class="shrink-0">
            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
                'key' => 'page-control-user',
                'autoSmall' => 'md',
            ])
        </div>
    </div>

    <div class="grid grid-cols-1 grid-rows-1 relative isolate z-40">

        <div x-show="activeFilter == ''" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 w-full grid grid-cols-1 sm:grid-cols-7 gap-x-3 gap-y-2 items-center">
            <div class="sm:col-span-7 relative">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Nama, Email, atau ID Pengguna...',
                    'searchMode' => $searchMode,
                    'searchValues' => ['simple', 'smart', 'complex'],
                    'searchOptions' => ['Cari Email & Identitas', 'Pencarian Cerdas', 'Pencarian Kompleks'],
                ])
            </div>
        </div>

        <div x-show="activeFilter !== ''" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4" class="col-start-1 row-start-1 w-full">
            <div class=" grid grid-cols-1 sm:grid-cols-7 gap-x-3 gap-y-2 items-center">
                <div class="sm:col-span-4 relative">
                    @include('livewire.global.search-and-filters.main-search', [
                        'placeholder' => 'Cari Nama, Email, atau ID Pengguna...',
                        'searchMode' => $searchMode,
                        'searchValues' => ['simple', 'smart', 'complex'],
                        'searchOptions' => ['Cari Email & Identitas', 'Pencarian Cerdas', 'Pencarian Kompleks'],
                    ])
                </div>

                <div class="order-3 sm:order-2 sm:col-span-3 relative">
                    @include('livewire.global.search-and-filters.secondary-search', [
                        'inputXFilterString' => 'inputPrFilter',
                        'xSearchResultsString' => 'prSearchResults',
                        'iconString' => 'academic-cap',
                        'placeholderString' => 'Filter berdasarkan Program Studi...',
                        'xSearchQueryString' => 'prSearchQuery',
                        'selectedXId' => $selectedPrId,
                        'selectedXName' => $pr_name,
                        'resetXFilter' => 'resetPrFilter()',
                        'xSearchQuery' => $prSearchQuery,
                        'xSearchResults' => $prSearchResults,
                        'selectXForFilterString' => 'selectPrForFilter',
                        'typeXString' => 'prodi',
                        'typeX2String' => 'departemen',
                        'typeX3String' => 'fakultas',
                        'unfoundString' => 'Tidak ada Program Studi ditemukan!',
                    ])
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full">

                <div class="sm:col-span-4 relative">
                    @include('livewire.global.search-and-filters.secondary-search', [
                        'inputXFilterString' => 'inputDpFilter',
                        'xSearchResultsString' => 'dpSearchResults',
                        'iconString' => 'book-open',
                        'placeholderString' => 'Filter berdasarkan Departemen...',
                        'xSearchQueryString' => 'dpSearchQuery',
                        'selectedXId' => $selectedDpId,
                        'selectedXName' => $dp_name,
                        'resetXFilter' => 'resetDpFilter()',
                        'xSearchQuery' => $dpSearchQuery,
                        'xSearchResults' => $dpSearchResults,
                        'selectXForFilterString' => 'selectDpForFilter',
                        'typeXString' => 'departemen',
                        'typeX2String' => 'kode_text',
                        'typeX3String' => 'fakultas',
                        'unfoundString' => 'Tidak ada Departemen ditemukan!',
                    ])
                </div>

                <div class="sm:col-span-4 relative">
                    @include('livewire.global.search-and-filters.secondary-search', [
                        'inputXFilterString' => 'inputFkFilter',
                        'xSearchResultsString' => 'fkSearchResults',
                        'iconString' => 'building-library',
                        'placeholderString' => 'Filter berdasarkan Fakultas...',
                        'xSearchQueryString' => 'fkSearchQuery',
                        'selectedXId' => $selectedFkId,
                        'selectedXName' => $fk_name,
                        'resetXFilter' => 'resetFkFilter()',
                        'xSearchQuery' => $fkSearchQuery,
                        'xSearchResults' => $fkSearchResults,
                        'selectXForFilterString' => 'selectFkForFilter',
                        'typeXString' => 'fakultas',
                        'typeX2String' => 'kode_text',
                        'unfoundString' => 'Tidak ada Fakultas ditemukan!',
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
