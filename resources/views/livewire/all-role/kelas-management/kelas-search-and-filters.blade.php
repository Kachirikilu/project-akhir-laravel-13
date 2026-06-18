<div x-data="{ activeFilter: @entangle('filterKelas') }"
    class="bg-[var(--main-table-color)] table-border text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">


    <div x-transition:enter="transition ease-out duration-1000"
        x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
        class="table-border flex items-end justify-between border-b mb-4 gap-4">
        <div class="min-w-0 flex-1 overflow-hidden">
            @if (Auth::user()->dosen || Auth::user()->mahasiswa)
                @include('livewire.global.search-and-filters.filter-mode', [
                    'filterByFunc' => 'filterByKelas',
                    'filterString' => 'filterKelas',
                    'totalTab' => $stats['kelas-saya'],
                    'totalTab1' => $stats['kelas-prodi'],
                    'totalTab2' => $stats['kelas'],
                    'totalTab3' => $stats['kelas-wajib'],
                    'totalTab4' => $stats['kelas-pilihan'],
                    'totalTab5' => $stats['kelas-uni'],
                    'tab1String' => 'kelas-prodi',
                    'tab2String' => 'kelas-all',
                    'tab3String' => 'kelas-wajib',
                    'tab4String' => 'kelas-pilihan',
                    'tab5String' => 'kelas-universitas',
                    'tabName' => 'Kelas Saya',
                    'tab1Name' => Auth::user()->prodi,
                    'tab2Name' => 'Semua Kelas',
                    'tab3Name' => 'Wajib',
                    'tab4Name' => 'Pilihan',
                    'tab5Name' => 'Universitas',
                ])
            @else
                @include('livewire.global.search-and-filters.filter-mode', [
                    'filterByFunc' => 'filterByKelas',
                    'filterString' => 'filterKelas',
                    'totalTab' => $stats['kelas-prodi'],
                    'totalTab1' => $stats['kelas'],
                    'totalTab2' => $stats['kelas-wajib'],
                    'totalTab3' => $stats['kelas-pilihan'],
                    'totalTab4' => $stats['kelas-uni'],
                    'tabHiddenString' => 'kelas-prodi',
                    'tab1String' => 'kelas-all',
                    'tab2String' => 'kelas-wajib',
                    'tab3String' => 'kelas-pilihan',
                    'tab4String' => 'kelas-universitas',
                    'tabName' => Auth::user()->prodi,
                    'tab1Name' => 'Semua Kelas',
                    'tab2Name' => 'Wajib',
                    'tab3Name' => 'Pilihan',
                    'tab4Name' => 'Universitas',
                ])
            @endif
        </div>
        <div class="shrink-0">
            @include('livewire.global.search-and-filters.page-control', [
                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150],
                'key' => 'page-control-kelas',
                'autoSmall' => 'lg',
            ])
        </div>
    </div>


    {{-- BAGIAN SECONDARY SEARCH --}}
    {{-- <div x-show="activeFilter !== 'kelas-universitas'" x-transition:enter="transition ease-out duration-600"
        x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
        class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full"> --}}
{{-- order-3 sm:order-2  --}}
    {{-- BAGIAN SEARCH UTAMA --}}
<div class="grid grid-cols-1 sm:grid-cols-12 gap-x-3 gap-y-2 items-center w-full">

    <div class="sm:col-span-8 relative">
        @include('livewire.global.search-and-filters.main-search', [
            'placeholder' => 'Cari Kelas...',
            'searchMode' => $searchMode,
            'searchValues' => ['simple', 'full'],
            'searchOptions' => ['Cari Kode Kelas', 'Pencarian Kompleks'],
        ])
    </div>

    <div class="sm:col-span-4 relative">
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

    <div x-show="activeFilter !== '' && activeFilter !== 'kelas-prodi' && activeFilter !== 'kelas-universitas'" 
         class="sm:col-span-6 lg:col-span-3 relative">
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

    <div class="relative text-left"
         :class="activeFilter !== '' && activeFilter !== 'kelas-prodi' && activeFilter !== 'kelas-universitas' ? 'sm:col-span-6 lg:col-span-4' : 'sm:col-span-5'">
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

    <div class="relative text-left"
         :class="activeFilter !== '' && activeFilter !== 'kelas-prodi' && activeFilter !== 'kelas-universitas' ? 'sm:col-span-12 lg:col-span-5' : 'sm:col-span-7'">
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
</div>

    {{-- <div x-show="activeFilter == '' || activeFilter == 'kelas-prodi' || activeFilter == 'kelas-universitas'"
        x-transition:enter="transition ease-out duration-1000" x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
        class="grid grid-cols-1 sm:grid-cols-7 gap-x-3 gap-y-2 items-center w-full">

        <div class="sm:col-span-4 relative">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Kelas...',
                'searchMode' => $searchMode,
                'searchValues' => ['simple', 'full'],
                'searchOptions' => ['Cari Kode Kelas', 'Pencarian Kompleks'],
            ])
        </div>

    </div>


    <div class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full">

    </div> --}}
</div>
