<div wire:key="view-table-cpl">

    <x-global.main-layout-table :paginator="$cpl">

        @php
            $showMore = $showMore ?? false;
            $withCapaian = $withCapaian ?? false;
            $isNilaiMahasiswa = $isNilaiMahasiswa ?? false;
        @endphp

        @if ($isNilaiMahasiswa)
            <x-slot:leftSecHead>
                <div
                    class="w-full pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto shrink-0">

                    @include('livewire.global.table.detail-view-switch', [
                        'autoSmall' => 'lg',
                        'textTrue' => 'Detailed',
                        'textFalse' => 'Simple',
                    ])

                    @include('livewire.global.table.head-sortir', [
                        'sortFieldString' => 'kode',
                    ])
                    @include('livewire.global.table.head-sortir', [
                        'sortFieldString' => 'deskripsi',
                    ])
                    @include('livewire.global.table.head-sortir', [
                        'sortFieldString' => 'index_cpl_mhs',
                        'headString' => 'Index',
                    ])
                    @include('livewire.global.table.head-sortir', [
                        'sortFieldString' => 'count_rps',
                        'headString' => 'Jumlah RPS',
                    ])
                </div>
            </x-slot:leftSecHead>

            <x-slot:rightSecHead>
                <div class="w-full md:w-110 xl:w-124">
                    <div class="col-start-1 row-start-1 w-full flex items-center justify-between gap-4">
                        <div class="flex-shrink-0">
                            @include('livewire.global.search-and-filters.page-control', [
                                'perPageOptions' => [3, 5, 8, 10, 15, 25, 50],
                                // 'alpine' => 'sesi',
                                'key' => 'page-control-cpl-table',
                                'withB' => 0,
                                'isSmall' => 1,
                            ])
                        </div>

                        <div class="flex-grow max-w-md">
                            @include('livewire.global.search-and-filters.main-search', [
                                'placeholder' => 'Cari Capaian Mahasiswa berdasarkan CPL...',
                                'isLive' => 1,
                                'isBorder' => 2,
                            ])
                        </div>
                    </div>
                </div>
            </x-slot:rightSecHead>
        @else
            <x-slot:leftSecHead>
                <div class="w-full pb-1 flex flex-wrap items-center gap-2.5 w-full lg:w-auto lg:justify-end">
                    @include('livewire.global.table.head-sortir', [
                        'sortFieldString' => 'kode',
                        'headString' => 'Kode CPL',
                    ])
                    @include('livewire.global.table.head-sortir', [
                        'sortFieldString' => 'deskripsi',
                    ])
                    @if ($withCapaian)
                        @include('livewire.global.table.head-sortir', [
                            'sortFieldString' => 'index_cpl_pr',
                            'headString' => 'Index',
                        ])
                    @endif
                    @include('livewire.global.table.head-sortir', [
                        'sortFieldString' => 'count_rps',
                        'headString' => 'Jumlah RPS',
                    ])
                </div>
            </x-slot:leftSecHead>

            <x-slot:rightSecHead>
                @include('livewire.global.table.detail-view-switch')
            </x-slot:rightSecHead>
        @endif

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

                @if ($withCapaian)
                    <th colspan="3" class="table-head-sub">
                        Nilai Capaian
                    </th>
                @endif

                @if ($showMore)
                    <th colspan="{{ ($withCapaian && (Auth::user()->admin || Auth::user()->dosen)) ? 3 : 2 }}" class="table-head-sub">
                        Rencana Pembelajaran Semester
                    </th>
                @else
                    <th rowspan="2" class="table-head border-x">RPS</th>
                @endif

                <th rowspan="2" class="table-head border-x">Aksi</th>

                @if ($showMore)
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'created_at',
                        'isCenter' => 1,
                        'rowSpan' => 2,
                    ])
                @endif
                @if (!$withCapaian && $showMore)
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'updated_at',
                        'isCenter' => 1,
                        'rowSpan' => 2,
                    ])
                @endif

            </tr>

            <tr class="bg-gray-50">
                @if ($withCapaian)
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'rekap_cpl_' . ($isNilaiMahasiswa ? 'mhs' : 'pr'),
                        'headString' => 'Nilai',
                        'isCenter' => 1,
                        'isBorderL' => 1,
                    ])
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'index_cpl_' . ($isNilaiMahasiswa ? 'mhs' : 'pr'),
                        'headString' => 'Index',
                        'isCenter' => 1,
                    ])
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'huruf_cpl_' . ($isNilaiMahasiswa ? 'mhs' : 'pr'),
                        'headString' => 'Huruf',
                        'isCenter' => 1,
                        'isMain' => 1,
                    ])
                @endif

                @if ($showMore)
                    <th class="table-head text-center border-x">Show</th>
                @endif

                @if ($withCapaian && $showMore)
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'count_rps_' . ($isNilaiMahasiswa ? 'mhs' : 'pr'),
                        'headString' => 'RPS ' . ($kode_pr_url ?? 'Mahasiswa'),
                        'isCenter' => 1,
                    ])
                @endif

                @if ($showMore && (Auth::user()->admin || Auth::user()->dosen))
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'count_rps',
                        'headString' => 'Jumlah RPS',
                        'isCenter' => 1,
                    ])
                @endif
            </tr>
        </x-slot:header>


        @forelse($cpl as $c)
            <tr wire:key="{{ $switchTable }}-{{ $c->id }}" data-{{ $switchTable }}-id="{{ $c->id }}"
                class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

                <td class="table-second text-center">{{ $c->id }}</td>

                <td class="table-main-sticky text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer" wire:click="$dispatch('trigger-cpl-modal')">
                            <flux:badge icon="beaker" color="sky" size="sm">{{ $c->kode ?? '---' }}
                            </flux:badge>
                        </button>
                        @include(
                            'livewire.staff.obe-management.cpl-management.cpl-toolbar.cpl-toolbar-table',
                            [
                                'key' => 1,
                            ]
                        )
                    </flux:dropdown>
                </td>


                <td class="table-second min-w-84 text-justify leading-relaxed [hyphens:auto]">
                    {{ $c->deskripsi ?? '-' }}</td>

                @if ($withCapaian)
                    <td class="table-second table-border-l whitespace-nowrap text-center">
                        {{ $c->{'rekap_cpl_' . ($isNilaiMahasiswa ? 'mhs' : 'pr')} ?? '0.00' }}</td>
                    <td class="table-second whitespace-nowrap text-center">
                        {{ $c->{'index_cpl_' . ($isNilaiMahasiswa ? 'mhs' : 'pr')} ?? '0.00' }}</td>
                    <td class="table-sub table-border-l whitespace-nowrap text-center">
                        <flux:dropdown>
                            <button class="cursor-pointer" wire:click="$dispatch('trigger-cpl-modal')">
                                @include('livewire.global.table.badge.nilai-huruf-badge', [
                                    'xValue' => $c->{'huruf_cpl_' . ($isNilaiMahasiswa ? 'mhs' : 'pr')} ?? 'E',
                                ])
                            </button>
                            @include(
                                'livewire.staff.obe-management.cpl-management.cpl-toolbar.cpl-toolbar-table',
                                [
                                    'key' => 2,
                                ]
                            )
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
                            const type = '{{ $c->tingkat_cpl }}';
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
                                    '{{ $c->rekap_cpl_pr ?? $c->rekap_cpl_mhs ?? 0 }}',
                                    '{{ $c->index_cpl_pr ?? $c->index_cpl_mhs ?? 0 }}',
                                    '{{ $c->huruf_cpl_pr ?? $c->huruf_cpl_mhs ?? 'E' }}',
                                );
                            $flux.modal('cpl-rps-modal').show();
                            $dispatch('open-list-rps-cpl-modal', {
                                id: {{ (int) $c->id }},
                                tingkatan: {{ (int) $c->tingkat_cpl }},
                                isRPS: 1,
                                idInduk: {{ (int) ($this->pr_id_url ?? $this->mahasiswa_id_url ?? 0) }},
                                isNilaiMahasiswa: {{ ! empty($isNilaiMahasiswa) ? 'true' : 'false' }}
                            });
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

                @if ($withCapaian && $showMore)
                    <td class="table-sub whitespace-nowrap text-center">
                        {{ $c->count_rps_pr ?? $c->count_rps_mhs ?? '-' }} RPS</td>
                @endif

                @if ($showMore && (Auth::user()->admin || Auth::user()->dosen))
                    <td class="table-sub whitespace-nowrap text-center">
                        {{ $c->count_rps ?? '-' }} RPS</td>
                @endif
                <td class="table-main text-center">
                    <flux:dropdown>
                        <flux:button class="cursor-pointer" wire:click="$dispatch('trigger-cpl-modal')" variant="ghost"
                            size="sm" icon="ellipsis-horizontal" inset="top bottom">
                        </flux:button>
                        @include(
                            'livewire.staff.obe-management.cpl-management.cpl-toolbar.cpl-toolbar-table',
                            [
                                'key' => 3,
                            ]
                        )
                    </flux:dropdown>
                </td>

                @if ($showMore)
                    <td class="table-second whitespace-nowrap text-center">{{ $c->created_day ?? '-' }}</td>
                @endif
                @if (!$withCapaian && $showMore)
                    <td class="table-second whitespace-nowrap text-center">{{ $c->updated_day ?? '-' }}</td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="{{ $withCapaian ? 10 : 8 }}"
                    class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada data Capaian Pembelajaran Lulusan (CPL) ditemukan!
                </td>
            </tr>
        @endforelse

    </x-global.main-layout-table>
</div>
