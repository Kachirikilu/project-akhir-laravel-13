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
    {{-- @include('livewire.staff.mk-management.mk-toolbar')
    @include('livewire.staff.mk-management.mk-switch-table')

    @include('livewire.staff.mk-management.mk-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.staff.mk-management.mk-table')
    </div>

    @include('livewire.staff.mk-management.mk-modal-form')
    @include('livewire.staff.mk-management.mk-modal-delete') --}}

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-2 md:mb-6 w-full min-w-0">

        <h2 class="text-xl sm:text-2xl font-bold text-[var(--contrast-second-text)] min-w-0 break-words">
            Manajemen Nilai Mahasiswa
        </h2>

        <div
            class="flex flex-row-reverse items-center justify-start gap-3 w-full md:w-auto overflow-x-auto scrollbar-tiny flex-nowrap shrink-0 pb-1">

            <div class="shrink-0 flex items-center">
                @include('livewire.global.table.export-button', [
                    'xString' => 'exportRekapMahasiswaExcel()',
                    'autoSmall' => 'sm',
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



    @include('livewire.admin.user-management.user-search-and-filters', ['role' => 'mahasiswa'])

    @include('livewire.staff.nilai-management.mahasiswa-nilai-table')

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
