<x-global.main-layout-table :paginator="$kelas">

    <x-slot:sortir>
        <div x-data="{ activeTab: @entangle('filterKelasgg') }"
            class="pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto">
            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByKelasgg',
                'xFilter' => 'filterKelasgg',
                'tabFilter' => $totalGanjilKelas + $totalGenapKelas,
                'tabString' => '',
                'tabNameString' => 'Semua',
                'icon' => 'table-cells',
            ])

            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByKelasgg',
                'xFilter' => 'filterKelasgg',
                'tabFilter' => $totalGanjilKelas,
                'tabString' => 'kelas-ganjil',
                'tabNameString' => 'Ganjil',
                'icon' => 'calendar-days',
            ])

            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByKelasgg',
                'xFilter' => 'filterKelasgg',
                'tabFilter' => $totalGenapKelas,
                'tabString' => 'kelas-genap',
                'tabNameString' => 'Genap',
                'icon' => 'calendar-days',
            ])
        </div>
    </x-slot:sortir>
    <x-slot:search>
        <div x-data="{ activeTab: @entangle('switchTable2') }" class="pb-1 scrollbar-tiny flex space-x-4 overflow-x-auto">
            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'switchingTable2',
                'xFilter' => 'switchTable2',
                'tabFilter' => $totalGanjilKelas + $totalGenapKelas,
                'tabString' => 'kelas-card',
                'tabNameString' => 'Daftar Kelas',
                'icon' => 'rectangle-group',
            ])

            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'switchingTable2',
                'xFilter' => 'switchTable2',
                'tabFilter' => $totalGanjilKelas + $totalGenapKelas,
                'tabString' => 'kelas-table',
                'tabNameString' => 'Tabel Kelas',
                'icon' => 'table-cells',
            ])
        </div>
    </x-slot:search>

    <x-slot:header>
        {{-- BARIS PERTAMA --}}
        <tr>

            @if (Auth::user()->admin || Auth::user()->dosen)
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'id',
                    'rowSpan' => 2,
                    'isCenter' => 1,
                ])
            @endif
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode',
                'rowSpan' => 2,
                'isCenter' => 1,
                'isMain' => 1,
                'isSticky' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode_rps',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
            <th rowspan="2" class="table-head table-border-x">Show</th>
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kelas',
                'rowSpan' => 2,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'program_studi',
                'rowSpan' => 2,
            ])

            <th colspan="4" class="table-head-sub table-border-l">
                Informasi Jadwal Kelas
            </th>


            <th colspan="6" class="table-head-sub table-border-l">
                Informasi Mata Kuliah
            </th>


            <th rowspan="2" class="table-head table-border-x">Aksi</th>

            @if (Auth::user()->admin || Auth::user()->dosen)
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
            @endif
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
                'isBorderL' => 1,
            ])

        </tr>
    </x-slot:header>


    @forelse($kelas as $k)
        <tr wire:key="kelas-{{ $k->id }}" data-kelas-id="{{ $k->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

            @if (Auth::user()->admin || Auth::user()->dosen)
                <td class="table-second text-center">{{ $k->id }}</td>
            @endif
            <td class="table-main-sticky text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.level-mk-badge', [
                            'xValue' => $k->kode,
                            'sortir' => $k->rps_rel?->mk_rel?->level_mk,
                        ])
                    </button>
                    @include('livewire.all-role.kelas-management.kelas-toolbar-table', ['key' => 1])
                </flux:dropdown>
            </td>

            <td class="table-second text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.semester-badge', [
                            'xValue' => $k->kode_rps,
                            'sortir' => $k->semester,
                        ])
                    </button>
                    @include('livewire.all-role.kelas-management.kelas-toolbar-table', ['key' => 2])
                </flux:dropdown>
            </td>
            <td class="table-second table-border-x text-center whitespace-nowrap">
                @if (!$k->trashed())
                    <x-button-action color="emerald"
                        href="{{ route('jadwal-management', $k->kode) }}" wire:navigate>
                        <flux:icon name="calendar-days" class="w-3.5 h-3.5" />
                        <span>Lihat Kelas
                    </x-button-action>
                @else
                    <code
                        class="font-mono text-xs bg-[var(--second-table-color)] px-1.5 py-0.5 rounded border table-border text-[var(--contrast-main-text)] italic">
                        unfound
                    </code>
                @endif
            </td>
            <td class="table-second min-w-84">{{ $k->kelas ?? '-' }}</td>
            <td class="table-second min-w-24">{{ $k->prodi ?? '-' }} ({{ $k->kode_pr ?? '---' }})</td>

            <td class="table-main text-center align-top table-border-x">
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
            <td class="table-sub whitespace-nowrap text-center align-top">
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
            <td class="table-second text-center">
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
            <td class="table-sub whitespace-nowrap text-center align-top">
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

            <td class="table-main text-center table-border-x">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.level-mk-badge', [
                            'xValue' => $k->kode_mk,
                            'sortir' => $k->rps_rel?->mk_rel?->level_mk,
                        ])
                    </button>
                    @include('livewire.all-role.kelas-management.kelas-toolbar-table', ['key' => 3])
                </flux:dropdown>
            </td>
            <td class="table-second min-w-42">{{ $k->mk ?? '-' }}</td>
            <td class="table-sub text-center">{{ $k->semester ?? '-' }}</td>
            <td class="table-sub text-center whitespace-nowrap">{{ $k->sks ?? '-' }} SKS</td>
            <td class="table-sub text-center whitespace-nowrap">{{ $k->sks_text ?? '-' }}</td>

            <td class="table-second table-border-l text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.wajib-badge', [
                            'xValue' => $k->wajib_text,
                            'sortir' => $k->wajib,
                        ])
                    </button>
                    @include('livewire.all-role.kelas-management.kelas-toolbar-table', ['key' => 4])
                </flux:dropdown>
            </td>

            <td class="table-main text-center table-border-x">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>
                    @include('livewire.all-role.kelas-management.kelas-toolbar-table', ['key' => 5])
                </flux:dropdown>
            </td>

            @if (Auth::user()->admin || Auth::user()->dosen)
                <td class="table-second whitespace-nowrap text-center">{{ $k->created_day ?? '-' }}</td>
                <td class="table-second whitespace-nowrap text-center">{{ $k->updated_day ?? '-' }}</td>
            @endif
        </tr>
    @empty
        <tr>
            <td colspan="19" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Kelas ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
