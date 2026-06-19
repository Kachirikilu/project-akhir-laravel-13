<div x-data="{ activeFilter: @entangle('filterStatus') }"
    class="bg-[var(--main-table-color)] table-border text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">

{{-- Container Utama: Menggunakan flex-col-reverse agar di mobile bagian tombol pindah ke ATAS filter --}}
    <div class="table-border flex flex-col-reverse lg:flex-row lg:items-end lg:justify-between border-b mb-4 gap-4 w-full min-w-0">
        
        {{-- Bagian Kiri: Komponen Filter Mode --}}
        <div class="min-w-0 flex-1 overflow-hidden w-full">
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
                'tabName' => Auth::user()->prodi,
                'tab1Name' => 'Semua Status',
                'tab2Name' => 'Aktif',
                'tab3Name' => 'Tidak Aktif',
            ])
        </div>
        
        {{-- Bagian Kanan: Wadah Seluruh Tombol (Export & Page Control) --}}
        <div class="flex flex-row items-center justify-end gap-3 w-full lg:w-auto shrink-0 pb-2 lg:pb-0">
            
            @if ($withCapaian ?? null)
                @if (Auth::user()->admin || Auth::user()->dosen)
                    <div x-data="{ activeTab: @entangle('filterStatus') }" class="shrink-0">
                        <div x-show="activeTab !== ''">
                            @include('livewire.global.table.export-button', [
                                'nameXString' => 'Rekap Capaian',
                                'xString' => 'generateRekapCapaian()',
                                'color' => 'blue',
                                'icon' => 'academic-cap',
                            ])
                        </div>
                        <div x-show="activeTab == ''">
                            @include('livewire.global.table.export-button', [
                                'nameXString' => 'Rekap Capaian ' . Auth::user()->kode_pr,
                                'xString' => 'generateRekapCapaian(' . Auth::user()->pr_id . ', 15)',
                                'color' => 'blue',
                                'icon' => 'academic-cap',
                            ])
                        </div>
                    </div>
                @endif

                <div class="shrink-0 flex items-center">
                    @include('livewire.global.table.export-button', [
                        'xString' => 'exportRekapMahasiswaExcel()',
                    ])
                </div>
            @endif

            <div class="shrink-0 flex items-center">
                @include('livewire.global.search-and-filters.page-control', [
                    'perPageOptions' => [3, 5, 8, 10, 15, 25, 50, 75, 100, 150, 200],
                    'key' => 'page-control-user',
                    'autoSmall' => 'md',
                ])
            </div>
            
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
                    'searchValues' => ['simple', 'full'],
                    'searchOptions' => ['Cari Email & Identitas', 'Pencarian Kompleks'],
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
                        'searchValues' => ['simple', 'full'],
                        'searchOptions' => ['Cari Email & Identitas', 'Pencarian Kompleks'],
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
