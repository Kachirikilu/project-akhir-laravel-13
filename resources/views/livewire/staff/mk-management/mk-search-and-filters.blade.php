<div x-data="{ activeFilter: @entangle('filterMK') }"
    class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">

    {{-- BAGIAN FILTER ATAS --}}
    <div x-transition:enter="transition ease-out duration-1000"
        x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
        class="border-[var(--border-table-color)] flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">
        {{-- Bagian Tab / Link (Kiri) --}}
        @include('livewire.global.search-and-filters.filter-mode', [
            'filterByFunc' => 'filterByMK',
            'filterString' => 'filterMK',
            'totalTab' => $totalMKProdi,
            'totalTab1' => $totalMKOpsi,
            'totalTab2' => $totalWajib,
            'totalTab3' => $totalPilihan,
            'totalTab4' => $totalUni,
            'tab1String' => 'mk-all',
            'tab2String' => 'mk-wajib',
            'tab3String' => 'mk-pilihan',
            'tab4String' => 'mk-universitas',
            'tabName' => Auth::user()->prodi,
            'tab1Name' => 'Semua MK',
            'tab2Name' => 'Wajib',
            'tab3Name' => 'Pilihan',
            'tab4Name' => 'Universitas'
        ])

        {{-- Kontrol Jumlah Data Per Halaman (Ditempatkan di kanan) --}}
        @include('livewire.global.search-and-filters.page-control', [
            'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150],
            'key' => 'page-control-mk',
        ])
    </div>

    <div class="grid grid-cols-1 grid-rows-1 relative isolate z-40">

        <div x-show="activeFilter == '' || activeFilter == 'mk-universitas'"
            x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 w-full grid grid-cols-1 sm:grid-cols-7 gap-3 items-center">
            <div class="sm:col-span-7 relative">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Mata Kuliah...',
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
            <div class=" grid grid-cols-1 sm:grid-cols-7 gap-3 items-center">
                <div class="sm:col-span-4 relative">
                    @include('livewire.global.search-and-filters.main-search', [
                        'placeholder' => 'Cari Mata Kuliah...',
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
