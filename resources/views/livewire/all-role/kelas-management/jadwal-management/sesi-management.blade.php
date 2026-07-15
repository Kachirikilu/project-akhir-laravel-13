<div x-data="{ activeTable: '{{ $switchTable ?? 'hari-ini' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === {{ $isJadwalOnly ?? null ? 'sesi-jadwal-kelas' : 'sesi-management' }} || segment === '') ? 'hari-ini' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    @include('livewire.global.header.tag-user')

    @include('livewire.all-role.kelas-management.jadwal-management.jadwal-header', [
        'alpine' => 'sesi',
        'backUrl' =>
            $isJadwalOnly ?? null
                ? route('jadwal-kelas')
                : route('jadwal-management', ['kode_kelas' => $kode_kelas_url]),
        'mainKode' => $kode_jadwal_url ?? '-',
        'subLabel' => 'Kelas ' . ($jadwal->label_extra ?? '- ---'),
        'mainHead' => 'Jadwal Kelas',
        'subHead' => 'Sesi Kelas',
    ])

    @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-switch-table')

    <div wire:loading.class="opacity-50" wire:target="switchingTable">
        @if ($this->switchTable == 'hari-ini' && $haveSesiDay == true && $stats['sesi-hari-ini'] <= 4)
            @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-hari-ini', ['mb' => 'mb-6'])
        @elseif ($this->switchTable == 'card' || ($this->switchTable == 'hari-ini' && ($haveSesiDay == false || $stats['sesi-hari-ini'] >= 4)))
            @if ($this->switchTable == 'hari-ini' && $stats['sesi-hari-ini'] == 0)
                @include('livewire.all-role.kelas-management.jadwal-management.jadwal-kosong-message')
            @endif
            @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-card')
        @elseif ($this->switchTable == 'table')
            @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-table')
        @elseif ($this->switchTable == 'mahasiswa')
            @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.mahasiswa-sesi-table')
        @elseif ($this->switchTable == 'cpmk')
            @if (Auth::user()->admin || Auth::user()->dosen)
                @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.mahasiswa-cpmk-sesi-table')
            @endif
        @endif
    </div>

    @if (Auth::user()->mahasiswa)
        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-left')
    @endif
</div>
