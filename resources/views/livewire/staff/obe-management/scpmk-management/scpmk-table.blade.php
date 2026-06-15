<x-global.main-layout-table :paginator="$scpmk">

    <x-slot:header>

        <tr>
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'id',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])


            @if ($switchTable !== 'dosen')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'kode',
                    'isMain' => 1,
                    'isCenter' => 1,
                    'rowSpan' => 2,
                ])
            @endif


            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'deskripsi',
                'rowSpan' => 2,
            ])

            @if ($withCapaian ?? null)
                <th colspan="3" class="table-head-sub">
                    Nilai Capaian
                </th>
            @endif

            <th colspan="4" class="table-head-sub">
                Pembelajaran
            </th>
            @if (!($withCapaian ?? false))
                <th colspan="4" class="table-head-sub">
                    Tugas
                </th>
            @endif

            <th rowspan="2" class="table-head border-x">Aksi</th>


            @if (!($withCapaian ?? false))
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'created_at',
                    'isCenter' => 1,
                    'rowSpan' => 2,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'updated_at',
                    'isCenter' => 1,
                    'rowSpan' => 2,
                ])
            @endif

        </tr>

        <tr class="bg-gray-50">
            @if ($withCapaian ?? null)
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'rekap_scpmk_pr',
                    'headString' => 'Nilai',
                    'isCenter' => 1,
                    'isBorderL' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'index_scpmk_pr',
                    'headString' => 'Index',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'akreditas_scpmk_pr',
                    'headString' => 'Akreditas',
                    'isCenter' => 1,
                    'isMain' => 1,
                ])
            @endif
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'metode',
                'isCenter' => 1,
                'rowSpan' => 2,
                'isMain' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'materi',
                'rowSpan' => 2,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'metodologi',
                'rowSpan' => 2,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'indikator',
                'rowSpan' => 2,
            ])

            {{-- @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'bobot',
                    'isMain' => 1,
                    'isCenter' => 1,
                    'rowSpan' => 2,
                ]) --}}
            @if (!($withCapaian ?? false))
                @include('livewire.global.search-and-filters.table-search', [
                    'sortFieldString' => 'bobot',
                    'modelString' => 'searchBobotSCPMK',
                    'resetXFilter' => 'resetInputBobotSCPMK()',
                    'maxLength' => 2,
                    'withSimbol' => 1,
                    'wInput' => 20,
                    'placeholder' => 'Bobot',
                    'isMain' => 1,
                    'isCenter' => 1,
                    'rowSpan' => 2,
                    'pTop' => 5,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'tugas',
                    'headString' => 'Deskripsi Tugas',
                    'rowSpan' => 2,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'w_tugas',
                    'headString' => 'Waktu Tugas',
                    'isCenter' => 1,
                    'rowSpan' => 2,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'w_mandiri',
                    'headString' => 'Waktu Mandiri',
                    'isCenter' => 1,
                    'rowSpan' => 2,
                ])
            @endif
        </tr>
    </x-slot:header>


    @forelse($scpmk as $sc)
        <tr wire:key="{{ $switchTable }}-{{ $sc->id }}" data-{{ $switchTable }}-id="{{ $sc->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="table-second text-center">{{ $sc->id }}</td>

            <td class="table-main text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        <flux:badge icon="academic-cap" color="fuchsia" size="sm">{{ $sc->kode ?? '---' }}
                        </flux:badge>
                    </button>

                    @include('livewire.staff.obe-management.obe-toolbar-table', [
                        'x' => $sc,
                        'typeXString' => $switchTable,
                        'nameXString' => 'Sub-CPMK',
                    ])
                </flux:dropdown>
            </td>

            <td class="table-second min-w-84 text-justify leading-relaxed [hyphens:auto]">
                {{ $sc->deskripsi ?? '-' }}</td>

            @if ($withCapaian ?? null)
                <td class="table-second table-border-l whitespace-nowrap text-center">
                    {{ $sc->rekap_scpmk_pr ?? '0.00' }}</td>
                <td class="table-second whitespace-nowrap text-center">
                    {{ $sc->index_scpmk_pr ?? '0.00' }}</td>
                <td class="table-sub table-border-l whitespace-nowrap text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @include('livewire.global.table.badge.nilai-huruf-badge', [
                                'xValue' => $sc->akreditas_scpmk_pr ?? 'E',
                            ])
                        </button>
                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $sc,
                            'typeXString' => $switchTable,
                            'nameXString' => 'Sub-CPMK',
                        ])
                    </flux:dropdown>
                </td>
            @endif

            <td class="table-main text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.metode-badge', [
                            'xValue' => $sc->metode,
                        ])
                    </button>

                    @include('livewire.staff.obe-management.obe-toolbar-table', [
                        'x' => $sc,
                        'typeXString' => $switchTable,
                        'nameXString' => 'Sub-CPMK',
                    ])
                </flux:dropdown>

            <td class="table-second min-w-48">{{ $sc->materi ?? '-' }}</td>
            <td class="table-second min-w-48">{{ $sc->metodologi ?? '-' }}</td>
            <td class="table-second min-w-48">{{ $sc->indikator ?? '-' }}</td>
            </td>
            @if (!($withCapaian ?? false))
                <td class="table-main text-center">{{ $sc->bobot_format ? $sc->bobot_format . '%' : '-' }}
                </td>
                <td class="table-second min-w-48">{{ $sc->tugas ?? '-' }}</td>
                <td class="table-second whitespace-nowrap text-center">
                    {{ $sc->waktu_tugas ? $sc->w_tugas . ' menit' : '60 m/SKS' }}</td>
                <td class="table-second whitespace-nowrap text-center">
                    {{ $sc->waktu_mandiri ? $sc->w_mandiri . ' menit' : '60 m/SKS' }}</td>
            @endif
            <td class="table-main text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.staff.obe-management.obe-toolbar-table', [
                        'x' => $sc,
                        'typeXString' => $switchTable,
                        'nameXString' => 'Sub-CPMK',
                    ])

                </flux:dropdown>
            </td>

            @if (!($withCapaian ?? false))
                <td class="table-second whitespace-nowrap text-center">{{ $sc->created_day ?? '-' }}</td>
                <td class="table-second whitespace-nowrap text-center">{{ $sc->updated_day ?? '-' }}</td>
            @endif
        </tr>
    @empty
        <tr>
            <td colspan="{{ $withCapaian ?? null ? 11 : 14 }}"
                class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Sub-CPMK ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
