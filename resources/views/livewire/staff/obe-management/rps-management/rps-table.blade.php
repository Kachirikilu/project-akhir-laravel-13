<x-global.main-layout-table :paginator="$rps">
    <x-slot:sortir>
        <div x-data="{ activeTab: @entangle('filterRPSgg') }"
            class="pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto">
            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByRPSgg',
                'xFilter' => 'filterRPSgg',
                'tabFilter' => $totalGanjilRPS + $totalGenapRPS,
                'tabString' => '',
                'tabNameString' => 'Semua',
                'icon' => 'table-cells',
            ])

            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByRPSgg',
                'xFilter' => 'filterRPSgg',
                'tabFilter' => $totalGanjilRPS ?? 0,
                'tabString' => 'rps-ganjil',
                'tabNameString' => 'Ganjil',
                'icon' => 'calendar-days',
            ])

            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByRPSgg',
                'xFilter' => 'filterRPSgg',
                'tabFilter' => $totalGenapRPS ?? 0,
                'tabString' => 'rps-genap',
                'tabNameString' => 'Genap',
                'icon' => 'calendar-days',
            ])
        </div>
    </x-slot:sortir>

    <x-slot:header>

        <tr>
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'id',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])


            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode',
                'isMain' => 1,
                'isCenter' => 1,
                'rowSpan' => 2,
                'isSticky' => 1,
            ])


            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'akademik',
                'headString' => 'Tahun Akademik',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])

            @if ($withCapaian ?? null)
                <th colspan="3" class="table-head-sub">
                    Nilai Capaian
                </th>
            @endif

            <th colspan="6" class="table-head-sub">
                Mata Kuliah
            </th>

            @if (!($withCapaian ?? false))
                <th colspan="3" class="table-head-sub">
                    Capaian Pebelajaran Mata Kuliah
                </th>
            @endif

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'is_draf',
                'headString' => 'Status',
                'isMain' => 1,
                'rowSpan' => 2,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'revisi',
                'headString' => 'Tanggal Revisi',
                'rowSpan' => 2,
            ])

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
                    'sortFieldString' => 'rekap_rps_pr',
                    'headString' => 'Nilai',
                    'isCenter' => 1,
                    'isBorderL' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'index_rps_pr',
                    'headString' => 'Index',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mutu_rps_pr',
                    'headString' => 'Mutu',
                    'isCenter' => 1,
                    'isMain' => 1,
                ])
            @endif
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode_mk',
                'isMain' => 1,
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'mk',
                'headString' => 'Mata Kuliah',
                'isBorderR' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'semester',
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'sks',
                'isCenter' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'pembelajaran',
                'isCenter' => 1,
                'isBorderR' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'wajib',
                'isMain' => 1,
                'isCenter' => 1,
            ])

            @if (!($withCapaian ?? false))
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'count_cpmk',
                    'headString' => 'CPMK',
                    'isCenter' => 1,
                    'isBorderL' => 1,
                ])

                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'count_scpmk',
                    'headString' => 'Sub-CPMK',
                    'isCenter' => 1,
                ])
                @include('livewire.global.search-and-filters.table-search', [
                    'sortFieldString' => 'total_bobot',
                    'modelString' => 'searchBobotRPS',
                    'resetXFilter' => 'resetInputBobotRPS()',
                    'maxLength' => 3,
                    'withSimbol' => 1,
                    'wInput' => 20,
                    'placeholder' => 'Bobot',
                    'pTop' => 5,
                ])
            @endif

        </tr>
    </x-slot:header>


    @forelse($rps as $r)
        <tr wire:key="{{ $switchTable }}-{{ $r->id }}" data-{{ $switchTable }}-id="{{ $r->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

            <td class="table-second text-center">{{ $r->id }}</td>

            <td class="table-main-sticky text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.level-mk-badge', [
                            'xValue' => $r->kode,
                            'sortir' => $r->level_mk,
                        ])
                    </button>

                    @include('livewire.staff.obe-management.obe-toolbar-table', [
                        'x' => $r,
                        'typeXString' => $switchTable,
                        'nameXString' => 'RPS',
                    ])
                </flux:dropdown>
            </td>


            <td class="table-second whitespace-nowrap text-center">{{ $r->akademik ?? '-' }}</td>


            @if ($withCapaian ?? null)
                <td class="table-second table-border-l whitespace-nowrap text-center">
                    {{ $r->rekap_rps_pr ?? '0.00' }}</td>
                <td class="table-second whitespace-nowrap text-center">
                    {{ $r->index_rps_pr ?? '0.00' }}</td>
                <td class="table-sub table-border-l whitespace-nowrap text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @include('livewire.global.table.badge.nilai-mutu-badge', [
                                'xValue' => $r->mutu_rps_pr ?? 'E',
                            ])
                        </button>
                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $r,
                            'typeXString' => $switchTable,
                            'nameXString' => 'RPS',
                        ])
                    </flux:dropdown>
                </td>
            @endif

            <td class="table-second table-border-x text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.level-mk-badge', [
                            'xValue' => $r->kode_mk,
                            'sortir' => $r->level_mk,
                            'noIcon' => 1,
                        ])
                    </button>

                    @include('livewire.staff.obe-management.obe-toolbar-table', [
                        'x' => $r,
                        'typeXString' => $switchTable,
                        'nameXString' => 'RPS',
                        'copyName' => 'Kode MK',
                        'copyText' => $r->kode_mk ?? '',
                    ])
                </flux:dropdown>
            </td>
            <td class="table-sub table-border-r whitespace-nowrap">{{ $r->mk ?? '-' }}</td>
            <td class="table-sub whitespace-nowrap">Semester {{ $r->semester ?? '-' }}</td>
            <td class="table-sub whitespace-nowrap text-center">{{ $r->sks ?? '-' }} SKS</td>
            <td class="table-second whitespace-nowrap text-center">{{ $r->sks_text ?? '-' }}</td>
            <td class="table-main table-border-r text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.wajib-badge', [
                            'xValue' => $r->wajib_text,
                            'sortir' => $r->wajib,
                        ])
                    </button>
                    @include('livewire.staff.obe-management.obe-toolbar-table', [
                        'x' => $r,
                        'typeXString' => $switchTable,
                        'nameXString' => 'RPS',
                        'copyName' => 'Kode MK',
                        'copyText' => $r->kode_mk ?? '',
                    ])
                </flux:dropdown>
            </td>

            @if (!($withCapaian ?? false))
                <td class="table-second table-border-l whitespace-nowrap text-center">
                    {{-- {{ $r->cpmks_count . ' CPMK' ?? '-' }} --}}
                    {{ $r->count_cpmk . ' CPMK' ?? '-' }}
                </td>
                <td class="table-second whitespace-nowrap text-center">

                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @if ($r->count_scpmk >= 14 && $r->count_scpmk <= 16)
                                <flux:badge color="green" size="sm">
                                    {{ $r->count_scpmk }} Sub-CPMK
                                </flux:badge>
                            @elseif ($r->count_scpmk >= 7 && $r->count_scpmk < 14)
                                <flux:badge color="yellow" size="sm">
                                    {{ $r->count_scpmk }} Sub-CPMK
                                </flux:badge>
                            @elseif ($r->count_scpmk >= 4 && $r->count_scpmk < 7)
                                <flux:badge color="orange" size="sm">
                                    {{ $r->count_scpmk }} Sub-CPMK
                                </flux:badge>
                            @else
                                <flux:badge color="red" size="sm">
                                    {{ $r->count_scpmk ?? 0 }} Sub-CPMK
                                </flux:badge>
                            @endif
                        </button>

                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $r,
                            'typeXString' => $switchTable,
                            'nameXString' => 'RPS',
                        ])
                    </flux:dropdown>
                </td>

                <td class="table-second table-border-r text-center">

                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @if ($r->total_bobot >= 70 && $r->total_bobot < 200)
                                <flux:badge icon="check-circle" color="green" size="sm">
                                    {{ $r->total_bobot }}%
                                </flux:badge>
                            @elseif ($r->total_bobot >= 200)
                                <flux:badge icon="exclamation-triangle" color="blue" size="sm">
                                    {{ $r->total_bobot }}%
                                </flux:badge>
                            @elseif ($r->total_bobot > 20 && $r->total_bobot < 70)
                                <flux:badge icon="clock" color="orange" size="sm">
                                    {{ $r->total_bobot }}%
                                </flux:badge>
                            @else
                                <flux:badge icon="no-symbol" color="red" size="sm">
                                    {{ $r->total_bobot ?? 0 }}%
                                </flux:badge>
                            @endif
                        </button>

                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $r,
                            'typeXString' => $switchTable,
                            'nameXString' => 'RPS',
                        ])
                    </flux:dropdown>

                </td>
            @endif

            <td class="table-main text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @if ($r->draf == 0)
                            <flux:badge color="green" size="sm" icon="check-circle">
                                Aktif
                            </flux:badge>
                        @else
                            <flux:badge color="red" size="sm" icon="document-text">
                                Draf
                            </flux:badge>
                        @endif
                    </button>

                    @include('livewire.staff.obe-management.obe-toolbar-table', [
                        'x' => $r,
                        'typeXString' => $switchTable,
                        'nameXString' => 'RPS',
                    ])
                </flux:dropdown>
            </td>
            <td class="table-second whitespace-nowrap">{{ $r->revisi_day ?? '-' }}</td>


            <td class="table-main text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.staff.obe-management.obe-toolbar-table', [
                        'x' => $r,
                        'typeXString' => $switchTable,
                        'nameXString' => 'RPS',
                    ])

                </flux:dropdown>
            </td>

            @if (!($withCapaian ?? false))
                <td class="table-second whitespace-nowrap text-center">{{ $r->created_day ?? '-' }}</td>
                <td class="table-second whitespace-nowrap text-center">{{ $r->updated_day ?? '-' }}</td>
            @endif
        </tr>
    @empty
        <tr>
            <td colspan="{{ $withCapaian ?? null ? 13 : 17 }}"
                class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Rencana Pembelajaran Semester (RPS) ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
