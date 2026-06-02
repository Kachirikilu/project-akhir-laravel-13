<x-global.main-layout-table :paginator="$users">

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
            'headString' => 'NIM',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'name',
            'headString' => 'Nama',
        ])
        @if (Auth::user()->admin || Auth::user()->dosen)
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'mhs_poin_absensi',
                'headString' => 'Absensi',
            ])
        @endif
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'angkatan',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'status',
        ])
    </x-slot:sortir>
    <x-slot:search>
        <div class="w-full md:w-96 xl:w-108">
            <div class="col-start-1 row-start-1 w-full">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Mahasiswa Kelas...',
                    'isLive' => 1,
                    'isBorder' => 2,
                ])
            </div>
        </div>
    </x-slot:search>

    <x-slot:header>
        <tr>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'mahasiswa_id',
                'headString' => 'MHS ID',
                'rowSpan' => 2,
                'isMain' => 1,
                'isCenter' => 1,
            ])

            <th rowspan="2" class="{{ $headKolom }}'">Role</th>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'pertemuan_ke',
                'headString' => 'NIM',
                'rowSpan' => 2,
                'isCenter' => 1,
                'isMain' => 1,
            ])


            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'name',
                'headString' => 'Nama',
                'rowSpan' => 2,
                'isMain' => 1,
            ])

            @if (Auth::user()->admin || Auth::user()->dosen)
                <th colspan="7" class="{{ $headSubKolom }}">
                    Kehadiran
                </th>
                <th colspan="3" class="{{ $headSubKolom }}">
                    Nilai
                </th>
            @endif

            {{-- Angkatan - Autocomplete Input --}}
            @if ($switchTable == 'mahasiswa')
                @include('livewire.global.search-and-filters.table-search', [
                    'sortFieldString' => 'angkatan',
                    'modelString' => 'searchAngkatan',
                    'resetXFilter' => 'resetInputAngkatan()',
                    'wInput' => 15,
                    'numberOnly' => 1,
                    'maxlength' => 4,
                    'placeholder' => 'Tahun',
                    'rowSpan' => 2,
                    'isBorderR' => 1,
                ])
            @endif

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'status',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'prodi',
                'headString' => 'Program Studi',
                'rowSpan' => 2,
            ])
            <th rowspan="2" class="{{ $headKolom }} border-x">Aksi</th>

        </tr>

        <tr>
            @if (Auth::user()->admin || Auth::user()->dosen)
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mhs_poin_absensi',
                    'headString' => 'Poin',
                    'isCenter' => 1,
                    'isMain' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mhs_masuk',
                    'headString' => 'Hadir',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mhs_dispensasi',
                    'headString' => 'Dispensi',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mhs_terlambat',
                    'headString' => 'Terlambat',
                    'isCenter' => 1,
                    'isBorderR' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mhs_izin',
                    'headString' => 'Izin',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mhs_sakit',
                    'headString' => 'Sakit',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mhs_tidak_masuk',
                    'headString' => 'Tidak Hadir',
                    'isCenter' => 1,
                    'isBorderR' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mhs_nilai_akhir',
                    'headString' => 'Angka',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mhs_nilai_index',
                    'headString' => 'Index',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mhs_nilai_huruf',
                    'headString' => 'Huruf',
                    'isCenter' => 1,
                    'isBorderR' => 1,
                ])
                {{-- @elseif (Auth::user()->mahasiswa)
                <th rowspan="1" class="{{ $headKolom }} border-x">Poin</th>
                <th rowspan="1" class="{{ $headKolom }}">Hadir</th>
                <th rowspan="1" class="{{ $headKolom }}">Dispensi</th>
                <th rowspan="1" class="{{ $headKolom }} border-r">Terlambat</th>
                <th rowspan="1" class="{{ $headKolom }}">Izin</th>
                <th rowspan="1" class="{{ $headKolom }}">Sakit</th>
                <th rowspan="1" class="{{ $headKolom }} border-r whitespace-nowrap">Tidak Hadir</th> --}}
            @endif
        </tr>


    </x-slot:header>


    @forelse($users as $user)
        @php
            $detail = $user->admin ?? ($user->dosen ?? $user->mahasiswa);
        @endphp

        <tr wire:key="user-{{ $user->id }}" data-user-id="{{ $user->id }}"
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="{{ $mainKolom }} text-center">{{ $user->mahasiswa->id }}</td>

            {{-- Role --}}
            <td class="{{ $secondKolom }} {{ $borderR }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        <flux:badge icon="book-open" color="cyan" size="sm">Mahasiswa</flux:badge>
                    </button>
                    @include(
                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-toolbar-table',
                        ['x' => $user]
                    )
                </flux:dropdown>
            </td>

            <td class="{{ $mainKolom }} {{ $borderR }} text-center">{{ $user->mahasiswa->nim }}</td>


            @php
                $isMahasiswa = false;
                if (Auth::user()->admin || Auth::user()->dosen) {
                    $isMahasiswa = true;
                } elseif (Auth::user()->mahasiswa && Auth::user()->id == $user->id) {
                    $isMahasiswa = true;
                }
            @endphp

            <td class="{{ $secondKolom }} {{ $borderR }} whitespace-nowrap">{{ $user->name ?? '-' }}</td>

            @if (Auth::user()->admin || Auth::user()->dosen)
                <td class="{{ $secondKolom }} {{ $borderR }} text-center whitespace-nowrap">
                    <flux:dropdown>

                        <button class="cursor-pointer">
                            @if ($isMahasiswa)
                                @php
                                    $poinMhs = round(
                                        (($user->mhs_poin_absensi ?? 0) / (2 * ($totalSesiKelas ?? 16))) * 100,
                                        2,
                                    );
                                @endphp
                                @include('livewire.global.table.badge.poin-absen-badge', [
                                    'xValue' => $poinMhs . '%',
                                    'sortir' => $poinMhs,
                                ])
                            @else
                                -
                            @endif
                        </button>

                        @include(
                            'livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-toolbar-table',
                            ['x' => $user]
                        )
                    </flux:dropdown>
                </td>
                <td class="{{ $subKolom }} text-center whitespace-nowrap">
                    @if ($isMahasiswa)
                        {{ $user->mhs_masuk ?? 0 }} / {{ $totalSesiKelas }} Sesi
                    @else
                        -
                    @endif
                </td>
                <td class="{{ $subKolom }} text-center whitespace-nowrap">
                    @if ($isMahasiswa)
                        {{ $user->mhs_dispensasi ?? 0 }} / {{ $totalSesiKelas }} Sesi
                    @else
                        -
                    @endif
                </td>
                <td class="{{ $subKolom }} {{ $borderR }} text-center whitespace-nowrap">
                    @if ($isMahasiswa)
                        {{ $user->mhs_terlambat ?? 0 }} / {{ $totalSesiKelas }} Sesi
                    @else
                        -
                    @endif
                </td>
                <td class="{{ $subKolom }} text-center whitespace-nowrap">
                    @if ($isMahasiswa)
                        {{ $user->mhs_izin ?? 0 }} / {{ $totalSesiKelas }} Sesi
                    @else
                        -
                    @endif
                </td>
                <td class="{{ $subKolom }} text-center whitespace-nowrap">
                    @if ($isMahasiswa)
                        {{ $user->mhs_sakit ?? 0 }} / {{ $totalSesiKelas }} Sesi
                    @else
                        -
                    @endif
                </td>
                <td class="{{ $subKolom }} {{ $borderR }} text-center whitespace-nowrap">
                    @if ($isMahasiswa)
                        {{ $user->mhs_tidak_masuk ?? 0 }} / {{ $totalSesiKelas }} Sesi
                    @else
                        -
                    @endif
                </td>

                <td class="{{ $subKolom }} text-center whitespace-nowrap">
                    @if ($isMahasiswa)
                        {{ $user->mhs_nilai_akhir ?? 0 }}
                    @else
                        -
                    @endif
                </td>
                <td class="{{ $subKolom }} text-center whitespace-nowrap">
                    @if ($isMahasiswa)
                        {{ $user->mhs_nilai_index ?? 0 }}
                    @else
                        -
                    @endif
                </td>
                <td class="{{ $subKolom }} {{ $borderR }} text-center whitespace-nowrap">
                    @if ($isMahasiswa)
                        {{ $user->mhs_nilai_huruf ?? 0 }}
                    @else
                        -
                    @endif
                </td>
            @endif

            <td class="{{ $secondKolom }} {{ $borderR }} text-center">{{ $detail->angkatan ?? '-' }}</td>

            <td class="{{ $secondKolom }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.status-user-badge', [
                            'xValue' => $user->status,
                        ])
                    </button>
                    @include(
                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-toolbar-table',
                        ['x' => $user]
                    )
                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} min-w-48">
                {{ $user->prodi ?? '-' }} ({{ $user->kode_pr ?? '---' }})</td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>
                    @include(
                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-toolbar-table',
                        ['x' => $user]
                    )
                </flux:dropdown>
            </td>

        </tr>

    @empty
        <tr>
            <td colspan="{{ Auth::user()->admin || Auth::user()->dosen ? '18' : '8' }}"
                class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Mahasiswa Kelas ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
