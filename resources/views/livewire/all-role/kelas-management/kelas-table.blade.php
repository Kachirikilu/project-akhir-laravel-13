<x-global.main-layout-table :paginator="$kelas">

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
        $borderL = 'border-[var(--border-table-color)] border-l';
    @endphp

    <x-slot:header>
        {{-- BARIS PERTAMA --}}
        <tr>

            {{-- Kolom yang ditarik ke bawah (Tinggi 2 baris) --}}
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'id',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode',
                'rowSpan' => 2,
                'isCenter' => 1,
                'isMain' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode_rps',
                'rowSpan' => 2,
                'isCenter' => 1,
                'isBorderR' => 1,
            ])
            <th rowspan="2" class="{{ $headKolom }} border-x">Show</th>
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kelas',
                'rowSpan' => 2,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'prodi',
                'rowSpan' => 2,
                'headString' => 'Program Studi',
            ])

            <th colspan="4" class="{{ $headSubKolom }}">
                Informasi Jadwal Kelas
            </th>


            <th colspan="6" class="{{ $headSubKolom }}">
                Informasi Mata Kuliah
            </th>


            <th rowspan="2" class="{{ $headKolom }} border-x">Aksi</th>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'created_at',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'updated_at',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
        </tr>

        {{-- BARIS KEDUA (Hanya untuk detail SKS) --}}
        <tr class="bg-gray-50">

            {{-- Informasi Kelas --}}
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'hari_pelaksanaan',
                'isCenter' => 1,
                'headString' => 'Hari',
                'isMain' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'jam_pelaksanaan',
                'isCenter' => 1,
                'headString' => 'Jam',
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kapasitas',
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'tanggal_pelaksanaan',
                'isCenter' => 1,
                'headString' => 'Tanggal',
            ])

            {{-- Informasi Kelas --}}
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode_mk',
                'isCenter' => 1,
                'isMain' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'mk',
                'headString' => 'Nama MK',
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'semester',
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'sks',
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'sks_text',
                'isCenter' => 1,
                'headString' => 'Pembelajaran',
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'is_wajib',
                'isCenter' => 1,
                'headString' => 'Wajib',
                'isMain' => 1,
            ])

        </tr>
    </x-slot:header>


    @forelse($kelas as $k)
        <tr wire:key="kelas-{{ $k->id }}" data-kelas-id="{{ $k->id }}"
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="{{ $secondKolom }} text-center">{{ $k->id }}</td>
            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.level-mk-badge', [
                            'xValue' => $k->kode,
                            'sortir' => $k->rps_rel?->mk_rel?->level_mk,
                        ])
                    </button>

                    @include('livewire.all-role.kelas-management.kelas-toolbar-table', [
                        'x' => $k,
                        'editString' => 'editKelas',
                        'nameXString' => 'Kelas',
                        'confirmDeleteString' => 'deleteKelas',
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} {{ $borderR }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.semester-badge', [
                            'xValue' => $k->kode_rps,
                            'sortir' => $k->semester,
                        ])
                    </button>

                    @include('livewire.all-role.kelas-management.kelas-toolbar-table', [
                        'x' => $k,
                        'editString' => 'editKelas',
                        'nameXString' => 'Kelas',
                        'confirmDeleteString' => 'deleteKelas',
                        'copyName' => 'Kode RPS',
                        'copyText' => $k->kode_rps ?? '',
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} {{ $borderR }}">
                <x-button-action color="emerald" href="{{ route('jadwal-management', $k->kode) }}" wire:navigate>
                    <flux:icon name="rectangle-group" class="w-3.5 h-3.5" />
                </x-button-action>
            </td>
            <td class="{{ $secondKolom }} min-w-84">{{ $k->kelas ?? '-' }}</td>
            <td class="{{ $secondKolom }} min-w-24">{{ $k->prodi ?? '-' }} ({{ $k->kode_pr ?? '---' }})</td>

            <td class="{{ $mainKolom }} text-center align-top">
                @if ($k->jadwals->isEmpty())
                    -
                @else
                    <ul class="text-left text-sm whitespace-nowrap">
                        @foreach ($k->jadwals->sortBy(['label_kelas', 'kode_wilayah'])->take(4) as $jadwal)
                            <li><strong class="mr-1">{{ $jadwal->label_full }}:</strong> {{ $jadwal->hari ?? '-' }}
                            </li>
                        @endforeach

                        @if ($k->jadwals->count() > 4)
                            <li class="text-xs text-gray-500 italic mt-1">
                                dan {{ $k->jadwals->count() - 4 }} kelas lainnya...
                            </li>
                        @endif
                    </ul>
                @endif
            </td>
            <td class="{{ $subKolom }} whitespace-nowrap text-center align-top">
                @if ($k->jadwals->isEmpty())
                    -
                @else
                    <ul class="text-left text-sm whitespace-nowrap">
                        @foreach ($k->jadwals->sortBy(['label_kelas', 'kode_wilayah'])->take(4) as $jadwal)
                            <li class="text-left">{{ $jadwal->jam_pelaksanaan ?? '-' }}</li>
                        @endforeach
                    </ul>
                @endif
            </td>
            <td class="{{ $subKolom }} text-center">
                @if ($k->jadwals->isEmpty())
                    -
                @else
                    <ul class="text-left text-sm whitespace-nowrap align-top">
                        @foreach ($k->jadwals->sortBy(['label_kelas', 'kode_wilayah'])->take(4) as $jadwal)
                            <li class="text-center">{{ $jadwal->kapasitas ?? '-' }}</li>
                        @endforeach
                    </ul>
                @endif
            </td>
            <td class="{{ $subKolom }} {{ $borderR }} whitespace-nowrap text-center align-top">
                @if ($k->jadwals->isEmpty())
                    -
                @else
                    <ul class="text-left text-sm whitespace-nowrap">
                        @foreach ($k->jadwals->sortBy(['label_kelas', 'kode_wilayah'])->take(4) as $jadwal)
                            <li>{{ $jadwal->tanggal_pelaksanaan ?? '-' }}</li>
                        @endforeach
                    </ul>
                @endif
            </td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.level-mk-badge', [
                            'xValue' => $k->kode_mk,
                            'sortir' => $k->rps_rel?->mk_rel?->level_mk,
                        ])
                    </button>

                    @include('livewire.all-role.kelas-management.kelas-toolbar-table', [
                        'x' => $k,
                        'editString' => 'editKelas',
                        'nameXString' => 'Kelas',
                        'confirmDeleteString' => 'deleteKelas',
                        'copyName' => 'Kode MK',
                        'copyText' => $k->kode_mk ?? '',
                    ])

                </flux:dropdown>
            </td>
            <td class="{{ $subKolom }} min-w-42">{{ $k->mk ?? '-' }}</td>
            <td class="{{ $subKolom }} text-center">{{ $k->semester ?? '-' }}</td>
            <td class="{{ $subKolom }} text-center whitespace-nowrap">{{ $k->sks ?? '-' }} SKS</td>
            <td class="{{ $subKolom }} text-center whitespace-nowrap">{{ $k->sks_text ?? '-' }}</td>

            <td class="{{ $secondKolom }} {{ $borderR }} {{ $borderL }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.wajib-badge', [
                            'xValue' => $k->wajib_text,
                            'sortir' => $k->wajib,
                        ])
                    </button>

                    @include('livewire.all-role.kelas-management.kelas-toolbar-table', [
                        'x' => $k,
                        'editString' => 'editKelas',
                        'nameXString' => 'Kelas',
                        'confirmDeleteString' => 'deleteKelas',
                        'copyName' => 'Kode MK',
                        'copyText' => $k->kode_mk ?? '',
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.all-role.kelas-management.kelas-toolbar-table', [
                        'x' => $k,
                        'editString' => 'editKelas',
                        'nameXString' => 'Kelas',
                        'confirmDeleteString' => 'deleteKelas',
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $k->created_day ?? '-' }}</td>
            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $k->updated_day ?? '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="19" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Kelas ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
