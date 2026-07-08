<div x-data="{ activeTable: '{{ $switchTable ?? 'jadwal-card' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === {{ $isJadwalMhs ?? null ? 'jadwal-mahasiswa' : 'jadwal-management' }} || segment === '') ? 'jadwal-card' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    @include('livewire.global.header.tag-user')

    @if (!$isJadwalMhs)
        @include('livewire.all-role.kelas-management.jadwal-management.jadwal-header', [
            'alpine' => 'jadwal',
            'mainKode' => $kode_kelas_url ?? '-',
            'mainHead' => 'Kelas',
            'subHead' => 'Jadwal Kelas',
        ])
    @endif

    {{-- @include('livewire.staff.obe-management.rps-management.rps-show-modal', [
        'alpineKey' => 'jadwal?.rps_id_show',
        'isEdit' => 0,
    ]) --}}

    @include('livewire.all-role.kelas-management.jadwal-management.jadwal-toolbar')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @if ($this->switchTable == 'jadwal-card')
            @include('livewire.all-role.kelas-management.jadwal-management.jadwal-card')
        @elseif ($this->switchTable == 'jadwal-table')
            @include('livewire.all-role.kelas-management.jadwal-management.jadwal-table')
        @endif
    </div>

    {{-- @if (Auth::user()->admin || Auth::user()->dosen)
        @include('livewire.all-role.kelas-management.jadwal-management.jadwal-modal-form')
        @include('livewire.all-role.kelas-management.jadwal-management.jadwal-modal-delete')
        @include('livewire.all-role.kelas-management.kelas-modal-form')
    @endif --}}
</div>
