<x-global.main-layout-table :paginator="$kelas">

    <x-slot:sortir>
        <div x-data="{ activeTab: @entangle('filterKelasgg') }"
            class="pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto">
            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByKelasgg',
                'xFilter' => 'filterKelasgg',
                'tabFilter' => $totalGanjil + $totalGenap,
                'tabString' => '',
                'tabNameString' => 'Semua',
                'icon' => 'table-cells',
            ])

            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByKelasgg',
                'xFilter' => 'filterKelasgg',
                'tabFilter' => $totalGanjil,
                'tabString' => 'kelas-ganjil',
                'tabNameString' => 'Ganjil',
                'icon' => 'calendar-days',
            ])

            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByKelasgg',
                'xFilter' => 'filterKelasgg',
                'tabFilter' => $totalGenap,
                'tabString' => 'kelas-genap',
                'tabNameString' => 'Genap',
                'icon' => 'calendar-days',
            ])
        </div>
    </x-slot:sortir>

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
            <th rowspan="2" class="table-head border-x">Show</th>
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kelas',
                'rowSpan' => 2,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'program_studi',
                'rowSpan' => 2,
            ])

            <th colspan="4" class="table-head-sub">
                Informasi Jadwal Kelas
            </th>


            <th colspan="6" class="table-head-sub">
                Informasi Mata Kuliah
            </th>


            <th rowspan="2" class="table-head border-x">Aksi</th>

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

            {{-- Informasi MK --}}
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
                'sortFieldString' => 'pembelajaran',
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'wajib',
                'isCenter' => 1,
                'isMain' => 1,
            ])

        </tr>
    </x-slot:header>


    @forelse($kelas as $k)
        <tr wire:key="kelas-{{ $k->id }}" data-kelas-id="{{ $k->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

            <td class="text-xs sm:text-sm table-second text-center">{{ $k->id }}</td>
            <td class="text-xs sm:text-sm table-main text-center">
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

            <td class="text-xs sm:text-sm table-second table-border-r text-center">
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

            <td class="text-xs sm:text-sm table-second table-border-r">
                <x-button-action color="emerald" href="{{ route('jadwal-management', $k->kode) }}" wire:navigate>
                    <flux:icon name="rectangle-group" class="w-3.5 h-3.5" />
                </x-button-action>
            </td>
            <td class="text-xs sm:text-sm table-second min-w-84">{{ $k->kelas ?? '-' }}</td>
            <td class="text-xs sm:text-sm table-second min-w-24">{{ $k->prodi ?? '-' }} ({{ $k->kode_pr ?? '---' }})</td>

            <td class="text-xs sm:text-sm table-main text-center align-top">
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
            <td class="text-xs sm:text-sm table-sub whitespace-nowrap text-center align-top">
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
            <td class="text-xs sm:text-sm table-sub text-center">
                @if ($k->jadwals->isEmpty())
                    -
                @else
                    <ul class="text-left text-sm whitespace-nowrap align-top">
                        @foreach ($k->jadwals->sortBy(['label_kelas', 'kode_wilayah'])->take(4) as $jadwal)
                            <li class="text-center">{{ $jadwal->count_mhs_jadwal }}</li>
                        @endforeach
                    </ul>
                @endif
            </td>
            <td class="text-xs sm:text-sm table-sub table-border-r whitespace-nowrap text-center align-top">
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

            <td class="text-xs sm:text-sm table-main text-center">
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
            <td class="text-xs sm:text-sm table-sub min-w-42">{{ $k->mk ?? '-' }}</td>
            <td class="text-xs sm:text-sm table-sub text-center">{{ $k->semester ?? '-' }}</td>
            <td class="text-xs sm:text-sm table-sub text-center whitespace-nowrap">{{ $k->sks ?? '-' }} SKS</td>
            <td class="text-xs sm:text-sm table-sub text-center whitespace-nowrap">{{ $k->sks_text ?? '-' }}</td>

            <td class="text-xs sm:text-sm table-second table-border-r table-border-l text-center">
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

            <td class="text-xs sm:text-sm table-main text-center">
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

            <td class="text-xs sm:text-sm table-second whitespace-nowrap text-center">{{ $k->created_day ?? '-' }}</td>
            <td class="text-xs sm:text-sm table-second whitespace-nowrap text-center">{{ $k->updated_day ?? '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="19" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Kelas ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
