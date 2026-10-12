<div x-data="{ activeTable: '{{ $switchTable ?? '' }}' }"
    @table-switched.window="
        activeTable = $event.detail.switchTable;
        window.history.pushState({}, '', $event.detail.targetUrl);
     "
    @navigate.window="
        let segment = window.location.pathname.split('/').pop();
        activeTable = (segment === 'rps-mahasiswa-management' || segment === '') ? '' : segment;
     "
    class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    @if (Auth::user()->admin || Auth::user()->dosen)
        @include('livewire.global.header.tag-user')
    @endif
    {{-- @include('livewire.staff.obe-management.rps-management.rps-show-modal', [
        'alpineKey' => 'nilai?.rps_id_show',
        'isEdit' => 0,
    ]) --}}

    {{-- @php
        $semester7Mks = [
            ['id' => 501, 'kode' => 'TKE4101', 'nama' => 'METODE PENELITIAN DAN PENULISAN ILMIAH', 'sks' => 2],
            ['id' => 502, 'kode' => 'TKE4102', 'nama' => 'TRANSMISI DAYA ARUS BOLAK BALIK DAN GARDU INDUK', 'sks' => 3],
            ['id' => 503, 'kode' => 'TKE4103', 'nama' => 'PERENCANAAN SISTEM KELISTRIKAN', 'sks' => 3],
            ['id' => 504, 'kode' => 'TKE4104', 'nama' => 'PRAKTIKUM MESIN-MESIN LISTRIK', 'sks' => 1],
            ['id' => 505, 'kode' => 'TKE4105', 'nama' => 'PRAKTIKUM DISTRIBUSI SISTEM KETENAGALISTRIKAN', 'sks' => 1],
            ['id' => 506, 'kode' => 'TKE4114', 'nama' => 'PROTEKSI MOTOR LISTRIK', 'sks' => 3],
            ['id' => 507, 'kode' => 'TKE4115', 'nama' => 'PERENCANAAN MESIN-MESIN LISTRIK', 'sks' => 3],
        ];

        $nilais = collect($semester7Mks)->map(function ($mk, $index) {
            $kodeRps = '2122-' . $mk['kode'];
            $rpsId = 100 + $index;

            // Mock Model RPS
            $rps = new \App\Models\Akademik\RPS();
            $rps->forceFill([
                'id' => $rpsId,
                'mk_id' => $mk['id'],
                'deskripsi' => 'RPS ' . $mk['nama'],
                'akademik' => '2021/2022',
                'bobot_uts' => '30',
                'bobot_uas' => '35',
                'is_draf' => 0,
                'revisi' => '2026-10-12',
                'created_at' => '2026-10-12 11:14:35',
                'updated_at' => '2026-10-12 19:39:14',
            ]);

            // Mock Model MataKuliah
            $mkModel = new \App\Models\Akademik\MataKuliah();
            $mkModel->forceFill([
                'id' => $mk['id'],
                'kode_mk' => $mk['kode'],
                'nama_mk' => $mk['nama'],
                'sks_kuliah' => $mk['sks'],
                'kode_rps' => $kodeRps,
            ]);

            $rps->setRelation('mk_rel', $mkModel);

            // Mock Model NilaiMahasiswa
            $nilaiMhs = new \App\Models\Penilaian\NilaiMahasiswa();
            $nilaiMhs->forceFill([
                'id' => 18805 + $index,
                'mahasiswa_id' => 4,
                'rps_id' => $rpsId,
                'kj_id' => null,
                'ganjil_genap' => 'Ganjil',
                'akademik' => '2021/2022',
                'nilai' => '100.00',
                'nilai_array' => array_fill(0, 16, 100),
                'bobot_array' => [
                    0.06,
                    0.06,
                    0.06,
                    0.06,
                    0.06,
                    0.06,
                    0.06,
                    0.06,
                    0.06,
                    0.06,
                    0.08,
                    0.08,
                    0.08,
                    0.06,
                    0.06,
                    0.06,
                ],
                'created_at' => '2026-10-12 11:18:50',
                'updated_at' => '2026-10-12 11:18:50',
            ]);

            $nilaiMhs->setRelation('rps_rel', $rps);

            return $nilaiMhs;
        });
    @endphp --}}

    @include(
        'livewire.staff.nilai-management.nilai-mahasiswa-management.nilai-mahasiswa-view.nilai-mahasiswa-header',
        [
            'alpine' => 'nilai',
            'backUrl' => $isNilaiMhs
                ? route('nilai-mahasiswa')
                : route('nilai-mahasiswa-management', ['nim' => $nim_url]),
        ]
    )

    @include('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mahasiswa-view.rps-mahasiswa-card')

    {{-- @include('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-modal-form') --}}
    {{-- @include('livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-modal-delete') --}}
</div>
