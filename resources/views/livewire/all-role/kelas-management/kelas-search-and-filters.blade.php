<div x-data="{ activeFilter: @entangle('filterKelas') }"
    class="bg-[var(--main-table-color)]/70 border-[var(--border-table-color)]/20 table-border text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border">


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
                    'totalTab' => $stats['kelas-saya'] ?? null,
                    'totalTab1' => $stats['kelas-prodi'] ?? null,
                    'totalTab2' => $stats['kelas'] ?? null,
                    'totalTab3' => $stats['kelas-wajib'] ?? null,
                    'totalTab4' => $stats['kelas-pilihan'] ?? null,
                    'totalTab5' => $stats['kelas-pr'] ?? null,
                    'totalTab6' => $stats['kelas-dp'] ?? null,
                    'totalTab7' => $stats['kelas-fk'] ?? null,
                    'totalTab8' => $stats['kelas-uni'] ?? null,
                    'tab1String' => 'kelas-prodi',
                    'tab2String' => 'kelas-all',
                    'tab3String' => 'kelas-wajib',
                    'tab4String' => 'kelas-pilihan',
                    'tab5String' => 'kelas-pr',
                    'tab6String' => 'kelas-dp',
                    'tab7String' => 'kelas-fk',
                    'tab8String' => 'kelas-uni',
                    'tabName' => 'Kelas Saya',
                    'tab1Name' => Auth::user()->prodi,
                    'tab2Name' => 'Semua Kelas',
                    'tab3Name' => 'Wajib',
                    'tab4Name' => 'Pilihan',
                    'tab5Name' => 'Program Studi',
                    'tab6Name' => 'Departemen',
                    'tab7Name' => 'Fakultas',
                    'tab8Name' => 'Universitas',
                ])
            @else
                @include('livewire.global.search-and-filters.filter-mode', [
                    'filterByFunc' => 'filterByKelas',
                    'filterString' => 'filterKelas',
                    'totalTab' => $stats['kelas-prodi'] ?? null,
                    'totalTab1' => $stats['kelas'] ?? null,
                    'totalTab2' => $stats['kelas-wajib'] ?? null,
                    'totalTab3' => $stats['kelas-pilihan'] ?? null,
                    'totalTab4' => $stats['kelas-pr'] ?? null,
                    'totalTab5' => $stats['kelas-dp'] ?? null,
                    'totalTab6' => $stats['kelas-fk'] ?? null,
                    'totalTab7' => $stats['kelas-uni'] ?? null,
                    'tabHiddenString' => 'kelas-prodi',
                    'tab1String' => 'kelas-all',
                    'tab2String' => 'kelas-wajib',
                    'tab3String' => 'kelas-pilihan',
                    'tab4String' => 'kelas-pr',
                    'tab5String' => 'kelas-dp',
                    'tab6String' => 'kelas-fk',
                    'tab7String' => 'kelas-uni',
                    'tabName' => Auth::user()->prodi ?? 'Program Studi Saya',
                    'tab1Name' => 'Semua Kelas',
                    'tab2Name' => 'Wajib',
                    'tab3Name' => 'Pilihan',
                    'tab4Name' => 'Program Studi',
                    'tab5Name' => 'Departemen',
                    'tab6Name' => 'Fakultas',
                    'tab7Name' => 'Universitas',
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
    {{-- <div x-show="activeFilter !== 'kelas-uni'" x-transition:enter="transition ease-out duration-600"
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
                'searchValues' => ['simple', 'smart', 'complex'],
                'searchOptions' => ['Cari Kode Kelas', 'Pencarian Cerdas', 'Pencarian Kompleks'],
            ])
        </div>

        <div class="sm:col-span-4 relative">
            <livewire:global.search-filters.dosen-search-filter lazy wire:key="dosen-search-filter" />
        </div>

        <div x-show="activeFilter !== '' && activeFilter !== 'kelas-prodi' && activeFilter !== 'kelas-uni'"
            class="sm:col-span-6 lg:col-span-3 relative">
            <livewire:global.search-filters.prodi-search-filter lazy wire:key="pr-search-filter" />
        </div>

        <div class="relative text-left"
            :class="activeFilter !== '' && activeFilter !== 'kelas-prodi' && activeFilter !== 'kelas-uni' ?
                'sm:col-span-6 lg:col-span-4' : 'sm:col-span-5'">
            <livewire:global.search-filters.mk-search-filter lazy wire:key="mk-search-filter" />
        </div>

        <div class="relative text-left"
            :class="activeFilter !== '' && activeFilter !== 'kelas-prodi' && activeFilter !== 'kelas-uni' ?
                'sm:col-span-12 lg:col-span-5' : 'sm:col-span-7'">
            <livewire:global.search-filters.rps-search-filter lazy wire:key="rps-search-filter" />
        </div>
    </div>

    {{-- <div x-show="activeFilter == '' || activeFilter == 'kelas-prodi' || activeFilter == 'kelas-uni'"
        x-transition:enter="transition ease-out duration-1000" x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
        class="grid grid-cols-1 sm:grid-cols-7 gap-x-3 gap-y-2 items-center w-full">

        <div class="sm:col-span-4 relative">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Kelas...',
                'searchMode' => $searchMode,
                'searchValues' => ['simple', 'smart', 'complex'],
                'searchOptions' => ['Cari Kode Kelas', 'Pencarian Cerdas', 'Pencarian Kompleks'],
            ])
        </div>

    </div>


    <div class="grid grid-cols-1 sm:grid-cols-8 mt-2 gap-2 items-center w-full">

    </div> --}}
</div>
