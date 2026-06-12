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

    <div class="shrink-0">
        @include('livewire.global.table.export-button', [
            'nameXString' => 'Export Nilai',
            'xString' => "generateRekapCPLProdi($pr_id_url)",
            'valuePx' => 6,
            'isTextMd' => 1,
            'isNoPb' => 1,
        ])
    </div>

    @include('livewire.staff.obe-management.obe-table', [
        'xResults' => match ($this->switchTable) {
            'cpl' => $cpls,
            default => collect([]),
        },
        'xNameString' => match ($this->switchTable) {
            'cpl' => 'CPL',
            default => 'Data',
        },
        'withCapaian' => 1,
    ])

    @include('livewire.staff.obe-management.rps-management.rps-show-modal')
    @include('livewire.staff.obe-management.cpl-management.cpl-modal-form', ['noModalRPS' => 1])
    @include('livewire.staff.obe-management.cpl-management.cpl-modal-delete')

    {{-- @include('livewire.admin.prodi-management.prodi-modal-form')
    @include('livewire.admin.prodi-management.prodi-modal-delete') --}}
</div>
