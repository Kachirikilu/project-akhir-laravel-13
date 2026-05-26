<div x-data="{ activeTable: '{{ $switchTable ?? 'sesi-card' }}' }"
     @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
     @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'sesi-management' || segment === '') ? 'sesi-card' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    @include('livewire.staff.kelas-management.jadwal-management.jadwal-header', [
        'alpine' => 'sesi',
        'backUrl' => route('jadwal-management', ['kode' => $kelas->kode]),
        'mainKode' => $jadwal->kode ?? '-',
        'subLabel' => 'Kelas ' . ($jadwal->label_extra ?? '- ---'),
        'mainHead' => 'Jadwal Kelas',
        'subHead' => 'Sesi Kelas'
    ])

    @include('livewire.staff.obe-management.rps-management.rps-show-modal', ['alpineKey' => 'sesi?.rps_id_show', 'isEdit' => 0])

    @include('livewire.staff.kelas-management.jadwal-management.sesi-management.sesi-switch-table')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @if ($this->switchTable == 'sesi-card')
            @include('livewire.staff.kelas-management.jadwal-management.sesi-management.sesi-card')
        @elseif ($this->switchTable == 'sesi-table')
            @include('livewire.staff.kelas-management.jadwal-management.sesi-management.sesi-table')
        @elseif ($this->switchTable == 'mahasiswa')
            @include('livewire.staff.kelas-management.jadwal-management.sesi-management.mahasiswa-table')
            {{-- @include('livewire.admin.user-management.user-table') --}}
        @endif
    </div>

    @include('livewire.staff.kelas-management.jadwal-management.sesi-management.sesi-modal-form')

</div>

