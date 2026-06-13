<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">

    <div class="grid grid-cols-1 grid-rows-1 relative isolate z-40">

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
                    'totalTab' => $stats['cpl'],
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

    </div>



    {{-- BAGIAN SEARCH UTAMA --}}
    <div class="grid grid-cols-1 sm:grid-cols-7 gap-x-3 gap-y-2 z-20">

        <div x-show="activeTab !== 'dosen'" class="sm:col-span-4 w-full">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari RPS, CPMK, Sub-CPMK, CPL, & Referensi,...',
                'searchMode' => $searchMode,
                'searchValues' => ['simple', 'full'],
                'searchOptions' => ['Cari Kode OBE', 'Pencarian Kompleks'],
            ])
        </div>

        {{-- 🔹 PRODI --}}
        <div x-show="activeTab !== 'referensi' && (activeTab !== 'dosen')"
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

        <div x-show="activeTab == 'sub-cpmk' || activeTab == 'cpl'"
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

    </div>
</div>
