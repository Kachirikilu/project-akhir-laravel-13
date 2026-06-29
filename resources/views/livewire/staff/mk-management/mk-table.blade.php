<x-global.main-layout-table :paginator="$mks">

    <x-slot:sortir>

        <div x-data="{ activeTab: @entangle('filterMKgg') }"
            class="pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto">
            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByMKgg',
                'xFilter' => 'filterMKgg',
                'tabFilter' => $totalGanjilMK + $totalGenapMK,
                'tabString' => '',
                'tabNameString' => 'Semua',
                'icon' => 'table-cells',
            ])

            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByMKgg',
                'xFilter' => 'filterMKgg',
                'tabFilter' => $totalGanjilMK,
                'tabString' => 'mk-ganjil',
                'tabNameString' => 'Ganjil',
                'icon' => 'calendar-days',
            ])

            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByMKgg',
                'xFilter' => 'filterMKgg',
                'tabFilter' => $totalGenapMK,
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
                'isSticky' => 1,
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
            <th colspan="{{ $switchTable == '' ? 5 : 2 }}" class="table-head-sub table-border-l">
                Bobot Mata Kuliah (SKS)
            </th>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'wajib',
                'rowSpan' => 2,
                'isCenter' => 1,
                'isBorderL' => 1,
            ])

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
                ])
            @endif
            @if ($switchTable == 'praktikum' || $switchTable == '')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'sks_pr',
                    'headString' => 'Praktikum',
                    // 'isSubHeader' => 1,
                    'isCenter' => 1,
                ])
            @endif
            @if ($switchTable == 'praktek-lapangan' || $switchTable == '')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'sks_pl',
                    'headString' => 'Praktek Lapangan',
                    // 'isSubHeader' => 1,
                    'isCenter' => 1,
                ])
            @endif
            @if ($switchTable == 'simulasi' || $switchTable == '')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'sks_sm',
                    'headString' => 'Simulasi',
                    // 'isSubHeader' => 1,
                    'isCenter' => 1,
                ])
            @endif
        </tr>
    </x-slot:header>


    @forelse($mks as $mk)
        <tr wire:key="mk-{{ $mk->id }}" data-mk-id="{{ $mk->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

            <td class="table-second text-center">{{ $mk->id }}</td>
            <td class="table-second text-center">
                <flux:dropdown>
                    <button class="cursor-pointer" wire:click="$dispatch('trigger-mk-modal')">
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

            <td class="table-main-sticky text-center">
                <flux:dropdown>
                    <button class="cursor-pointer" wire:click="$dispatch('trigger-mk-modal')">
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

            <td class="table-second min-w-84">{{ $mk->mk ?? '-' }}</td>
            <td class="table-second text-center">{{ $mk->semester ?? '-' }}</td>

            <td class="table-main text-center table-border-x">{{ $mk->sks ?? '-' }}</td>

            @if ($switchTable == 'tatap-muka' || $switchTable == '')
                <td class="table-sub text-center">{{ $mk->sks_tm ?? '-' }}</td>
            @endif

            @if ($switchTable == 'praktikum' || $switchTable == '')
                <td class="table-sub text-center">
                    {{ $mk->sks_pr ?? '-' }}</td>
            @endif

            @if ($switchTable == 'praktek-lapangan' || $switchTable == '')
                <td class="table-sub text-center">
                    {{ $mk->sks_pl ?? '-' }}</td>
            @endif

            @if ($switchTable == 'simulasi' || $switchTable == '')
                <td class="table-sub text-center">
                    {{ $mk->sks_sm ?? '-' }}</td>
            @endif

            <td class="table-second table-border-l text-center">
                <flux:dropdown>
                    <button class="cursor-pointer" wire:click="$dispatch('trigger-mk-modal')">
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

            <td class="table-main text-center table-border-x">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" wire:click="$dispatch('trigger-mk-modal')" variant="ghost" size="sm" icon="ellipsis-horizontal"
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

            <td class="table-second whitespace-nowrap text-center">{{ $mk->created_day ?? '-' }}</td>
            <td class="table-second whitespace-nowrap text-center">{{ $mk->updated_day ?? '-' }}</td>
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
