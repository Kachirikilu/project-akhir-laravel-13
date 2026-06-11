<div x-data="{ activeTab: @entangle('switchTable'), activeFilterDosen: @entangle('filterStatus') }"
    class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">




    <div class="grid grid-cols-1 grid-rows-1 relative isolate z-40">

        <div x-show="activeTab == 'rps'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 border-[var(--border-table-color)] flex items-end justify-between border-b mb-4 gap-4">
            <div class="min-w-0 flex-1 overflow-hidden">
                @if (Auth::user()->dosen)
                    @include('livewire.global.search-and-filters.filter-mode', [
                        'filterByFunc' => 'filterByRPS',
                        'filterString' => 'filterRPS',
                        'totalTab' => $totalRPSSaya,
                        'totalTab1' => $stats['rps-prodi'],
                        'totalTab2' => $totalRPS,
                        'totalTab3' => $stats['rps-akademik'],
                        'totalTab4' => $stats['rps-rev-new'],
                        'totalTab5' => $stats['rps-aktif'],
                        'totalTab6' => $stats['rps-draf'],
                        'totalTab7' => $stats['rps-older-5'],
                        'tab1String' => 'rps-prodi',
                        'tab2String' => 'rps-all',
                        'tab3String' => 'rps-akademik',
                        'tab4String' => 'rps-rev-new',
                        'tab5String' => 'rps-aktif',
                        'tab6String' => 'rps-draf',
                        'tab7String' => 'rps-older-5',
                        'tabName' => 'RPS Saya',
                        'tab1Name' => Auth::user()->prodi,
                        'tab2Name' => 'Semua RPS',
                        'tab3Name' => 'Terbaru',
                        'tab4Name' => 'Baru Direvisi',
                        'tab5Name' => 'Aktif',
                        'tab6Name' => 'Draf',
                        'tab7Name' => '>5 Tahun Lalu',
                    ])
                @else
                    @include('livewire.global.search-and-filters.filter-mode', [
                        'filterByFunc' => 'filterByRPS',
                        'filterString' => 'filterRPS',
                        'totalTab' => $stats['rps-prodi'],
                        'totalTab1' => $totalRPS,
                        'totalTab2' => $stats['rps-akademik'],
                        'totalTab3' => $stats['rps-rev-new'],
                        'totalTab4' => $stats['rps-aktif'],
                        'totalTab5' => $stats['rps-draf'],
                        'totalTab6' => $stats['rps-older-5'],
                        'tabHiddenString' => 'rps-prodi',
                        'tab1String' => 'rps-all',
                        'tab2String' => 'rps-akademik',
                        'tab3String' => 'rps-rev-new',
                        'tab4String' => 'rps-aktif',
                        'tab5String' => 'rps-draf',
                        'tab6String' => 'rps-older-5',
                        'tabName' => Auth::user()->prodi,
                        'tab1Name' => 'Semua RPS',
                        'tab2Name' => 'Terbaru',
                        'tab3Name' => 'Baru Direvisi',
                        'tab4Name' => 'Aktif',
                        'tab5Name' => 'Draf',
                        'tab6Name' => '>5 Tahun Lalu',
                    ])
                @endif
            </div>
            <div class="shrink-0">
                @include('livewire.global.search-and-filters.page-control', [
                    'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
                    'key' => 'page-control-rps',
                    'autoSmall' => 'lg',
                ])
            </div>
        </div>

        <div x-show="activeTab == 'cpl'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 border-[var(--border-table-color)] flex items-end justify-between border-b mb-4 gap-4">
            <div class="min-w-0 flex-1 overflow-hidden">
                @include('livewire.global.search-and-filters.filter-mode', [
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
                    'tabName' => 'Semua CPL',
                    'tab1Name' => 'Terbaru',
                    'tab2Name' => 'Semester Ini',
                    'tab3Name' => 'Tahun Ini',
                    'tab4Name' => '>5 Tahun Lalu',
                ])
            </div>
            <div class="shrink-0">
                @include('livewire.global.search-and-filters.page-control', [
                    'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100],
                    'key' => 'page-control-cpl',
                    'autoSmall' => 'lg',
                ])
            </div>
        </div>

        <div x-show="activeTab == 'cpmk'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 border-[var(--border-table-color)] flex items-end justify-between border-b mb-4 gap-4">
            <div class="min-w-0 flex-1 overflow-hidden">
                @include('livewire.global.search-and-filters.filter-mode', [
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
                    'tabName' => 'Semua CPMK',
                    'tab1Name' => 'Terbaru',
                    'tab2Name' => 'Semester Ini',
                    'tab3Name' => 'Tahun Ini',
                    'tab4Name' => '>5 Tahun Lalu',
                ])
            </div>
            <div class="shrink-0">
                @include('livewire.global.search-and-filters.page-control', [
                    'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200, 300],
                    'key' => 'page-control-cpmk',
                    'autoSmall' => 'lg',
                ])
            </div>
        </div>

        <div x-show="activeTab == 'sub-cpmk'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 border-[var(--border-table-color)] flex items-end justify-between border-b mb-4 gap-4">
            <div class="min-w-0 flex-1 overflow-hidden">
                @include('livewire.global.search-and-filters.filter-mode', [
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
                    'tabName' => 'Semua Sub-CPMK',
                    'tab1Name' => 'Terbaru',
                    'tab2Name' => 'Semester Ini',
                    'tab3Name' => 'Tahun Ini',
                    'tab4Name' => '>5 Tahun Lalu',
                ])
            </div>
            <div class="shrink-0">
                @include('livewire.global.search-and-filters.page-control', [
                    'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200, 300, 500],
                    'key' => 'page-control-sub-cpmk',
                    'autoSmall' => 'lg',
                ])
            </div>
        </div>

        <div x-show="activeTab == 'referensi'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 border-[var(--border-table-color)] flex items-end justify-between border-b mb-4 gap-4">
            <div class="min-w-0 flex-1 overflow-hidden">
                @include('livewire.global.search-and-filters.filter-mode', [
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
                    'tabName' => 'Semua Referensi',
                    'tab1Name' => 'Terbaru',
                    'tab2Name' => '2-3 Tahun Lalu',
                    'tab3Name' => '4-5 Tahun Lalu',
                    'tab4Name' => '6-10 Tahun Lalu',
                    'tab5Name' => '>10 Tahun Lalu',
                ])

            </div>
            <div class="shrink-0">
                @include('livewire.global.search-and-filters.page-control', [
                    'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150],
                    'key' => 'page-control-referensi',
                    'autoSmall' => 'lg',
                ])
            </div>
        </div>


        <div x-show="activeTab == 'dosen'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1
                border-[var(--border-table-color)]
                border-b mb-4

                grid
                grid-cols-[1fr_auto]
                lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto]

                gap-x-6 lg:gap-x-8
                items-end">

            {{-- filter-mode 1 --}}
            <div
                class="col-span-2 lg:col-span-1 min-w-0 overflow-hidden border-[var(--border-table-color)] sm:border-b">
                @include('livewire.global.search-and-filters.filter-mode', [
                    'filterByFunc' => 'filterByDosen',
                    'filterString' => 'filterDosen',
                    'totalTab' => $totalDosen,
                    'totalTab1' => $stats['dosen-rps'],
                    'totalTab2' => $stats['dosen-non-rps'],
                    'tab1String' => 'dosen-rps',
                    'tab2String' => 'dosen-non-rps',
                    'tabName' => 'Semua Dosen',
                    'tab1Name' => 'Memiliki RPS',
                    'tab2Name' => 'Tidak Memiliki RPS',
                ])
            </div>

            {{-- filter-mode 2 --}}
            <div class="min-w-0 overflow-hidden">
                @include('livewire.global.search-and-filters.filter-mode', [
                    'filterByFunc' => 'filterByStatus',
                    'filterString' => 'filterStatus',
                    'totalTab' => $stats['dosen-prodi'],
                    'totalTab1' => $stats['dosen-all'],
                    'totalTab2' => $stats['dosen-aktif'],
                    'totalTab3' => $stats['dosen-non-aktif'],
                    'tab1String' => 'dosen-all',
                    'tab2String' => 'dosen-aktif',
                    'tab3String' => 'dosen-non-aktif',
                    'tabName' => Auth::user()->prodi,
                    'tab1Name' => 'Semua Status',
                    'tab2Name' => 'Aktif',
                    'tab3Name' => 'Tidak Aktif',
                ])
            </div>

            {{-- page-control --}}
            <div class="justify-self-end shrink-0">
                @include('livewire.global.search-and-filters.page-control', [
                    'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
                    'key' => 'page-control-dosen',
                    'autoSmall' => 'lg',
                    'withBValue' => 4,
                    'autoTSmall' => 3,
                    'autoBSmall' => 3,
                ])
            </div>

        </div>



    </div>



    {{-- BAGIAN SEARCH UTAMA --}}
    <div class="grid grid-cols-1 sm:grid-cols-7 gap-x-3 gap-y-2 z-20">

        <div x-show="activeTab == 'dosen' && activeFilterDosen == ''" class="sm:col-span-7 w-full">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Nama, atau ID Dosen...',
                'searchMode' => $searchMode,
                'searchValues' => ['simple', 'full'],
                'searchOptions' => ['Cari Identitas Dosen', 'Pencarian Kompleks'],
            ])
        </div>
        <div x-show="activeTab !== 'dosen' || activeFilterDosen !== ''" class="sm:col-span-4 w-full">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari RPS, CPMK, Sub-CPMK, CPL, & Referensi,...',
                'searchMode' => $searchMode,
                'searchValues' => ['simple', 'full'],
                'searchOptions' => ['Cari Kode OBE', 'Pencarian Kompleks'],
            ])
        </div>

        {{-- 🔹 PRODI --}}
        <div x-show="activeTab !== 'referensi' && (activeTab !== 'dosen' || activeFilterDosen !== '')"
            class="sm:col-span-3 relative">
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

        {{-- 🔹 MK --}}
        <div x-show="activeTab == 'rps'"
            class="{{ Auth::user()->admin || $this->filterRPS !== '' ? 'sm:col-span-3' : 'sm:col-span-7' }} relative">
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
                'typeX2String' => 'sks_full',
                'typeX3String' => 'semester_text',
                'typeX4String' => 'wajib_text',
                'unfoundString' => 'Tidak ada Mata Kuliah ditemukan!',
            ])
        </div>

        @if (Auth::user()->admin || $this->filterRPS !== '')
            {{-- 🔹 Dosen --}}
            <div x-show="activeTab == 'rps'" class="sm:col-span-4 relative">
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
                    'typeX2String' => 'nip_full',
                    'typeX3String' => 'status',
                    'typeKodeString' => 'kode_pr',
                    'unfoundString' => 'Tidak ada Dosen ditemukan!',
                ])

            </div>
        @endif


        {{-- 🔹 RPS --}}
        <div x-show="activeTab !== 'rps'" class="sm:col-span-3 relative">
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
                'typeXString' => 'rps_with_kode',
                'typeX2String' => 'sks_full',
                'typeX3String' => 'wajib_text',
                'typeX4String' => 'draf_full',
                'unfoundString' => 'Tidak ada RPS ditemukan!',
            ])
        </div>

        {{-- 🔹 CPL --}}
        <div x-show="activeTab == 'cpmk'" class="sm:col-span-4 relative">
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
                'typeX2String' => 'kode',
                'unfoundString' => 'Tidak ada CPL ditemukan!',
            ])
        </div>

        {{-- 🔹 CPMK --}}
        <div x-show="activeTab == 'sub-cpmk' || activeTab == 'cpl' || activeTab == 'referensi'"
            x-bind:class="activeTab == 'referensi' ? 'sm:col-span-3' : 'sm:col-span-4'" class="relative">
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
                'typeX2String' => 'kode',
                'typeX3String' => 'total_bobot_text',
                'typeX4String' => 'total_pertemuan',
                'unfoundString' => 'Tidak ada CPMK ditemukan!',
            ])
        </div>

        {{-- 🔹 Sub-CPMK --}}
        <div x-show="activeTab == 'referensi'" class="sm:col-span-4 relative">
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
                'typeX2String' => 'kode',
                'typeX3String' => 'metode',
                'typeX4String' => 'bobot_text',
                'unfoundString' => 'Tidak ada Sub-CPMK ditemukan!',
            ])
        </div>

        <div x-show="activeTab == 'dosen'" class="sm:col-span-4 relative">
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
