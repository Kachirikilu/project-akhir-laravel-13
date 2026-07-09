<div x-data="{ activeTab: @entangle('switchTable') }"
    class="bg-[var(--main-table-color)] table-border text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">

    <div class="grid grid-cols-1 grid-rows-1 relative isolate z-40">
        @include('livewire.staff.obe-management.obe-partial.obe-filters')

        <div x-show="activeTab == 'mahasiswa'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 table-border flex items-end justify-between border-b mb-4 gap-4">
            <div class="min-w-0 flex-1 overflow-hidden">
                @include('livewire.global.search-and-filters.filter-mode', [
                    'filterByFunc' => 'filterByStatus',
                    'filterString' => 'filterStatus',
                    'totalTab' => $stats['mahasiswa'],
                    'totalTab1' => $stats['mahasiswa-aktif'],
                    'totalTab2' => $stats['mahasiswa-non-aktif'],
                    'tab1String' => 'mahasiswa-aktif',
                    'tab2String' => 'mahasiswa-non-aktif',
                    'tabName' => 'Semua Status',
                    'tab1Name' => 'Aktif',
                    'tab2Name' => 'Tidak Aktif',
                ])
            </div>
            <div class="shrink-0">
                @include('livewire.global.search-and-filters.page-control', [
                    'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
                    'key' => 'page-control-user',
                    'autoSmall' => 'md',
                ])
            </div>
        </div>
    </div>






    {{-- BAGIAN SEARCH UTAMA --}}
    <div class="grid grid-cols-1 sm:grid-cols-7 gap-x-3 gap-y-2 z-20">

        <div x-show="activeTab == 'mahasiswa'" class="sm:col-span-7 w-full">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Nama, atau ID Mahasiswa...',
                'searchMode' => $searchMode,
                'searchValues' => ['simple', 'smart', 'complex'],
                'searchOptions' => ['Cari Kode OBE', 'Pencarian Cerdas', 'Pencarian Kompleks'],
            ])
        </div>
        <div x-show="activeTab !== 'mahasiswa'" class="sm:col-span-4 w-full">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari RPS, CPMK, Sub-CPMK, CPL,...',
                'searchMode' => $searchMode,
                'searchValues' => ['simple', 'smart', 'complex'],
                'searchOptions' => ['Cari Kode OBE', 'Pencarian Cerdas', 'Pencarian Kompleks'],
            ])
        </div>

        {{-- 🔹 RPS --}}
        <div x-show="activeTab !== 'rps' && activeTab !== 'mahasiswa'" class="sm:col-span-3 relative">
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
            class="sm:col-span-7 relative">
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

        <div x-show="activeTab == 'rps' || activeTab == 'cpmk'"
             x-bind:class="activeTab == 'rps' ? 'sm:col-span-3' : 'sm:col-span-7'" class="relative">
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


    </div>
</div>
