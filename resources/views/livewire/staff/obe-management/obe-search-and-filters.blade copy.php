<div x-data="{ activeTab: @entangle('switchTable'), activeFilter: @entangle('filterRPS') }"
    class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">



    <div class="grid grid-cols-1 grid-rows-1 relative isolate z-40">

        {{-- BAGIAN FILTER ATAS --}}
        <div x-show="activeTab == 'rps'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 w-full border-[var(--border-table-color)] flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">
            @include('livewire.global.search-and-filters.filter-mode', [
                'typeXString' => 'RPS',
                'filterByFunc' => 'filterByRPS',
                'filterString' => 'filterRPS',
                'totalTab' => $totalRPS,
                'totalTab1' => $stats['rps-akademik'],
                'totalTab2' => $stats['rps-rev-new'],
                'totalTab3' => $stats['rps-aktif'],
                'totalTab4' => $stats['rps-draf'],
                'totalTab5' => $stats['rps-older-5'],
                'tab1String' => 'rps-akademik',
                'tab2String' => 'rps-rev-new',
                'tab3String' => 'rps-aktif',
                'tab4String' => 'rps-draf',
                'tab5String' => 'rps-older-5',
                'tab1Name' => 'Terbaru',
                'tab2Name' => 'Baru Direvisi',
                'tab3Name' => 'Aktif',
                'tab4Name' => 'Draf',
                'tab5Name' => '>5 Tahun Lalu',
            ])

            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
                'key' => 'page-control-rps',
            ])
        </div>

        <div x-show="activeTab == 'cpmk'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 w-full border-[var(--border-table-color)] flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">
            @include('livewire.global.search-and-filters.filter-mode', [
                'typeXString' => 'CPMK',
                'filterByFunc' => 'filterByCPMK',
                'filterString' => 'filterCPMK',
                'totalTab' => $totalCPMK,
                'totalTab1' => $stats['cpmk-month'],
                'totalTab2' => $stats['cpmk-6-months'],
                'totalTab3' => $stats['cpmk-year'],
                'totalTab4' => $stats['cpmk-older-5'],
                'tab1String' => 'cpmk-month',
                'tab2String' => 'cpmk-6-months',
                'tab3String' => 'cpmk-year',
                'tab4String' => 'cpmk-older-5',
                'tab1Name' => 'Terbaru',
                'tab2Name' => 'Semester Ini',
                'tab3Name' => 'Tahun Ini',
                'tab4Name' => '>5 Tahun Lalu',
            ])

            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200, 300],
                'key' => 'page-control-cpmk',
            ])
        </div>

        <div x-show="activeTab == 'scpmk'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 w-full border-[var(--border-table-color)] flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">
            @include('livewire.global.search-and-filters.filter-mode', [
                'typeXString' => 'SCPMK',
                'filterByFunc' => 'filterBySCPMK',
                'filterString' => 'filterSCPMK',
                'totalTab' => $totalSCPMK,
                'totalTab1' => $stats['scpmk-month'],
                'totalTab2' => $stats['scpmk-6-months'],
                'totalTab3' => $stats['scpmk-year'],
                'totalTab4' => $stats['scpmk-older-5'],
                'tab1String' => 'scpmk-month',
                'tab2String' => 'scpmk-6-months',
                'tab3String' => 'scpmk-year',
                'tab4String' => 'scpmk-older-5',
                'tab1Name' => 'Terbaru',
                'tab2Name' => 'Semester Ini',
                'tab3Name' => 'Tahun Ini',
                'tab4Name' => '>5 Tahun Lalu',
            ])

            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200, 300, 500],
                'key' => 'page-control-scpmk',
            ])
        </div>


        <div x-show="activeTab == 'cpl'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 w-full border-[var(--border-table-color)] flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">
            @include('livewire.global.search-and-filters.filter-mode', [
                'typeXString' => 'CPL',
                'filterByFunc' => 'filterByCPL',
                'filterString' => 'filterCPL',
                'totalTab' => $totalCPL,
                'totalTab1' => $stats['cpl-month'],
                'totalTab2' => $stats['cpl-6-months'],
                'totalTab3' => $stats['cpl-year'],
                'totalTab4' => $stats['cpl-older-5'],
                'tab1String' => 'cpl-month',
                'tab2String' => 'cpl-6-months',
                'tab3String' => 'cpl-year',
                'tab4String' => 'cpl-older-5',
                'tab1Name' => 'Terbaru',
                'tab2Name' => 'Semester Ini',
                'tab3Name' => 'Tahun Ini',
                'tab4Name' => '>5 Tahun Lalu',
            ])

            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100],
                'key' => 'page-control-cpl',
            ])
        </div>

        <div x-show="activeTab == 'ref'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 w-full border-[var(--border-table-color)] flex flex-col-reverse md:flex-row md:justify-between md:items-end border-b mb-4 gap-4">
            @include('livewire.global.search-and-filters.filter-mode', [
                'typeXString' => 'Referensi',
                'filterByFunc' => 'filterByRef',
                'filterString' => 'filterRef',
                'totalTab' => $totalRef,
                'totalTab1' => $stats['ref-year'],
                'totalTab2' => $stats['ref-2-3-years'],
                'totalTab3' => $stats['ref-4-5-years'],
                'totalTab4' => $stats['ref-6-10-years'],
                'totalTab5' => $stats['ref-older-10'],
                'tab1String' => 'ref-year',
                'tab2String' => 'ref-2-3-years',
                'tab3String' => 'ref-4-5-years',
                'tab4String' => 'ref-6-10-years',
                'tab5String' => 'ref-older-10',
                'tab1Name' => 'Terbaru',
                'tab2Name' => '2-3 Tahun Lalu',
                'tab3Name' => '4-5 Tahun Lalu',
                'tab4Name' => '6-10 Tahun Lalu',
                'tab5Name' => '>10 Tahun Lalu',
            ])

            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150],
                'key' => 'page-control-ref',
            ])
        </div>

    </div>



    {{-- BAGIAN SEARCH UTAMA --}}
    <div class="grid grid-cols-1 sm:grid-cols-7 gap-3 z-20">

        {{-- Tab RPS --}}
        <div class="sm:col-span-4 w-full row-start-1 col-start-1">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari RPS, CPMK, Sub-CPMK, CPL, dan Referensi...',
            ])
        </div>


        {{-- 🔹 PRODI --}}
        <div x-show="activeTab !== 'ref'"
            class="sm:col-span-3 relative row-start-1 col-start-2">
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
                'unfoundString' => 'Tidak ada Program Studi ditemukan!',
            ])
        </div>

        {{-- 🔹 MK --}}
        <div x-show="activeTab == 'rps'"
            class="sm:col-span-3 relative grid row-start-2 col-start-1">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputMKFilter',
                'xSearchResultsString' => 'mkSearchResults',
                'iconString' => 'rectangle-stack',
                'placeholderString' => 'Filter berdasarkan Mata Kuliah...',
                'xSearchQueryString' => 'mkSearchQuery',
                'selectedXId' => $selectedMKId,
                'selectedXName' => $mk_name,
                'resetXFilter' => 'resetMKFilter()',
                'xSearchQuery' => $mkSearchQuery,
                'xSearchResults' => $mkSearchResults,
                'selectXForFilterString' => 'selectMKForFilter',
                'typeXString' => 'mk',
                'unfoundString' => 'Tidak ada Mata Kuliah ditemukan!',
            ])
        </div>

        {{-- 🔹 Dosen --}}
        <div x-show="activeTab == 'rps'"
            class="sm:col-span-4 relative row-start-2 col-start-2">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputDosenFilter',
                'xSearchResultsString' => 'dosenSearchResults',
                'iconString' => 'user',
                'placeholderString' => 'Filter berdasarkan Dosen...',
                'xSearchQueryString' => 'dosenSearchQuery',
                'selectedXId' => $selectedDosenId,
                'selectedXName' => $dosen_name,
                'resetXFilter' => 'resetDosenFilter()',
                'xSearchQuery' => $dosenSearchQuery,
                'xSearchResults' => $dosenSearchResults,
                'selectXForFilterString' => 'selectDosenForFilter',
                'typeXString' => 'name',
                'unfoundString' => 'Tidak ada Dosen ditemukan!',
            ])
        </div>

        {{-- 🔹 RPS --}}
        <div x-show="activeTab !== 'rps'"
            x-bind:class="activeTab !== 'ref' ? 'row-start-2 col-start-1' : 'row-start-1 col-start-2'"
            class="sm:col-span-3 relative">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputRPSFilter',
                'xSearchResultsString' => 'rpsSearchResults',
                'iconString' => 'clipboard-document-list',
                'placeholderString' => 'Filter berdasarkan RPS...',
                'xSearchQueryString' => 'rpsSearchQuery',
                'selectedXId' => $selectedRPSId,
                'selectedXName' => $rps_name,
                'resetXFilter' => 'resetRPSFilter()',
                'xSearchQuery' => $rpsSearchQuery,
                'xSearchResults' => $rpsSearchResults,
                'selectXForFilterString' => 'selectRPSForFilter',
                'typeXString' => 'rps',
                'unfoundString' => 'Tidak ada RPS ditemukan!',
            ])
        </div>

        {{-- 🔹 CPL --}}
        <div x-show="activeTab == 'cpmk'"
            x-bind:class="activeTab !== 'ref' ? 'sm:col-span-3 row-start-2 col-start-2' : 'sm:col-span-4 row-start-2 col-start-1'"
            class="sm:col-span-4 relative">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputCPLFilter',
                'xSearchResultsString' => 'cplSearchResults',
                'iconString' => 'document-text',
                'placeholderString' => 'Filter berdasarkan CPL...',
                'xSearchQueryString' => 'cplSearchQuery',
                'selectedXId' => $selectedCPLId,
                'selectedXName' => $cpl_name,
                'resetXFilter' => 'resetCPLFilter()',
                'xSearchQuery' => $cplSearchQuery,
                'xSearchResults' => $cplSearchResults,
                'selectXForFilterString' => 'selectCPLForFilter',
                'typeXString' => 'deskripsi',
                'unfoundString' => 'Tidak ada CPL ditemukan!',
            ])
        </div>

        {{-- 🔹 CPMK --}}
        <div x-show="activeTab == 'scpmk' || activeTab == 'cpl' || activeTab == 'ref'"
           
            x-bind:class="activeTab !== 'ref' ? 'sm:col-span-4' : 'sm:col-span-3'"
            class="relative row-start-2 col-start-2">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputCPMKFilter',
                'xSearchResultsString' => 'cpmkSearchResults',
                'iconString' => 'academic-cap',
                'placeholderString' => 'Filter berdasarkan CPMK...',
                'xSearchQueryString' => 'cpmkSearchQuery',
                'selectedXId' => $selectedCPMKId,
                'selectedXName' => $cpmk_name,
                'resetXFilter' => 'resetCPMKFilter()',
                'xSearchQuery' => $cpmkSearchQuery,
                'xSearchResults' => $cpmkSearchResults,
                'selectXForFilterString' => 'selectCPMKForFilter',
                'typeXString' => 'deskripsi',
                'unfoundString' => 'Tidak ada CPMK ditemukan!',
            ])
        </div>

        {{-- 🔹 Sub-CPMK --}}
        <div x-show="activeTab == 'ref'"
            class="sm:col-span-4 relative row-start-2 col-start-2">
            @include('livewire.global.search-and-filters.secondary-search', [
                'inputXFilterString' => 'inputSCPMKFilter',
                'xSearchResultsString' => 'scpmkSearchResults',
                'iconString' => 'academic-cap',
                'placeholderString' => 'Filter berdasarkan Sub-CPMK...',
                'xSearchQueryString' => 'scpmkSearchQuery',
                'selectedXId' => $selectedSCPMKId,
                'selectedXName' => $scpmk_name,
                'resetXFilter' => 'resetSCPMKFilter()',
                'xSearchQuery' => $scpmkSearchQuery,
                'xSearchResults' => $scpmkSearchResults,
                'selectXForFilterString' => 'selectSCPMKForFilter',
                'typeXString' => 'deskripsi',
                'unfoundString' => 'Tidak ada Sub-CPMK ditemukan!',
            ])
        </div>

    </div>









</div>
