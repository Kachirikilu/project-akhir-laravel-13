<div x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'nilai-management' || segment === '') ? '' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    @include('livewire.global.header.tag-user')

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 w-full min-w-0">

        <h2 class="text-xl sm:text-2xl font-bold text-[var(--contrast-second-text)] min-w-0 break-words">
            Manajemen Nilai Mahasiswa
        </h2>

        <div
            class="flex flex-row-reverse items-center justify-start gap-3 w-full md:w-auto overflow-x-auto scrollbar-tiny flex-nowrap shrink-0 pb-1">

            <div x-show="activeTable == 'mahasiswa'" class="shrink-0 flex items-center">
                @include('livewire.global.table.export-button', [
                    'xString' => 'exportRekapMahasiswaExcel()',
                    'autoSmall' => 'sm',
                ])
            </div>

            <div x-show="activeTable == 'rps'" class="shrink-0">
                @include('livewire.global.table.export-button', [
                    'xString' => 'exportOBEExcel()',
                    'autoSmall' => 'lg',
                ])
            </div>

            @if (Auth::user()->admin || Auth::user()->dosen)
                <div x-data="{ activeTab: @entangle('filterStatus') }" class="shrink-0">
                    <div x-show="activeTab !== ''" class="shrink-0">
                        @include('livewire.global.table.export-button', [
                            'nameXString' => 'Rekap Capaian',
                            'xString' => 'generateRekapCapaian()',
                            'color' => 'blue',
                            'icon' => 'academic-cap',
                        ])
                    </div>
                    <div x-show="activeTab == ''" class="shrink-0">
                        @include('livewire.global.table.export-button', [
                            'nameXString' => 'Rekap Capaian ' . Auth::user()->kode_pr,
                            'xString' => 'generateRekapCapaian(' . Auth::user()->pr_id . ', 15)',
                            'color' => 'blue',
                            'icon' => 'academic-cap',
                        ])
                    </div>
                </div>
            @endif

        </div>
    </div>

    <div class="flex items-center w-full mt-2 mb-2" x-data="{ activeTab: @entangle('switchTable') }">
        <div class="w-full">

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 w-full">

                <div class="scrollbar-tiny -mb-px flex items-center space-x-3 overflow-x-auto w-full pb-1">
                    @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                        'xString' => 'switchingTable',
                        'xFilter' => $switchTable,
                        'tabFilter' => null,
                        'tabString' => 'mahasiswa',
                        // 'tabNameString' => 's',
                        'icon' => 'users',
                    ])

                    @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                        'xString' => 'switchingTable',
                        'xFilter' => $switchTable,
                        'tabFilter' => null,
                        'tabString' => 'rps',
                        'tabNameString' => 'Rencana Pembelajaran Semester',
                        'icon' => 'clipboard-document-list',
                    ])
                </div>

            </div>
        </div>
    </div>

    @include('livewire.staff.nilai-management.nilai-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @if ($switchTable == 'mahasiswa')
            @include('livewire.staff.nilai-management.mahasiswa-nilai-table')
        @elseif ($switchTable == 'rps')
            @include('livewire.staff.nilai-management.rps-nilai-table')
            {{-- @include('livewire.staff.obe-management.rps-management.rps-table') --}}
        @endif
    </div>

    {{-- @include('livewire.admin.user-management.user-table', [
        'withRPS' => 1,
        'withNilai' => 1,
        'withCapaian' => 1,
        'withProdi' => 1,
    ]) --}}


    {{-- <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.admin.user-management.user-table')
    </div> --}}

    {{-- @include('livewire.admin.user-management.user-rps-list', ['noModalRPS' => 1]) --}}
    {{-- @include('livewire.staff.obe-management.rps-management.rps-show-modal') --}}

    {{-- @if (Auth::user()->admin)
        @include('livewire.admin.user-management.user-modal-form')
        @include('livewire.admin.user-management.user-modal-delete')
    @endif --}}
</div>
