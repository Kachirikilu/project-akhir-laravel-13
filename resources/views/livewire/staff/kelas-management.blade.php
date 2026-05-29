<div x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'kelas-management' || segment === '') ? '' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">
    @include('livewire.staff.kelas-management.kelas-toolbar')
    @include('livewire.staff.kelas-management.kelas-switch-table')

    @include('livewire.staff.kelas-management.kelas-search-and-filters')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @if (Auth::user()->mahasiswa)
            @include('livewire.staff.kelas-management.kelas-card')
        @else
            @include('livewire.staff.kelas-management.kelas-table')
        @endif
    </div>

    @if (Auth::user()->admin || Auth::user()->dosen)
        @include('livewire.staff.kelas-management.kelas-modal-form')
    @endif
    @include('livewire.staff.obe-management.rps-management.rps-show-modal', [
        'alpineKey' => 'kelas?.rps_id_show',
        'isEdit' => 0,
    ])

    {{-- @include('livewire.staff.kelas-management.kelas-modal-delete') --}}
</div>
