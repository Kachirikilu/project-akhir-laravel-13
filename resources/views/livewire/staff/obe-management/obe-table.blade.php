<x-global.main-layout-table>

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

                <th colspan="5" class="{{ $headSubKolom }}">
                    Mata Kuliah
                </th>
                <th colspan="3" class="{{ $headSubKolom }}">
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

            @if ($switchTable === 'cpmk' || $switchTable === 'scpmk' || $switchTable === 'cpl')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'deskripsi',
                    'rowSpan' => 2,
                ])
            @endif

            @if ($switchTable === 'cpmk')
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
                    'maxlength' => 2,
                    'floatOnly' => 1,
                    'wInput' => 15,
                    'placeholder' => 'Bobot',
                    'rowSpan' => 2,
                    'pTop' => 5,
                ])
            @endif
            @if ($switchTable === 'scpmk')
                <th colspan="4" class="{{ $headSubKolom }}">
                    Pembelajaran
                </th>
                <th colspan="4" class="{{ $headSubKolom }}">
                    Tugas
                </th>
            @endif
            @if ($switchTable === 'ref')
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

            <th rowspan="2" class="{{ $headKolom }} border-x">Aksi</th>

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
                ])

                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'sks',
                    'isCenter' => 1,
                ])

                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'sks_text',
                    'headString' => 'Pembelajaran',
                    'isCenter' => 1,
                    'isBorderR' => 1,
                ])

                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'is_wajib',
                    'headString' => 'Wajib',
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
                    'maxlength' => 3,
                    'floatOnly' => 1,
                    'wInput' => 15,
                    'placeholder' => 'Bobot',
                    'pTop' => 5,
                ])
            @endif

            @if ($switchTable === 'scpmk')

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
                    'maxlength' => 2,
                    'floatOnly' => 1,
                    'wInput' => 15,
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
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="{{ $secondKolom }} text-center">{{ $x->id }}</td>

            @if ($switchTable !== 'dosen')
                <td class="{{ $mainKolom }} text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @if ($switchTable === 'rps')
                                @switch($x->level_mk)
                                    @case(1)
                                        <flux:badge icon="academic-cap" color="emerald" size="sm">{{ $x->kode ?? '---' }}
                                        </flux:badge>
                                    @break

                                    @case(2)
                                        <flux:badge icon="book-open" color="amber" size="sm">{{ $x->kode ?? '---' }}
                                        </flux:badge>
                                    @break

                                    @case(3)
                                        <flux:badge icon="building-library" color="indigo" size="sm">
                                            {{ $x->kode ?? '---' }}
                                        </flux:badge>
                                    @break

                                    @default
                                        <flux:badge icon="globe-alt" color="red" size="sm">{{ $x->kode ?? '---' }}
                                        </flux:badge>
                                @endswitch
                            @else
                                @switch($switchTable)
                                    @case('cpmk')
                                        <flux:badge icon="academic-cap" color="sky" size="sm">{{ $x->kode ?? '---' }}
                                        </flux:badge>
                                    @break

                                    @case('scpmk')
                                        <flux:badge icon="academic-cap" color="fuchsia" size="sm">{{ $x->kode ?? '---' }}
                                        </flux:badge>
                                    @break

                                    @case('cpl')
                                        <flux:badge icon="beaker" color="lime" size="sm">{{ $x->kode ?? '---' }}
                                        </flux:badge>
                                    @break

                                    @default
                                        <flux:badge icon="book-open" color="violet" size="sm">{{ $x->kode ?? '---' }}
                                        </flux:badge>
                                @endswitch
                            @endif
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
                <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $x->akademik ?? '-' }}</td>
                <td class="{{ $secondKolom }} {{ $borderR }} {{ $borderL }} text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @switch($x->level_mk)
                                @case(1)
                                    <flux:badge color="emerald" size="sm">{{ $x->kode_mk ?? '---' }}
                                    </flux:badge>
                                @break

                                @case(2)
                                    <flux:badge color="amber" size="sm">{{ $x->kode_mk ?? '---' }}
                                    </flux:badge>
                                @break

                                @case(3)
                                    <flux:badge color="indigo" size="sm">{{ $x->kode_mk ?? '---' }}
                                    </flux:badge>
                                @break

                                @default
                                    <flux:badge color="red" size="sm">{{ $x->kode_mk ?? '---' }}
                                    </flux:badge>
                            @endswitch
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
                <td class="{{ $subKolom }} whitespace-nowrap">{{ $x->mk ?? '-' }}</td>
                <td class="{{ $subKolom }} whitespace-nowrap text-center">{{ $x->sks ?? '-' }} SKS</td>
                <td class="{{ $subKolom }} whitespace-nowrap text-center">{{ $x->sks_text ?? '-' }}</td>
                <td class="{{ $mainKolom }} {{ $borderR }} text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @if ($x->wajib)
                                <flux:badge icon="check" color="green" size="sm" inset="top bottom">
                                    {{ $x->wajib_text }}
                                </flux:badge>
                            @else
                                <flux:badge icon="x-mark" color="zinc" size="sm" inset="top bottom">
                                    {{ $x->wajib_text }}
                                </flux:badge>
                            @endif
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

                <td class="{{ $secondKolom }} {{ $borderL }} whitespace-nowrap text-center">
                    {{ $x->cpmks_count . ' CPMK' ?? '-' }}
                </td>
                <td class="{{ $secondKolom }} whitespace-nowrap text-center">

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
                <td class="{{ $secondKolom }} {{ $borderR }} text-center">

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

                <td class="{{ $mainKolom }} text-center">
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
                <td class="{{ $secondKolom }} whitespace-nowrap">{{ $x->revisi_day ?? '-' }}</td>
            @endif

            @if ($switchTable === 'cpmk')
                <td class="{{ $secondKolom }} min-w-84 text-justify leading-relaxed [hyphens:auto]">
                    {{ $x->deskripsi_cpl ?? '-' }}</td>
            @endif

            @if ($switchTable === 'scpmk' || $switchTable === 'cpl')
                <td class="{{ $secondKolom }} min-w-84 text-justify leading-relaxed [hyphens:auto]">
                    {{ $x->deskripsi ?? '-' }}</td>
            @endif

            @if ($switchTable === 'cpmk')
                <td class="{{ $secondKolom }} whitespace-nowrap text-center">
                    {{ $x->count_scpmk . ' Sub-CPMK' ?? '-' }}</td>
                <td class="{{ $secondKolom }} text-center">{{ $x->total_bobot ? $x->total_bobot . '%' : '-' }}</td>
            @endif

            @if ($switchTable === 'scpmk')
                <td class="{{ $mainKolom }} text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @switch($x->metode)
                                @case('Teori')
                                    <flux:badge icon="book-open" color="emerald" size="sm" variant="pill">Teori
                                    </flux:badge>
                                @break

                                @case('Praktik')
                                    <flux:badge icon="beaker" color="cyan" size="sm" variant="pill">Praktik
                                    </flux:badge>
                                @break

                                @case('Tugas')
                                    <flux:badge icon="pencil-square" color="blue" size="sm" variant="pill">Tugas
                                    </flux:badge>
                                @break

                                @case('UTS')
                                @case('UAS')
                                    <flux:badge icon="clipboard-document-check" color="amber" size="sm"
                                        variant="pill">
                                        {{ $x->metode }}</flux:badge>
                                @break

                                @case('Hasil Proyek')
                                    <flux:badge icon="light-bulb" color="indigo" size="sm" variant="pill">Hasil Proyek
                                    </flux:badge>
                                @break

                                @case('Kerja Praktek')
                                    <flux:badge icon="briefcase" color="violet" size="sm" variant="pill">Kerja Praktek
                                    </flux:badge>
                                @break

                                @case('Skripsi')
                                    <flux:badge icon="academic-cap" color="fuchsia" size="sm" variant="pill">Skripsi
                                    </flux:badge>
                                @break

                                @case('Aktivitas Partisipasif')
                                    <flux:badge icon="user-group" color="rose" size="sm" variant="pill">Partisipasif
                                    </flux:badge>
                                @break

                                @case('Mandiri')
                                    <flux:badge icon="user" color="slate" size="sm" variant="pill">Mandiri
                                    </flux:badge>
                                @break

                                @default
                                    <flux:badge icon="information-circle" color="zinc" size="sm" variant="pill">
                                        {{ $x->metode ?? '-' }}</flux:badge>
                            @endswitch
                        </button>

                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString,
                        ])
                    </flux:dropdown>

                    <td class="{{ $secondKolom }} min-w-48">{{ $x->materi ?? '-' }}</td>
                    <td class="{{ $secondKolom }} min-w-48">{{ $x->metodologi ?? '-' }}</td>
                    <td class="{{ $secondKolom }} min-w-48">{{ $x->indikator ?? '-' }}</td>
                </td>

                <td class="{{ $mainKolom }} text-center">{{ $x->bobot_format ? $x->bobot_format . '%' : '-' }}</td>
                <td class="{{ $secondKolom }} min-w-48">{{ $x->tugas ?? '-' }}</td>
                <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $x->w_tugas ?? '60 m/SKS' }}</td>
                <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $x->w_mandiri ?? '60 m/SKS' }}</td>
            @endif

            @if ($switchTable === 'ref')
                <td class="{{ $secondKolom }} min-w-48">{{ $x->judul ?? '-' }}</td>
                <td class="{{ $secondKolom }} min-w-48">{{ $x->penulis ?? '-' }}</td>
                <td class="{{ $secondKolom }} min-w-48">{{ $x->penerbit ?? '-' }}</td>
                <td class="{{ $mainKolom }} text-center">{{ $x->tahun ?? '-' }}</td>
                <td class="{{ $secondKolom }} min-w-48">
                    @if ($x->link)
                        <a href="{{ $x->link }}" target="_blank"
                            class="flex items-center gap-1 hover:underline text-xs font-bold text-blue-600 dark:text-blue-400">
                            <flux:icon.link variant="micro" /> <span>{{ $x->link ?? '-' }}</span>
                        </a>
                    @else
                        -
                    @endif
                    </template>

                </td>
            @endif

            @if ($switchTable !== 'dosen')
                <td class="{{ $mainKolom }} text-center">
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


            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $x->created_day ?? '-' }}</td>
            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $x->updated_day ?? '-' }}</td>
        </tr>
        @empty
            <tr>
                <td colspan="{{ match ($switchTable) {
                    'rps' => 16,
                    'cpmk' => 8,
                    'scpmk' => 14,
                    'cpl' => 6,
                    'ref' => 10,
                    default => 9,
                } }}"
                    class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada data {{ $xNameString }} ditemukan!
                </td>
            </tr>
        @endforelse


        <x-slot:footer>
            @include('livewire.global.table.footer-table', [
                'typeXString' => $xResults,
            ])
        </x-slot:footer>

        </x-admin.global.table.main-layout-table>
