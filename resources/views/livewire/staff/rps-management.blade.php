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
        @if ($this->switchTable !== 'dosen')
            @include('livewire.staff.obe-management.obe-table', [
                'xResults' => match ($this->switchTable) {
                    'rps' => $rps,
                    'cpl' => $cpl,
                    'cpmk' => $cpmk,
                    'sub-cpmk' => $scpmk,
                    'referensi' => $ref,
                    default => collect([]),
                },
                'xNameString' => match ($this->switchTable) {
                    'rps' => 'RPS',
                    'cpl' => 'CPL',
                    'cpmk' => 'CPMK',
                    'sub-cpmk' => 'Sub-CPMK',
                    'referensi' => 'Referensi',
                    default => 'Data',
                },
            ])
        @else
            @include('livewire.admin.user-management.user-table', ['withRPS' => true])
            @include('livewire.admin.user-management.user-modal-delete')
            @include('livewire.admin.user-management.user-rps-modal-form')
        @endif
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
    @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-delete')
    @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-delete')
    @include('livewire.staff.obe-management.cpl-management.cpl-modal-delete')
    @include('livewire.staff.obe-management.ref-management.ref-modal-delete')
</div>