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

    <div class="flex flex-wrap items-center gap-2 mb-4">
        <h2 class="text-2xl mr-4 font-bold mb-4 text-[var(--contrast-second-text)]">Manajemen Nilai Mahasiswa</h2>
        {{-- <div class="ml-auto">
            @include('livewire.global.table.export-button', [
                'xString' => 'exportRekapMahasiswaExcel()',
                // 'autoSmall' => 'sm',
            ])
        </div> --}}
    </div>


    @include('livewire.admin.user-management.user-search-and-filters', ['role' => 'mahasiswa', 'withCapaian' => 1])

    @include('livewire.admin.user-management.user-table', [
        'withRPS' => 1,
        'withNilai' => 1,
        'withCapaian' => 1,
        'withProdi' => 1,
    ])


    {{-- <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.admin.user-management.user-table')
    </div> --}}

    @include('livewire.admin.user-management.user-rps-list', ['noModalRPS' => 1])
    @include('livewire.staff.obe-management.rps-management.rps-show-modal')
    
    @if (Auth::user()->admin)
        @include('livewire.admin.user-management.user-modal-form')
        @include('livewire.admin.user-management.user-modal-delete')
    @endif
</div>
