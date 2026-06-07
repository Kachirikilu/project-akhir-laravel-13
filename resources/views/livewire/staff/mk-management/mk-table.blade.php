<x-global.main-layout-table :paginator="$mks">

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

    <x-slot:sortir>

        <div x-data="{ activeTab: @entangle('filterMKgg') }"
            class="scrollbar-thin flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto">
            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByMKgg',
                'xFilter' => 'filterMKgg',
                'tabFilter' => $totalGanjil + $totalGenap,
                'tabString' => '',
                'tabNameString' => 'Semua',
                'icon' => 'table-cells',
            ])

            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByMKgg',
                'xFilter' => 'filterMKgg',
                'tabFilter' => $totalGanjil,
                'tabString' => 'mk-ganjil',
                'tabNameString' => 'Ganjil',
                'icon' => 'calendar-days',
            ])

            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByMKgg',
                'xFilter' => 'filterMKgg',
                'tabFilter' => $totalGenap,
                'tabString' => 'mk-genap',
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
                'sortFieldString' => 'no_mk',
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
                        @include('livewire.global.table.badge.level-mk-badge', [
                            'xValue' => $mk->digit_mk,
                            'sortir' => $mk->level_mk,
                        ])
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
                        @include('livewire.global.table.badge.semester-badge', [
                            'xValue' => $mk->kode,
                            'sortir' => $mk->semester,
                        ])
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
                        @include('livewire.global.table.badge.wajib-badge', [
                            'xValue' => $mk->wajib_text,
                            'sortir' => $mk->wajib,
                        ])
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

    </x-admin.global.table.main-layout-table>
