<x-global.main-layout-table :paginator="$sesis" :onlyAdmin="!Auth::user()->admin">

    <x-slot:sortir>
        <div
            class="w-full pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto shrink-0">

            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'pertemuan_ke',
                'headString' => 'Pertemuan',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'total_absensi',
                'headString' => 'Absensi',
            ])
            @include('livewire.global.table.head-sortir', ['sortFieldString' => 'metode'])
            @include('livewire.global.table.head-sortir', ['sortFieldString' => 'bobot'])
        </div>
    </x-slot:sortir>
    <x-slot:search>
        <div class="w-full md:w-96 xl:w-108">
            <div class="col-start-1 row-start-1 w-full">
                {{-- @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Sesi Pertemuan Kelas...',
                    'isLive' => 1,
                    'isBorder' => 2,
                ]) --}}

                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Sesi Pertemuan Kelas...',
                    'searchMode' => $searchMode,
                    'searchValues' => ['simple', 'smart', 'complex'],
                    'searchOptions' => ['Cari Kode Kelas', 'Pencarian Cerdas', 'Pencarian Kompleks'],
                ])
            </div>
        </div>
    </x-slot:search>

    <x-slot:header>
        {{-- BARIS PERTAMA --}}
        <tr>

            @if (Auth::user()->admin || Auth::user()->dosen)
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'id',
                    'isCenter' => 1,
                    'rowSpan' => 2,
                ])
            @endif

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'metode',
                'isCenter' => 1,
                'isMain' => 1,
                'rowSpan' => 2,
            ])
            {{-- <th rowspan="2" class="table-head border-x">Metode</th> --}}

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'pertemuan_ke',
                'headString' => 'Pertemuan',
                'isCenter' => 1,
                'rowSpan' => 2,
                'isSticky' => 1,
            ])

            <th colspan="5" class="table-head-sub">
                Informasi Sesi Kelas
            </th>

            <th colspan="6" class="table-head-sub">
                Informasi Sub-CPMK
            </th>

            @if (Auth::user()->admin || Auth::user()->dosen)
                <th rowspan="2" class="table-head border-x">Aksi</th>
            @endif

            {{-- @include('livewire.global.table.head-table', [
                'sortFieldString' => 'created_at',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'updated_at',
                'isCenter' => 1,
                'rowSpan' => 2,
            ]) --}}
        </tr>

        <tr>
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'hari_pelaksanaan',
                'headString' => 'Hari',
                'isMain' => 1,
                'isCenter' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'jam_pelaksanaan',
                'headString' => 'Jam',
                'isCenter' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'total_absensi',
                'headString' => 'Absensi',
                'isCenter' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'total_absensi_all',
                'headString' => 'Absensi Terdata',
                'isCenter' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'tanggal_pelaksanaan',
                'headString' => 'Tanggal',
                'isCenter' => 1,
            ])

            {{-- Sub-CPMK --}}

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode_scpmk',
                'headString' => 'Sub-CPMK',
                'isMain' => 1,
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'bobot',
                'isBorderR' => 1,
            ])


            {{-- @include('livewire.global.table.head-table', [
                'sortFieldString' => 'deskripsi',
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'materi',
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'metodologi',
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'indikator',
            ]) --}}

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'tugas',
                'headString' => 'Deskripsi Tugas',
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'w_tugas',
                'headString' => 'Waktu Tugas',
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'w_mandiri',
                'headString' => 'Waktu Mandiri',
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode_cpmk',
                'headString' => 'CPMK',
                'isMain' => 1,
                'isCenter' => 1,
            ])

        </tr>
    </x-slot:header>


    @php
        $daftarUjian = array_merge(config('app.uts_fields'), config('app.uas_fields'));
    @endphp

    @forelse($sesis as $s)
        @php
            $isUjian = in_array(strtoupper($s->metode ?? ''), $daftarUjian);
            $kehadiran_mhs = Auth::user()->mahasiswa
                ? $s->kehadirans->where('mahasiswa_id', Auth::user()->mahasiswa->id)->first()
                : null;
        @endphp

        <tr wire:key="kelas-sesi-{{ $s->id }}" data-kelas-id="{{ $s->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

            @if (Auth::user()->admin || Auth::user()->dosen)
                <td class="table-second text-center">{{ $s->id }}</td>
            @endif

            <td class="table-main text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.metode-badge', [
                            'xValue' => $s->metode,
                        ])
                    </button>
                    @include(
                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                        ['key' => 1]
                    )
                </flux:dropdown>
            </td>

            <td class="table-second-sticky text-center whitespace-nowrap">{{ $s->pertemuan_ke }}</td>

            <td class="table-main text-center whitespace-nowrap">{{ $s->hari }}</td>
            <td class="table-sub text-center whitespace-nowrap">{{ $s->jam_pelaksanaan }}</td>
            <td class="table-sub text-center whitespace-nowrap">
                {{ $s->total_absensi . ' / ' . ($s->count_mahasiswa ?? 0) }}</td>
            <td class="table-sub text-center whitespace-nowrap">{{ $s->total_absensi_all ?? 0 }}</td>
            <td class="table-second text-center whitespace-nowrap">{{ $s->tanggal_pelaksanaan }}</td>

            <td class="table-main text-center whitespace-nowrap">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        <flux:badge icon="academic-cap" color="fuchsia" size="sm">{{ $s->kode_scpmk ?? '---' }}
                        </flux:badge>
                    </button>
                    @include(
                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                        ['key' => 2]
                    )
                </flux:dropdown>
            </td>
            <td class="table-sub table-border-r text-center whitespace-nowrap">
                {{ $s->bobot_normalisasi ? $s->bobot_normalisasi . '%' : '-' }}</td>
            {{-- <td class="table-sub min-w-84">{{ $s->deskripsi ?? '-' }}</td>
            <td class="table-sub min-w-48">{{ $s->materi ?? '-' }}</td>
            <td class="table-sub min-w-48">{{ $s->metodologi ?? '-' }}</td>
            <td class="table-sub min-w-48">{{ $s->indikator ?? '-' }}</td> --}}

            <td class="table-sub min-w-48">{{ $s->tugas ?? '-' }}</td>
            <td class="table-sub whitespace-nowrap text-center">
                {{ $s->w_tugas ?? 0 }} menit</td>
            <td class="table-sub table-border-r whitespace-nowrap text-center">
                {{ $s->w_mandiri ?? 0 }} menit</td>

            <td class="table-second text-center whitespace-nowrap">

                <flux:dropdown>
                    <button class="cursor-pointer">
                        <flux:badge icon="academic-cap" color="sky" size="sm">{{ $s->kode_cpmk ?? '---' }}
                        </flux:badge>
                    </button>
                    @include(
                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                        ['key' => 3]
                    )
                </flux:dropdown>
            </td>

            @if (Auth::user()->admin || Auth::user()->dosen)
                <td class="table-main text-center">
                    <flux:dropdown>
                        <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                            inset="top bottom">
                        </flux:button>
                        @include(
                            'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                            ['key' => 4]
                        )
                    </flux:dropdown>
                </td>
            @endif
            {{-- <td class="table-second whitespace-nowrap text-center">{{ $s->created_day ?? '-' }}</td>
            <td class="table-second whitespace-nowrap text-center">{{ $s->updated_day ?? '-' }}</td> --}}
        </tr>
    @empty
        <tr>
            <td colspan="{{ Auth::user()->admin || Auth::user()->dosen ? '17' : '15' }}"
                class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Sesi Pertemuan Kelas ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
