<x-global.main-layout-table :paginator="$sesis">

    @php
        $padingKolom = 'px-6 py-4 text-sm';
        $headKolom =
            'bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] uppercase text-xs ' .
            $padingKolom;

        $mainKolom =
            'bg-[var(--main-table-trans)] border-[var(--border-table-color)] text-[var(--contrast-main-text)]' .
            ' border-x ' .
            $padingKolom;
        $secondKolom = 'bg-[var(--second-table-trans)] text-[var(--contrast-second-text)] ' . $padingKolom;

        $headSubKolom =
            'bg-[var(--main-table-color)] border-[var(--border-table-color)] text-[var(--focus-color)] border-x border-b text-center font-bold uppercase ' .
            $padingKolom;
        $subKolom =
            'bg-[var(--sub-table-trans)] border-[var(--border-table-color)] text-[var(--contrast-second-text)] ' .
            $padingKolom;
    @endphp

    @php
        $borderR = 'border-[var(--border-table-color)] border-r';
    @endphp

    <x-slot:sortir>
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'pertemuan_ke',
            'headString' => 'Pertemuan',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'jumlah_absensi',
            'headString' => 'Absensi',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'tanggal_pelaksanaan',
            'headString' => 'Tanggal',
        ])
        @include('livewire.global.table.head-sortir', ['sortFieldString' => 'metode'])
    </x-slot:sortir>
    <x-slot:search>
        <div class="w-full md:w-96 xl:w-108">
            <div class="col-start-1 row-start-1 w-full">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Sesi Pertemuan Kelas...',
                    'isLive' => 1,
                    'isBorder' => 2,
                ])
            </div>
        </div>
    </x-slot:search>

    <x-slot:header>
        {{-- BARIS PERTAMA --}}
        <tr>

            {{-- Kolom yang ditarik ke bawah (Tinggi 2 baris) --}}
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'id',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'metode',
                'isCenter' => 1,
                'isMain' => 1,
                'rowSpan' => 2,
            ])
            {{-- <th rowspan="2" class="{{ $headKolom }} border-x">Metode</th> --}}

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'pertemuan_ke',
                'headString' => 'Pertemuan',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])

            <th colspan="4" class="{{ $headSubKolom }}">
                Informasi Sesi Kelas
            </th>

            <th colspan="5" class="{{ $headSubKolom }}">
                Informasi Sub-CPMK
            </th>

            @if (Auth::user()->admin || Auth::user()->dosen)
                <th rowspan="2" class="{{ $headKolom }} border-x">Aksi</th>
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
                'sortFieldString' => 'jumlah_absensi',
                'headString' => 'Absensi',
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

        </tr>
    </x-slot:header>


    @forelse($sesis as $s)
        <tr wire:key="kelas-{{ $s->id }}" data-kelas-id="{{ $s->id }}"
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="{{ $secondKolom }} text-center">{{ $s->id }}</td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.metode-badge', [
                            'xValue' => $s->metode,
                        ])
                    </button>

                    @include(
                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                        [
                            'x' => $s,
                            'editString' => 'editSesi',
                            'nameXString' => 'Sesi',
                            'confirmDeleteString' => 'deleteSesi',
                            'copyName' => 'Kode Sub-CPMK',
                            'copyText' => $s->kode_scpmk ?? '',
                        ]
                    )

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} text-center whitespace-nowrap">{{ $s->pertemuan_ke }}</td>

            <td class="{{ $mainKolom }} text-center whitespace-nowrap">{{ $s->hari }}</td>
            <td class="{{ $subKolom }} text-center whitespace-nowrap">{{ $s->jam_pelaksanaan }}</td>
            <td class="{{ $subKolom }} text-center whitespace-nowrap">
                {{ ($s->mhs_absensi ?? 0) . ' / ' . $s->count_mahasiswa }}</td>
            <td class="{{ $subKolom }} text-center whitespace-nowrap">{{ $s->tanggal_pelaksanaan }}</td>

            <td class="{{ $mainKolom }} text-center whitespace-nowrap">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        <flux:badge icon="academic-cap" color="fuchsia" size="sm">{{ $s->kode_scpmk ?? '---' }}
                        </flux:badge>
                    </button>

                    @include(
                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                        [
                            'x' => $s,
                            'editString' => 'editSesi',
                            'nameXString' => 'Sesi',
                            'confirmDeleteString' => 'deleteSesi',
                            'copyName' => 'Kode Sub-CPMK',
                            'copyText' => $s->kode_scpmk ?? '',
                        ]
                    )
                </flux:dropdown>
            </td>
            <td class="{{ $subKolom }} {{ $borderR }} text-center whitespace-nowrap">
                {{ $s->bobot ? $s->bobot . '%' : '-' }}</td>
            {{-- <td class="{{ $subKolom }} min-w-84">{{ $s->deskripsi ?? '-' }}</td>
            <td class="{{ $subKolom }} min-w-48">{{ $s->materi ?? '-' }}</td>
            <td class="{{ $subKolom }} min-w-48">{{ $s->metodologi ?? '-' }}</td>
            <td class="{{ $subKolom }} min-w-48">{{ $s->indikator ?? '-' }}</td> --}}

            <td class="{{ $subKolom }} min-w-48">{{ $s->tugas ?? '-' }}</td>
            <td class="{{ $subKolom }} whitespace-nowrap text-center">
                {{ $s->w_tugas ?? 0 }} menit</td>
            <td class="{{ $subKolom }} whitespace-nowrap text-center">
                {{ $s->w_mandiri ?? 0 }} menit</td>

            @if (Auth::user()->admin || Auth::user()->dosen)
                <td class="{{ $mainKolom }} text-center">
                    <flux:dropdown>
                        <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                            inset="top bottom">
                        </flux:button>

                        @include(
                            'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                            [
                                'x' => $s,
                                'editString' => 'editSesi',
                                'nameXString' => 'Sesi',
                                'confirmDeleteString' => 'deleteSesi',
                                'copyName' => 'Kode Sub-CPMK',
                                'copyText' => $s->kode_scpmk ?? '',
                            ]
                        )

                    </flux:dropdown>
                </td>
            @endif

            {{-- <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $s->created_day ?? '-' }}</td>
            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $s->updated_day ?? '-' }}</td> --}}
        </tr>
    @empty
        <tr>
            <td colspan="{{ Auth::user()->admin || Auth::user()->dosen ? '15' : '14' }}"
                class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Sesi Pertemuan Kelas ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
