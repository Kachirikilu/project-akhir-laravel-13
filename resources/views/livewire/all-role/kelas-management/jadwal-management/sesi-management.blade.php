<div x-data="{ activeTable: '{{ $switchTable ?? 'sesi-card' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === {{ $isJadwalMhs ?? null ? 'sesi-mahasiswa' : 'sesi-management' }} || segment === '') ? 'sesi-card' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    @include('livewire.all-role.kelas-management.jadwal-management.jadwal-header', [
        'alpine' => 'sesi',
        'backUrl' => $isJadwalMhs ?? null ? route('jadwal-mahasiswa') : route('jadwal-management', ['kode_kelas' => $kode_kelas_url]),
        'mainKode' => $kode_jadwal_url ?? '-',
        'subLabel' => 'Kelas ' . ($jadwal->label_extra ?? '- ---'),
        'mainHead' => 'Jadwal Kelas',
        'subHead' => 'Sesi Kelas',
    ])

    @include('livewire.staff.obe-management.rps-management.rps-show-modal', [
        'alpineKey' => 'sesi?.rps_id_show',
        'isEdit' => 0,
    ])

    @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-switch-table')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @if ($this->switchTable == 'sesi-card')
            @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-card')
        @elseif ($this->switchTable == 'sesi-table')
            @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-table')
        @elseif ($this->switchTable == 'mahasiswa')
            @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.mahasiswa-table')
        @endif
    </div>

    @if (Auth::user()->mahasiswa)
        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-left')
        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-absen-modal-form')
    @endif


    @if (Auth::user()->admin || Auth::user()->dosen)
        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-modal-form')
        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-modal-form')
        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.nilai-excel-modal-form')
        @include('livewire.all-role.kelas-management.jadwal-management.jadwal-modal-form')
    @elseif (Auth::user()->mahasiswa)
        @include('livewire.all-role.kelas-management.jadwal-management.jadwal-left-modal-form')
    @endif

</div>
