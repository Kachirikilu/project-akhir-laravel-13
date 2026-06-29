<div x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'program-studi-management' || segment === '') ? '' : segment;
     " class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">
     
    @include('livewire.admin.prodi-management.prodi-toolbar')
    @include('livewire.admin.prodi-management.prodi-switch-table')

    @include('livewire.admin.prodi-management.prodi-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @include('livewire.admin.prodi-management.prodi-table', [
            'xResults' => match ($this->switchTable) {
                '', 'prodi' => $prodis,
                'departemen' => $departemens,
                'fakultas' => $fakultas,
                default => collect([]),
            },
            'xNameString' => match ($this->switchTable) {
                '', 'prodi' => 'Program Studi',
                'departemen' => 'Departemen',
                'fakultas' => 'Fakultas',
                default => 'Data',
            },
        ])
    </div>

    <livewire:admin.prodi-management.modal-prodi-management lazy />
    <livewire:admin.prodi-management.delete-prodi-management lazy />
    {{-- @include('livewire.admin.prodi-management.prodi-modal-form')
    @include('livewire.admin.prodi-management.prodi-modal-delete') --}}
</div>

{{-- <script defer>
    document.addEventListener('alpine:init', () => {
        Alpine.store('config', {
            typeModal: 'prodi',
            isEdit: 0,
            colorIcon: 'text-red-700',

            nama_pr: '',
            strata: '',
            dp_id: '',
            nama_dp: '',
            fk_id: '',
            nama_fk: '',

            setType(val) {
                this.typeModal = val
            },

            setEdit(val) {
                this.isEdit = val
            },

            setColor(val) {
                this.colorIcon = val
            },

            setValueProdi(namaProdi, strata, idDp, namaDepartemen, idFk, namaFakultas) {
                this.nama_pr = namaProdi
                this.strata = strata
                this.dp_id = idDp
                this.nama_dp = namaDepartemen
                this.fk_id = idFk
                this.nama_fk = namaFakultas
            },

            reset() {
                this.typeModal = 'prodi'
                this.isEdit = 0
                this.colorIcon = 'text-gray-700'

                this.nama_pr = ''
                this.strata = ''
                this.dp_id = ''
                this.nama_dp = ''
                this.fk_id = ''
                this.nama_fk = ''
            }
        })
    })
</script> --}}
