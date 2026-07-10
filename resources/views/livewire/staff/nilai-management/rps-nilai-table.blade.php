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
                'sortFieldString' => 'kurikulum',
                'headString' => 'Kurikulum',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])

            {{-- <th rowspan="2" class="table-head border-x">Show</th> --}}

            <th colspan="2" class="table-head-sub">
                Show
            </th>


            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'is_draf',
                'headString' => 'Status',
                'isMain' => 1,
                'isCenter' => 1,
                'rowSpan' => 2,
            ])



            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'revisi',
                'headString' => 'Tanggal Revisi',
                'rowSpan' => 2,
            ])

            <th rowspan="2" class="table-head border-x">Aksi</th>

        </tr>

        <tr class="bg-gray-50">
            <th class="table-head border-x">Capaian</th>
            <th class="table-head border-x">Detail</th>

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
                    @include('livewire.staff.obe-management.rps-management.rps-toolbar-table', [
                        'key' => 1,
                        'noData' => 1,
                    ])
                </flux:dropdown>
            </td>

            <td class="table-second whitespace-nowrap text-center">{{ $r->akademik ?? '-' }}</td>

            <td class="table-second table-border-x whitespace-nowrap text-center">
                @if (!$r->trashed())
                    <x-button-action color="blue"
                        href="{{ route('rps-capaian-mahasiswa-management', [
                            'kode_rps' => $r->kode ?? null,
                        ]) }}"
                        wire:navigate>
                        <flux:icon name="document-text" class="w-3.5 h-3.5" />
                        Nilai
                    </x-button-action>
                @else
                    <code
                        class="font-mono text-xs bg-[var(--second-table-color)] px-1.5 py-0.5 rounded border table-border text-[var(--contrast-main-text)] italic">
                        unfound
                    </code>
                @endif
            </td>

            <td class="table-second table-border-x text-center">
                @if (!$r->trashed())
                    <x-button-action
                        @click="
                            $store.rps?.resetShow();
                            $store.rps?.setShowRPS(
                                '{{ $r->id ?? '' }}',
                                '{{ $r->kode ?? '' }}',
                                '{{ $r->rps ?? '' }}',
                                '{{ $r->draf ?? '' }}',
                                '{{ $r->level_mk ?? '' }}',
                            );
                            $store.rps?.setColor('text-green-700 dark:text-green-400');
                            $flux.modal('rps-detail-modal').show();
                            $dispatch('open-show-rps-modal', { id: {{ $r->id }} });
                        "
                        color="emerald">
                        <flux:icon name="eye" class="w-3.5 h-3.5" />
                        <span>RPS</span>
                    </x-button-action>
                @else
                    <code
                        class="font-mono text-xs bg-[var(--second-table-color)] px-1.5 py-0.5 rounded border table-border text-[var(--contrast-main-text)] italic">
                        unfound
                    </code>
                @endif
            </td>

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
                    @include('livewire.staff.obe-management.rps-management.rps-toolbar-table', [
                        'key' => 2,
                        'noData' => 1,
                    ])
                </flux:dropdown>
            </td>
            <td class="table-second whitespace-nowrap">{{ $r->revisi_day ?? 'Tidak ada Revisi  ' }}</td>


            <td class="table-main text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>
                    @include('livewire.staff.obe-management.rps-management.rps-toolbar-table', [
                        'key' => 3,
                        'noData' => 1,
                    ])
                </flux:dropdown>
            </td>


        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Rencana Pembelajaran Semester (RPS) ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
