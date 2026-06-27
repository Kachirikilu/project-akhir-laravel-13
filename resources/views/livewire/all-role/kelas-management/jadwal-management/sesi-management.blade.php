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
        'backUrl' =>
            $isJadwalMhs ?? null
                ? route('jadwal-mahasiswa')
                : route('jadwal-management', ['kode_kelas' => $kode_kelas_url]),
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
        @elseif ($this->switchTable == 'cpmk')
            @if (Auth::user()->admin || Auth::user()->dosen)
                {{-- <div class="flex justify-end mb-4 no-print">
                    <button type="button" onclick="window.print()"
                        class="cursor-pointer text-sm sm:text-md flex items-center gap-2 bg-blue-600 text-white px-12 py-2 rounded shadow hover:bg-blue-700 transition">
                        Cetak/Preview PDF
                    </button>
                </div>

                <div id="print-content">
                    @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.cpmk-grafik-table')
                </div> --}}

                {{-- @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.cpmk-grafik-table') --}}
                @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.cpmk-mahasiswa-table')
            @endif
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
