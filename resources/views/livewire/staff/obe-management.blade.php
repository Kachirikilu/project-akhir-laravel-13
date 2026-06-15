<div x-data="{ activeTable: '{{ $switchTable ?? 'rps' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'obe-management' || segment === '') ? 'rps' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    @include('livewire.staff.obe-management.obe-toolbar', ['typeXString' => 'all'])
    @include('livewire.staff.obe-management.obe-switch-table')
    @include('livewire.staff.obe-management.obe-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @switch($switchTable)
            @case('rps')
                @include('livewire.staff.obe-management.rps-management.rps-table')

            @break

            @case('cpl')
                @include('livewire.staff.obe-management.cpl-management.cpl-table')
    @include('livewire.staff.obe-management.cpl-management.cpl-rps-list')
            @break

            @case('cpmk')
                @include('livewire.staff.obe-management.cpmk-management.cpmk-table')
            @break

            @case('sub-cpmk')
                @include('livewire.staff.obe-management.scpmk-management.scpmk-table')
            @break

            @case('referensi')
                @include('livewire.staff.obe-management.ref-management.ref-table')
            @break

            @case('dosen')
                @include('livewire.admin.user-management.user-table', ['withRPS' => true])
    @include('livewire.admin.user-management.user-rps-list')
            @break
        @endswitch
    </div>

    {{-- --- AREA INCLUDE MODALS --- --}}
    @include('livewire.staff.obe-management.rps-management.rps-modal-form')
    @include('livewire.staff.obe-management.rps-management.rps-show-modal')

    @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form')
    @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form')
    @include('livewire.staff.obe-management.cpl-management.cpl-modal-form')
    @include('livewire.staff.obe-management.ref-management.ref-modal-form')

    @include('livewire.admin.user-management.user-modal-form', ['withRPS' => true])

    @include('livewire.staff.obe-management.rps-management.rps-modal-delete')
    @include('livewire.staff.obe-management.cpl-management.cpl-modal-delete')
    @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-delete')
    @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-delete')
    @include('livewire.admin.user-management.user-modal-delete')


</div>
