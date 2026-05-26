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
        if ($switchTable !== '') {
            $borderR = 'border-[var(--border-table-color)] border-r';
            $isBorderRight = 1;
        } else {
            $borderR = '';
            $isBorderRight = 0;
        }
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
                'sortFieldString' => 'digit_mk',
                'rowSpan' => 2,
                'isCenter' => 1,
                'headString' => 'No',
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode',
                'rowSpan' => 2,
                'isCenter' => 1,
                'isMain' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'mk',
                'rowSpan' => 2,
                'headString' => 'Mata Kuliah',
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'semester',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])

            {{-- Group SKS (Lebar 5 kolom: Total SKS + 4 Tipe SKS) --}}
            <th colspan="{{ $switchTable == '' ? 5 : 2 }}" class="{{ $headSubKolom }}">
                Bobot Mata Kuliah (SKS)
            </th>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'wajib',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])

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
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'sks',
                'headString' => 'Total',
                // 'isSubHeader' => 1,
                'isCenter' => 1,
                'isMain' => 1,
            ])
            @if ($switchTable == 'tatap-muka' || $switchTable == '')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'sks_tm',
                    'headString' => 'Tatap Muka',
                    'isSubHeader' => 1,
                    'isCenter' => 1,
                    'isBorderR' => $isBorderRight,
                ])
            @endif
            @if ($switchTable == 'praktikum' || $switchTable == '')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'sks_pr',
                    'headString' => 'Praktikum',
                    // 'isSubHeader' => 1,
                    'isCenter' => 1,
                    'isBorderR' => $isBorderRight,
                ])
            @endif
            @if ($switchTable == 'praktek-lapangan' || $switchTable == '')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'sks_pl',
                    'headString' => 'Praktek Lapangan',
                    // 'isSubHeader' => 1,
                    'isCenter' => 1,
                    'isBorderR' => $isBorderRight,
                ])
            @endif
            @if ($switchTable == 'simulasi' || $switchTable == '')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'sks_sm',
                    'headString' => 'Simulasi',
                    // 'isSubHeader' => 1,
                    'isCenter' => 1,
                    'isBorderR' => 1,
                ])
            @endif
        </tr>
    </x-slot:header>


    @forelse($mks as $mk)
        <tr wire:key="mk-{{ $mk->id }}" data-mk-id="{{ $mk->id }}"
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="{{ $secondKolom }} text-center">{{ $mk->id }}</td>
            <td class="{{ $secondKolom }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @switch($mk->level_mk)
                            @case(1)
                                <flux:badge icon="academic-cap" color="emerald" size="sm">{{ $mk->digit_mk ?? '-' }}
                                </flux:badge>
                            @break

                            @case(2)
                                <flux:badge icon="book-open" color="amber" size="sm">{{ $mk->digit_mk ?? '-' }}
                                </flux:badge>
                            @break

                            @case(3)
                                <flux:badge icon="building-library" color="indigo" size="sm">{{ $mk->digit_mk ?? '-' }}
                                </flux:badge>
                            @break

                            @default
                                <flux:badge icon="globe-alt" color="red" size="sm">{{ $mk->digit_mk ?? '-' }}
                                </flux:badge>
                        @endswitch
                    </button>

                    @include('livewire.staff.mk-management.mk-toolbar-table', [
                        'x' => $mk,
                        'typeXString' => $mk->level_mk,
                        'editString' => 'editMK',
                        'nameXString' => 'Mata Kuliah',
                        'confirmDeleteString' => 'deleteMK',
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @switch($mk->semester)
                            {{-- Tahun 1: Biru/Cyan --}}
                            @case(1)
                                <flux:badge color="blue" size="sm">{{ $mk->kode ?? '---' }}</flux:badge>
                            @break

                            @case(2)
                                <flux:badge color="cyan" size="sm">{{ $mk->kode ?? '---' }}</flux:badge>
                            @break

                            {{-- Tahun 2: Hijau/Emerald --}}
                            @case(3)
                                <flux:badge color="green" size="sm">{{ $mk->kode ?? '---' }}</flux:badge>
                            @break

                            @case(4)
                                <flux:badge color="emerald" size="sm">{{ $mk->kode ?? '---' }}</flux:badge>
                            @break

                            {{-- Tahun 3: Kuning/Oranye --}}
                            @case(5)
                                <flux:badge color="yellow" size="sm">{{ $mk->kode ?? '---' }}</flux:badge>
                            @break

                            @case(6)
                                <flux:badge color="orange" size="sm">{{ $mk->kode ?? '---' }}</flux:badge>
                            @break

                            {{-- Tahun 4: Merah/Ungu (Fase Tugas Akhir) --}}
                            @case(7)
                                <flux:badge color="red" size="sm">{{ $mk->kode ?? '---' }}</flux:badge>
                            @break

                            @default
                                <flux:badge color="purple" size="sm">{{ $mk->kode ?? '---' }}</flux:badge>
                        @endswitch
                    </button>

                    @include('livewire.staff.mk-management.mk-toolbar-table', [
                        'x' => $mk,
                        'typeXString' => $mk->level_mk,
                        'editString' => 'editMK',
                        'nameXString' => 'Mata Kuliah',
                        'confirmDeleteString' => 'deleteMK',
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} min-w-84">{{ $mk->mk ?? '-' }}</td>
            <td class="{{ $secondKolom }} text-center">{{ $mk->semester ?? '-' }}</td>

            {{-- <td class="px-6 py-4 text-sm text-[var(--contrast-second-text)]">{{ $mk->sks ?? '-' }}</td> --}}
            <td class="{{ $mainKolom }} text-center">{{ $mk->sks ?? '-' }}</td>

            @if ($switchTable == 'tatap_muka' || $switchTable == '')
                <td class="{{ $subKolom }} {{ $borderR }} text-center">{{ $mk->sks_tm ?? '-' }}</td>
            @endif

            @if ($switchTable == 'praktikum' || $switchTable == '')
                <td class="{{ $subKolom }} {{ $borderR }} text-center">
                    {{ $mk->sks_pr ?? '-' }}</td>
            @endif

            @if ($switchTable == 'praktek_lapangan' || $switchTable == '')
                <td class="{{ $subKolom }} {{ $borderR }} text-center">
                    {{ $mk->sks_pl ?? '-' }}</td>
            @endif

            @if ($switchTable == 'simulasi' || $switchTable == '')
                <td class="{{ $subKolom }} border-r text-center">
                    {{ $mk->sks_sm ?? '-' }}</td>
            @endif

            <td class="{{ $secondKolom }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @if ($mk->wajib)
                            <flux:badge icon="check" color="green" size="sm" inset="top bottom">
                                {{ $mk->wajib_text }}
                            </flux:badge>
                        @else
                            <flux:badge icon="x-mark" color="zinc" size="sm" inset="top bottom">
                                {{ $mk->wajib_text }}
                            </flux:badge>
                        @endif
                    </button>

                    @include('livewire.staff.mk-management.mk-toolbar-table', [
                        'x' => $mk,
                        'typeXString' => $mk->level_mk,
                        'editString' => 'editMK',
                        'nameXString' => 'Mata Kuliah',
                        'confirmDeleteString' => 'deleteMK',
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.staff.mk-management.mk-toolbar-table', [
                        'x' => $mk,
                        'typeXString' => $mk->level_mk,
                        'editString' => 'editMK',
                        'nameXString' => 'Mata Kuliah',
                        'confirmDeleteString' => 'deleteMK',
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $mk->created_day ?? '-' }}</td>
            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $mk->updated_day ?? '-' }}</td>
        </tr>
        @empty
            <tr>
                <td colspan="{{ $switchTable == '' ? 14 : 11 }}"
                    class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada data Mata Kuliah ditemukan!
                </td>
            </tr>
        @endforelse


        <x-slot:footer>
            @include('livewire.global.table.footer-table', [
                'typeXString' => $mks,
            ])
        </x-slot:footer>

        </x-admin.global.table.main-layout-table>
