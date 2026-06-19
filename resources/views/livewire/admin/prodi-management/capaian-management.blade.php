<div x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'capaian-management' || segment === '') ? '' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    <div class="mb-6">
    @include('livewire.admin.prodi-management.capaian-management.capaian-toolbar', [
        'typeXString' => 'all',
        'withCapaian' => 1,
        'textString' => "Manajemen Capaian $prodi->prodi",
        'textString2' => "$prodi->prodi ($prodi->kode)",
        'textString3' => "$prodi->fakultas_fk ($prodi->kode_fk)",
        'backUrl' => route('program-studi-management'),
    ])
    </div>
    @include('livewire.admin.prodi-management.capaian-management.capian-switch-table')
    @include('livewire.admin.prodi-management.capaian-management.capaian-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @switch($switchTable)
            @case('rps')
                @include('livewire.staff.obe-management.rps-management.rps-table', ['withCapaian' => 1])
            @break

            @case('cpl')
                @include('livewire.staff.obe-management.cpl-management.cpl-table', ['withCapaian' => 1])
                @include('livewire.staff.obe-management.cpl-management.cpl-rps-list', ['withCapaian' => 1])
            @break

            @case('cpmk')
                @include('livewire.staff.obe-management.cpmk-management.cpmk-table', ['withCapaian' => 1])
            @break

            @case('sub-cpmk')
                @include('livewire.staff.obe-management.scpmk-management.scpmk-table', [
                    'withCapaian' => 1,
                ])
            @break

            @case('mahasiswa')
                @include('livewire.admin.user-management.user-table', [
                    'withRPS' => true,
                    'withCapaian' => 1,
                ])
                @include('livewire.admin.user-management.user-rps-list')
            @break
        @endswitch
    </div>

    @include('livewire.staff.obe-management.rps-management.rps-show-modal')
    @include('livewire.staff.obe-management.rps-management.rps-modal-form')

    {{-- @include('livewire.staff.obe-management.cpl-management.cpl-modal-form', ['noModalRPS' => 1])
    @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form', ['noModalRPS' => 1])
    @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form', ['noModalRPS' => 1])
    @include('livewire.staff.obe-management.ref-management.ref-modal-form', ['noModalRPS' => 1]) --}}

    @include('livewire.staff.obe-management.cpl-management.cpl-modal-form')
    @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form')
    @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form')
    @include('livewire.staff.obe-management.ref-management.ref-modal-form')
    @include('livewire.admin.user-management.user-modal-form', ['withRPS' => 1])

    @include('livewire.staff.obe-management.rps-management.rps-modal-delete')
    @include('livewire.staff.obe-management.cpl-management.cpl-modal-delete')
    @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-delete')
    @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-delete')
    @include('livewire.admin.user-management.user-modal-delete')

</div>
