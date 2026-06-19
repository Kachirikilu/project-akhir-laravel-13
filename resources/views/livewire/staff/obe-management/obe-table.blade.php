<x-global.main-layout-table :paginator="$xResults">

    @php
        $padingKolom = 'px-6 py-4 text-sm';
        $headKolom =
            'bg-[var(--main-table-color)] table-border text-[var(--contrast-main-text)] uppercase text-xs ' .
            $padingKolom;

        $mainKolom =
            'bg-[var(--main-table-trans)] table-border text-[var(--contrast-main-text)]' .
            ' border-x ' .
            $padingKolom;
        $secondKolom = 'bg-[var(--second-table-trans)] text-[var(--contrast-second-text)] ' . $padingKolom;

        $headSubKolom =
            'bg-[var(--main-table-color)] table-border text-[var(--focus-color)] border-x border-b text-center font-bold uppercase ' .
            $padingKolom;
        $subKolom =
            'bg-[var(--sub-table-trans)] table-border text-[var(--contrast-second-text)] ' .
            $padingKolom;
    @endphp

    @php
        $borderX = 'table-border border-x';
        $borderR = 'table-border border-r';
        $borderL = 'table-border border-l';
    @endphp

    @if ($this->switchTable == 'rps')
        <x-slot:sortir>
            <div x-data="{ activeTab: @entangle('filterRPSgg') }"
                class="pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto">
                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'filterByRPSgg',
                    'xFilter' => 'filterRPSgg',
                    'tabFilter' => $totalGanjil + $totalGenap,
                    'tabString' => '',
                    'tabNameString' => 'Semua',
                    'icon' => 'table-cells',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'filterByRPSgg',
                    'xFilter' => 'filterRPSgg',
                    'tabFilter' => $totalGanjil ?? 0,
                    'tabString' => 'rps-ganjil',
                    'tabNameString' => 'Ganjil',
                    'icon' => 'calendar-days',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'filterByRPSgg',
                    'xFilter' => 'filterRPSgg',
                    'tabFilter' => $totalGenap ?? 0,
                    'tabString' => 'rps-genap',
                    'tabNameString' => 'Genap',
                    'icon' => 'calendar-days',
                ])
            </div>
        </x-slot:sortir>
    @endif

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


            @if ($switchTable === 'rps')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'akademik',
                    'headString' => 'Tahun Akademik',
                    'isCenter' => 1,
                    'rowSpan' => 2,
                ])

                <th colspan="6" class="table-head-sub">
                    Mata Kuliah
                </th>
                <th colspan="3" class="table-head-sub">
                    Capaian Pebelajaran Mata Kuliah
                </th>

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
            @endif

            @if ($switchTable === 'cpl' || $switchTable === 'cpmk' || $switchTable === 'sub-cpmk')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'deskripsi',
                    'rowSpan' => 2,
                ])
            @endif

            @if (($withCapaian ?? null) && $switchTable === 'cpl')
                <th colspan="3" class="table-head-sub">
                    Nilai Capaian
                </th>
                <th colspan="3" class="table-head-sub">
                    Rencana Pembelajaran Semester
                </th>
            @elseif ($switchTable === 'cpl')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'count_rps',
                    'headString' => 'Total RPS',
                    'isBorderL' => 1,
                    'isCenter' => 1,
                ])
            @endif
            @if ($switchTable === 'cpmk')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'count_cpl',
                    'headString' => 'Total CPL',
                    'isMain' => 1,
                    'isCenter' => 1,
                    'rowSpan' => 2,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'count_scpmk',
                    'headString' => 'Sub-CPMK',
                    'isCenter' => 1,
                    'rowSpan' => 2,
                ])
                @include('livewire.global.search-and-filters.table-search', [
                    'sortFieldString' => 'total_bobot',
                    'modelString' => 'searchBobotCPMK',
                    'resetXFilter' => 'resetInputBobotCPMK()',
                    'maxLength' => 2,
                    'withSimbol' => 1,
                    'wInput' => 20,
                    'placeholder' => 'Bobot',
                    'rowSpan' => 2,
                    'pTop' => 5,
                ])
            @endif
            @if ($switchTable === 'sub-cpmk')
                <th colspan="4" class="table-head-sub">
                    Pembelajaran
                </th>
                <th colspan="4" class="table-head-sub">
                    Tugas
                </th>
            @endif
            @if ($switchTable === 'referensi')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'judul',
                    'rowSpan' => 2,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'penulis',
                    'rowSpan' => 2,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'penerbit',
                    'rowSpan' => 2,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'tahun',
                    'isMain' => 1,
                    'isCenter' => 1,
                    'rowSpan' => 2,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'link',
                    'rowSpan' => 2,
                ])
            @endif

            <th rowspan="2" class="table-head border-x">Aksi</th>

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

        </tr>

        <tr class="bg-gray-50">
            @if ($switchTable === 'rps')
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

            @if (($withCapaian ?? null) && $switchTable === 'cpl')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'rekap_cpl_pr',
                    'headString' => 'Nilai',
                    'isCenter' => 1,
                    'isBorderL' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'index_cpl_pr',
                    'headString' => 'Index',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mutu_cpl_pr',
                    'headString' => 'Mutu',
                    'isCenter' => 1,
                    'isMain' => 1,
                ])
                <th class="table-head text-center border-x">Show</th>
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'count_rps_pr',
                    'headString' => 'RPS '. ($kode_pr_url ?? 'UNI'),
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'count_rps',
                    'headString' => 'Total RPS',
                    'isCenter' => 1,
                ])
            @endif

            @if ($switchTable === 'sub-cpmk')
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


    @forelse($xResults as $x)
        <tr wire:key="{{ $switchTable }}-{{ $x->id }}" data-{{ $switchTable }}-id="{{ $x->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

            <td class="text-xs sm:text-sm table-second text-center">{{ $x->id }}</td>

            @if ($switchTable !== 'dosen')
                <td class="text-xs sm:text-sm table-main text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @switch($switchTable)
                                @case('rps')
                                    @include('livewire.global.table.badge.level-mk-badge', [
                                        'xValue' => $x->kode,
                                        'sortir' => $x->level_mk,
                                    ])
                                @break

                                @case('cpl')
                                    <flux:badge icon="beaker" color="sky" size="sm">{{ $x->kode ?? '---' }}
                                    </flux:badge>
                                @break

                                @case('cpmk')
                                    <flux:badge icon="academic-cap" color="violet" size="sm">{{ $x->kode ?? '---' }}
                                    </flux:badge>
                                @break

                                @case('sub-cpmk')
                                    <flux:badge icon="academic-cap" color="fuchsia" size="sm">{{ $x->kode ?? '---' }}
                                    </flux:badge>
                                @break

                                @default
                                    <flux:badge icon="book-open" color="orange" size="sm">{{ $x->kode ?? '---' }}
                                    </flux:badge>
                            @endswitch
                        </button>

                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString,
                        ])
                    </flux:dropdown>
                </td>
            @endif

            @if ($switchTable === 'rps')
                <td class="text-xs sm:text-sm table-second whitespace-nowrap text-center">{{ $x->akademik ?? '-' }}</td>
                <td class="text-xs sm:text-sm table-second table-border-r table-border-l text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @include('livewire.global.table.badge.level-mk-badge', [
                                'xValue' => $x->kode_mk,
                                'sortir' => $x->level_mk,
                                'noIcon' => 1,
                            ])
                        </button>

                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString,
                            'copyName' => 'Kode MK',
                            'copyText' => $x->kode_mk ?? '',
                        ])
                    </flux:dropdown>
                </td>
                <td class="text-xs sm:text-sm table-sub table-border-r whitespace-nowrap">{{ $x->mk ?? '-' }}</td>
                <td class="text-xs sm:text-sm table-sub whitespace-nowrap">Semester {{ $x->semester ?? '-' }}</td>
                <td class="text-xs sm:text-sm table-sub whitespace-nowrap text-center">{{ $x->sks ?? '-' }} SKS</td>
                <td class="text-xs sm:text-sm table-sub whitespace-nowrap text-center">{{ $x->sks_text ?? '-' }}</td>
                <td class="text-xs sm:text-sm table-main table-border-r text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @include('livewire.global.table.badge.wajib-badge', [
                                'xValue' => $x->wajib_text,
                                'sortir' => $x->wajib,
                            ])
                        </button>
                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString,
                            'copyName' => 'Kode MK',
                            'copyText' => $x->kode_mk ?? '',
                        ])
                    </flux:dropdown>
                </td>

                <td class="text-xs sm:text-sm table-second table-border-l whitespace-nowrap text-center">
                    {{-- {{ $x->cpmks_count . ' CPMK' ?? '-' }} --}}
                    {{ $x->count_cpmk . ' CPMK' ?? '-' }}
                </td>
                <td class="text-xs sm:text-sm table-second whitespace-nowrap text-center">

                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @if ($x->count_scpmk >= 14 && $x->count_scpmk <= 16)
                                <flux:badge color="green" size="sm">
                                    {{ $x->count_scpmk }} Sub-CPMK
                                </flux:badge>
                            @elseif ($x->count_scpmk >= 7 && $x->count_scpmk < 14)
                                <flux:badge color="yellow" size="sm">
                                    {{ $x->count_scpmk }} Sub-CPMK
                                </flux:badge>
                            @elseif ($x->count_scpmk >= 4 && $x->count_scpmk < 7)
                                <flux:badge color="orange" size="sm">
                                    {{ $x->count_scpmk }} Sub-CPMK
                                </flux:badge>
                            @else
                                <flux:badge color="red" size="sm">
                                    {{ $x->count_scpmk ?? 0 }} Sub-CPMK
                                </flux:badge>
                            @endif
                        </button>

                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString,
                        ])
                    </flux:dropdown>
                </td>
                <td class="text-xs sm:text-sm table-second table-border-r text-center">

                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @if ($x->total_bobot >= 70 && $x->total_bobot < 200)
                                <flux:badge icon="check-circle" color="green" size="sm">
                                    {{ $x->total_bobot }}%
                                </flux:badge>
                            @elseif ($x->total_bobot >= 200)
                                <flux:badge icon="exclamation-triangle" color="blue" size="sm">
                                    {{ $x->total_bobot }}%
                                </flux:badge>
                            @elseif ($x->total_bobot > 20 && $x->total_bobot < 70)
                                <flux:badge icon="clock" color="orange" size="sm">
                                    {{ $x->total_bobot }}%
                                </flux:badge>
                            @else
                                <flux:badge icon="no-symbol" color="red" size="sm">
                                    {{ $x->total_bobot ?? 0 }}%
                                </flux:badge>
                            @endif
                        </button>

                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString,
                        ])
                    </flux:dropdown>

                </td>

                <td class="text-xs sm:text-sm table-main text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @if ($x->draf == 0)
                                <flux:badge color="green" size="sm">
                                    Aktif
                                </flux:badge>
                            @else
                                <flux:badge color="red" size="sm">
                                    Draf
                                </flux:badge>
                            @endif
                        </button>

                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString,
                        ])
                    </flux:dropdown>
                </td>
                <td class="text-xs sm:text-sm table-second whitespace-nowrap">{{ $x->revisi_day ?? '-' }}</td>
            @endif

            @if ($switchTable === 'cpmk')
                <td class="text-xs sm:text-sm table-second min-w-84 text-justify leading-relaxed [hyphens:auto]">
                    {{ $x->deskripsi_cpl ?? '-' }}</td>
            @endif

            @if ($switchTable === 'cpl' || $switchTable === 'sub-cpmk')
                <td class="text-xs sm:text-sm table-second min-w-84 text-justify leading-relaxed [hyphens:auto]">
                    {{ $x->deskripsi ?? '-' }}</td>
            @endif

            @if (($withCapaian ?? null) && $switchTable === 'cpl')
                <td class="text-xs sm:text-sm table-second table-border-l whitespace-nowrap text-center">
                    {{ $x->rekap_cpl_pr ?? '0.00' }}</td>
                <td class="text-xs sm:text-sm table-second whitespace-nowrap text-center">
                    {{ $x->index_cpl_pr ?? '0.00' }}</td>
                <td class="text-xs sm:text-sm table-sub table-border-l whitespace-nowrap text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @include('livewire.global.table.badge.nilai-mutu-badge', [
                                'xValue' => $x->mutu_cpl_pr ?? 'E',
                            ])
                        </button>
                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString,
                        ])
                    </flux:dropdown>
                </td>

                <td class="text-xs sm:text-sm table-second table-border-x">
                    <x-button-action color="emerald"
                        href="{{ route('rps-capaian-management', [
                            'kode_cpl' => $x->kode,
                            'kode_pr' => $kode_pr_url,
                        ]) }}"
                        wire:navigate>
                        <flux:icon name="document-text" class="w-3.5 h-3.5" />
                        RPS
                    </x-button-action>
                </td>
                <td class="text-xs sm:text-sm table-sub whitespace-nowrap text-center">
                    {{ $x->count_rps_pr ?? '-' }} RPS</td>
                <td class="text-xs sm:text-sm table-sub whitespace-nowrap text-center">
                    {{ $x->count_rps ?? '-' }} RPS</td>
            @elseif ($switchTable === 'cpl')
                <td class="text-xs sm:text-sm table-second table-border-l whitespace-nowrap text-center">
                    {{ $x->count_rps ?? '-' }} RPS</td>
            @endif

            @if ($switchTable === 'cpmk')
                <td class="text-xs sm:text-sm table-second table-border-x whitespace-nowrap text-center">
                    {{ $x->count_cpl ?? '-' }} CPL</td>
                <td class="text-xs sm:text-sm table-second whitespace-nowrap text-center">
                    {{ $x->count_scpmk . ' Sub-CPMK' ?? '-' }}</td>
                <td class="text-xs sm:text-sm table-second text-center">{{ $x->total_bobot ? $x->total_bobot . '%' : '-' }}</td>
            @endif

            @if ($switchTable === 'sub-cpmk')
                <td class="text-xs sm:text-sm table-main text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @include('livewire.global.table.badge.metode-badge', [
                                'xValue' => $x->metode,
                            ])
                        </button>

                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString,
                        ])
                    </flux:dropdown>

                <td class="text-xs sm:text-sm table-second min-w-48">{{ $x->materi ?? '-' }}</td>
                <td class="text-xs sm:text-sm table-second min-w-48">{{ $x->metodologi ?? '-' }}</td>
                <td class="text-xs sm:text-sm table-second min-w-48">{{ $x->indikator ?? '-' }}</td>
                </td>

                <td class="text-xs sm:text-sm table-main text-center">{{ $x->bobot_format ? $x->bobot_format . '%' : '-' }}
                </td>
                <td class="text-xs sm:text-sm table-second min-w-48">{{ $x->tugas ?? '-' }}</td>
                <td class="text-xs sm:text-sm table-second whitespace-nowrap text-center">
                    {{ $x->waktu_tugas ? $x->w_tugas . ' menit' : '60 m/SKS' }}</td>
                <td class="text-xs sm:text-sm table-second whitespace-nowrap text-center">
                    {{ $x->waktu_mandiri ? $x->w_mandiri . ' menit' : '60 m/SKS' }}</td>
            @endif

            @if ($switchTable === 'referensi')
                <td class="text-xs sm:text-sm table-second min-w-84">{{ $x->judul ?? '-' }}</td>
                <td class="text-xs sm:text-sm table-second min-w-48">{{ $x->penulis ?? '-' }}</td>
                <td class="text-xs sm:text-sm table-second min-w-48">{{ $x->penerbit ?? '-' }}</td>
                <td class="text-xs sm:text-sm table-main text-center">{{ $x->tahun ?? '-' }}</td>
                <td class="text-xs sm:text-sm table-second min-w-48">
                    @if ($x->link)
                        <a href="{{ $x->link }}" target="_blank"
                            class="flex items-center gap-1 hover:underline active:underline text-xs font-bold text-blue-600 dark:text-blue-400">
                            <flux:icon.link variant="micro" /> <span>{{ $x->link ?? '-' }}</span>
                        </a>
                    @else
                        -
                    @endif
                    </template>

                </td>
            @endif

            @if ($switchTable !== 'dosen')
                <td class="text-xs sm:text-sm table-main text-center">
                    <flux:dropdown>
                        <flux:button class="cursor-pointer" variant="ghost" size="sm"
                            icon="ellipsis-horizontal" inset="top bottom">
                        </flux:button>

                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString,
                        ])

                    </flux:dropdown>
                </td>
            @endif


            <td class="text-xs sm:text-sm table-second whitespace-nowrap text-center">{{ $x->created_day ?? '-' }}</td>
            <td class="text-xs sm:text-sm table-second whitespace-nowrap text-center">{{ $x->updated_day ?? '-' }}</td>
        </tr>
        @empty
            <tr>
                <td colspan="{{ match ($switchTable) {
                    'rps' => 17,
                    'cpl' => $withCapaian ?? null ? 12 : 7,
                    'cpmk' => 9,
                    'sub-cpmk' => 14,
                    'referensi' => 10,
                    default => 9,
                } }}"
                    class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada data {{ $xNameString }} ditemukan!
                </td>
            </tr>
        @endforelse

        </x-admin.global.table.main-layout-table>
