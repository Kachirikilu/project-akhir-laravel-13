<div x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'rps-capaian-management' || segment === '') ? '' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    {{-- @include('livewire.admin.prodi-management.prodi-toolbar')
    @include('livewire.admin.prodi-management.prodi-switch-table')

    @include('livewire.admin.prodi-management.prodi-search-and-filters') --}}
    {{-- 
    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.admin.prodi-management.prodi-table', [
            'xResults' => match ($this->switchTable) {
                'prodi' => $prodis,
                'departemen' => $departemens,
                'fakultas' => $fakultas,
                default => collect([]),
            },
            'xNameString' => match ($this->switchTable) {
                'prodi' => 'Program Studi',
                'departemen' => 'Departemen',
                'fakultas' => 'Fakultas',
                default => 'Data',
            },
        ])
    </div> --}}

    {{-- @include('livewire.admin.prodi-management.capaian-management.capaian-table') --}}

    @include('livewire.staff.obe-management.obe-table', [
        'xResults' => match ($this->switchTable) {
            'rps' => $rps,
            default => collect([]),
        },
        'xNameString' => match ($this->switchTable) {
            'rps' => 'RPS',
            default => 'Data',
        },
        'withCapaian' => 1,
    ])

    {{-- @include('livewire.admin.prodi-management.prodi-modal-form')
    @include('livewire.admin.prodi-management.prodi-modal-delete') --}}
</div>
