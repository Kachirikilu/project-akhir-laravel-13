<x-global.main-layout-table :paginator="$cpl">

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
                'sortFieldString' => 'deskripsi',
                'rowSpan' => 2,
            ])

            @if ($withCapaian ?? null)
                <th colspan="3" class="table-head-sub">
                    Nilai Capaian
                </th>
            @endif
            <th colspan="{{ $withCapaian ?? null ? 3 : 2 }}" class="table-head-sub">
                Rencana Pembelajaran Semester
            </th>
            {{-- @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'count_rps',
                    'headString' => 'Total RPS',
                    'isBorderL' => 1,
                    'isCenter' => 1,
                ]) --}}

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
            @endif

            <th class="table-head text-center border-x">Show</th>
            @if ($withCapaian ?? null)
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'count_rps_pr',
                    'headString' => 'RPS ' . ($kode_pr_url ?? 'UNI'),
                    'isCenter' => 1,
                ])
            @endif
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'count_rps',
                'headString' => 'Total RPS',
                'isCenter' => 1,
            ])
        </tr>
    </x-slot:header>


    @forelse($cpl as $c)
        <tr wire:key="{{ $switchTable }}-{{ $c->id }}" data-{{ $switchTable }}-id="{{ $c->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

            <td class="table-second text-center">{{ $c->id }}</td>

            <td class="table-main-sticky text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        <flux:badge icon="beaker" color="sky" size="sm">{{ $c->kode ?? '---' }}
                        </flux:badge>
                    </button>

                    @include('livewire.staff.obe-management.obe-toolbar-table', [
                        'x' => $c,
                        'typeXString' => $switchTable,
                        'nameXString' => 'CPL',
                    ])
                </flux:dropdown>
            </td>


            <td class="table-second min-w-84 text-justify leading-relaxed [hyphens:auto]">
                {{ $c->deskripsi ?? '-' }}</td>

            @if ($withCapaian ?? null)
                <td class="table-second table-border-l whitespace-nowrap text-center">
                    {{ $c->rekap_cpl_pr ?? '0.00' }}</td>
                <td class="table-second whitespace-nowrap text-center">
                    {{ $c->index_cpl_pr ?? '0.00' }}</td>
                <td class="table-sub table-border-l whitespace-nowrap text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @include('livewire.global.table.badge.nilai-mutu-badge', [
                                'xValue' => $c->mutu_cpl_pr ?? 'E',
                            ])
                        </button>
                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $c,
                            'typeXString' => $switchTable,
                            'nameXString' => 'CPL',
                        ])
                    </flux:dropdown>
                </td>

                {{-- <td class="table-second table-border-x">
                    <x-button-action color="emerald"
                        href="{{ route('rps-capaian-management', [
                            'kode_cpl' => $c->kode,
                            'kode_pr' => $kode_pr_url,
                        ]) }}"
                        wire:navigate>
                        <flux:icon name="document-text" class="w-3.5 h-3.5" />
                        RPS
                    </x-button-action>
                </td> --}}
            @endif
            <td class="table-second table-border-x text-center">
                @if (!$c->trashed())
                    <x-button-action
                        @click="
                            $store.cpl?.reset();
                            const type = '{{ $c->level_cpl }}';
                            $store.cpl?.setEdit(1);
                            const colors = {
                                '1': 'text-emerald-700 dark:text-emerald-400',
                                '2': 'text-amber-700 dark:text-amber-400',
                                '3': 'text-indigo-700 dark:text-indigo-400',
                                '4': 'text-red-700 dark:text-red-400'
                            };
                            $store.cpl?.setColor(colors[type] ?? 'text-sky-700 dark:text-sky-400');
                                $store.cpl?.setValueCPLRPS (
                                    '{{ $c->kode ?? '' }}',
                                    '{{ $c->rekap_cpl_pr ?? 0 }}',
                                    '{{ $c->index_cpl_pr ?? 0 }}',
                                    '{{ $c->mutu_cpl_pr ?? 'E' }}',
                                );
                            $flux.modal('cpl-rps-modal').show();
                        "
                        wire:click="editCPL({{ $c->id }}, {{ $c->level_cpl }}, 1)" color="emerald"
                        wire:navigate>
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
            @if ($withCapaian ?? null)
                <td class="table-sub whitespace-nowrap text-center">
                    {{ $c->count_rps_pr ?? '-' }} RPS</td>
            @endif
            <td class="table-sub whitespace-nowrap text-center">
                {{ $c->count_rps ?? '-' }} RPS</td>
            <td class="table-main text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.staff.obe-management.obe-toolbar-table', [
                        'x' => $c,
                        'typeXString' => $switchTable,
                        'nameXString' => 'CPL',
                    ])

                </flux:dropdown>
            </td>

            @if (!($withCapaian ?? false))
                <td class="table-second whitespace-nowrap text-center">{{ $c->created_day ?? '-' }}</td>
                <td class="table-second whitespace-nowrap text-center">{{ $c->updated_day ?? '-' }}</td>
            @endif
        </tr>
    @empty
        <tr>
            <td colspan="{{ $withCapaian ?? null ? 10 : 8 }}"
                class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Capaian Pembelajaran Lulusan (CPL) ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
