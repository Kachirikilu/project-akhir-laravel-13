<div x-data="{ activeTab: @entangle('switchTable'), activeFilterDosen: @entangle('filterStatus') }"
    class="bg-[var(--main-table-color)] table-border text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">

    <div class="grid grid-cols-1 grid-rows-1 relative isolate z-40">

        @include('livewire.staff.obe-management.obe-partial.obe-filters')

        <div x-show="activeTab == 'referensi'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 table-border flex items-end justify-between border-b mb-4 gap-4">
            <div class="min-w-0 flex-1 overflow-hidden">
                @include('livewire.global.search-and-filters.filter-mode', [
                    'filterByFunc' => 'filterByRef',
                    'filterString' => 'filterRef',
                    'totalTab' => $stats['ref'],
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

        {{-- //     'tim-dosen-saya' => '👥',
                // 'tim-dosen-prodi' => '🏛️',
                // 'tim-dosen-all' => '👥',
                // 'tim-dosen-rps' => '✅',
                // 'tim-dosen-non-rps' => '❌', --}}


        <div x-show="activeTab == 'tim-dosen'" x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-100 -translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-100 -translate-y-4"
            class="col-start-1 row-start-1 table-border flex items-end justify-between border-b mb-4 gap-4">
            <div class="min-w-0 flex-1 overflow-hidden">
                @if (Auth::user()->dosen)
                    @include('livewire.global.search-and-filters.filter-mode', [
                        'filterByFunc' => 'filterByTimDosen',
                        'filterString' => 'filterTimDosen',
                        'totalTab' => $stats['tim-dosen-saya'],
                        'totalTab1' => $stats['tim-dosen-prodi'],
                        'totalTab2' => $stats['tim-dosen-all'],
                        'totalTab3' => $stats['tim-dosen-rps'],
                        'totalTab4' => $stats['tim-dosen-non-rps'],
                        'tab1String' => 'tim-dosen-prodi',
                        'tab2String' => 'tim-dosen-all',
                        'tab3String' => 'tim-dosen-rps',
                        'tab4String' => 'tim-dosen-non-rps',
                        'tabName' => 'Tim Saya',
                        'tab1Name' => Auth::user()->prodi,
                        'tab2Name' => 'Semua Tim Dosen',
                        'tab3Name' => 'Memiliki RPS',
                        'tab4Name' => 'Tidak Memiliki RPS',
                    ])
                @else
                    @include('livewire.global.search-and-filters.filter-mode', [
                        'filterByFunc' => 'filterByTimDosen',
                        'filterString' => 'filterTimDosen',
                        'totalTab' => $stats['tim-dosen-prodi'],
                        'totalTab1' => $stats['tim-dosen-all'],
                        'totalTab2' => $stats['tim-dosen-rps'],
                        'totalTab3' => $stats['tim-dosen-non-rps'],
                        'tab1String' => 'tim-dosen-all',
                        'tab2String' => 'tim-dosen-rps',
                        'tab3String' => 'tim-dosen-non-rps',
                        'tabName' => Auth::user()->prodi,
                        'tab1Name' => 'Semua Tim Dosen',
                        'tab2Name' => 'Memiliki RPS',
                        'tab3Name' => 'Tidak Memiliki RPS',
                    ])
                @endif
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
                table-border
                border-b mb-4

                grid
                grid-cols-[1fr_auto]
                lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto]

                gap-x-6 lg:gap-x-8
                items-end">

            {{-- filter-mode 1 --}}
            <div class="col-span-2 lg:col-span-1 min-w-0 overflow-hidden table-border sm:border-b">
                @include('livewire.global.search-and-filters.filter-mode', [
                    'filterByFunc' => 'filterByDosen',
                    'filterString' => 'filterDosen',
                    'totalTab' => $stats['dosen'],
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
            <div class="min-w-0 overflow-hidden sm:mt-2 md:mt-0">
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
        <div x-show="activeTab == 'tim-dosen'" class="sm:col-span-4 w-full">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Tim Dosen, atau Ketua Dosen...',
                'searchMode' => $searchMode,
                'searchValues' => ['simple', 'full'],
                'searchOptions' => ['Cari Tim Dosen', 'Pencarian Kompleks'],
            ])
        </div>
        <div x-show="(activeTab == 'dosen' && activeFilterDosen == '')" class="sm:col-span-7 w-full">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Nama Dosen, atau ID Dosen...',
                'searchMode' => $searchMode,
                'searchValues' => ['simple', 'full'],
                'searchOptions' => ['Cari Identitas Dosen', 'Pencarian Kompleks'],
            ])
        </div>
        <div x-show="(activeTab !== 'dosen' || activeFilterDosen !== '') && activeTab !== 'tim-dosen'"
            class="sm:col-span-4 w-full">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari RPS, CPMK, Sub-CPMK, CPL, & Referensi...',
                'searchMode' => $searchMode,
                'searchValues' => ['simple', 'full'],
                'searchOptions' => ['Cari Kode OBE', 'Pencarian Kompleks'],
            ])
        </div>

        {{-- 🔹 PRODI --}}
        <div x-show="activeTab !== 'referensi' && (activeTab !== 'dosen' || activeFilterDosen !== '')"
            class="sm:col-span-3 relative">
            <livewire:global.search-filters.prodi-search-filter lazy wire:key="prodi-search-filter" />
        </div>

        {{-- 🔹 MK --}}
        <div x-show="activeTab == 'rps' || activeTab == 'tim-dosen'"
            x-bind:class="activeTab === 'tim-dosen' ? 'sm:col-span-3' :
                '{{ Auth::user()->admin || $this->filterRPS !== '' ? 'sm:col-span-3' : 'sm:col-span-7 ' }}'"
            class="relative" {{-- class="{{ Auth::user()->admin || $this->filterRPS !== '' ? 'sm:col-span-3' : ($switchTable == 'tim-dosen' ? 'sm:col-span-3' : 'sm:col-span-7') }} relative" --}}>
            <livewire:global.search-filters.mk-search-filter lazy wire:key="mk-search-filter" />
        </div>

        @if (Auth::user()->admin || $this->filterRPS !== '')
            {{-- 🔹 Dosen --}}
            <div x-show="activeTab == 'rps'" class="sm:col-span-4 relative">
                <livewire:global.search-filters.dosen-search-filter lazy wire:key="dosen-search-filter" />
            </div>
            {{-- <div x-show="activeTab == 'rps'" class="sm:col-span-4 relative">
                <livewire:global.search-filters.dosen-search-filter lazy />
            </div> --}}
        @endif


        {{-- 🔹 RPS --}}
        <div x-show="activeTab !== 'rps'" x-bind:class="activeTab === 'tim-dosen' ? 'sm:col-span-4' : 'sm:col-span-3'"
            class="relative">
            <livewire:global.search-filters.rps-search-filter lazy wire:key="rps-search-filter" />
        </div>

        {{-- 🔹 CPL --}}
        <div x-show="activeTab == 'cpmk'" class="sm:col-span-4 relative">
            <livewire:global.search-filters.cpl-search-filter lazy wire:key="cpl-search-filter" />
        </div>

        {{-- 🔹 CPMK --}}
        <div x-show="activeTab == 'sub-cpmk' || activeTab == 'cpl' || activeTab == 'referensi'"
            x-bind:class="activeTab == 'referensi' ? 'sm:col-span-3' : 'sm:col-span-4'" class="relative">
            <livewire:global.search-filters.cpmk-search-filter lazy wire:key="cpmk-search-filter" />
        </div>

        {{-- 🔹 Sub-CPMK --}}
        <div x-show="activeTab == 'referensi'" class="sm:col-span-4 relative">
            <livewire:global.search-filters.sub-cpmk-search-filter lazy wire:key="spmk-search-filter" />
        </div>

        <div x-show="activeTab == 'dosen'" class="sm:col-span-4 relative">
            <livewire:global.search-filters.fakultas-search-filter lazy wire:key="ref-search-filter" />
        </div>

    </div>
</div>
