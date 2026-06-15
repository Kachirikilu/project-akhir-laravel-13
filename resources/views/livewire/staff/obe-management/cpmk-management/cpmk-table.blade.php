<x-global.main-layout-table :paginator="$cpmk">

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

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'count_cpl',
                'headString' => 'Total CPL',
                'isMain' => 1,
                'isCenter' => 1,
                'rowSpan' => 2,
            ])
            @if (!($withCapaian ?? false))
                <th colspan="2" class="table-head-sub">
                    Sub-CPMK
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
                    'sortFieldString' => 'rekap_cpmk_pr',
                    'headString' => 'Nilai',
                    'isCenter' => 1,
                    'isBorderL' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'index_cpmk_pr',
                    'headString' => 'Index',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'akreditas_cpmk_pr',
                    'headString' => 'Akreditas',
                    'isCenter' => 1,
                    'isMain' => 1,
                ])
            @endif
            @if (!($withCapaian ?? false))
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'count_scpmk',
                    'headString' => 'Sub-CPMK',
                    'isCenter' => 1,
                ])
                @include('livewire.global.search-and-filters.table-search', [
                    'sortFieldString' => 'total_bobot',
                    'modelString' => 'searchBobotCPMK',
                    'resetXFilter' => 'resetInputBobotCPMK()',
                    'maxLength' => 2,
                    'withSimbol' => 1,
                    'wInput' => 20,
                    'placeholder' => 'Bobot',
                    'pTop' => 5,
                ])
            @endif
        </tr>
    </x-slot:header>


    @forelse($cpmk as $c)
        <tr wire:key="{{ $switchTable }}-{{ $c->id }}" data-{{ $switchTable }}-id="{{ $c->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="table-second text-center">{{ $c->id }}</td>

            <td class="table-main text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        <flux:badge icon="academic-cap" color="violet" size="sm">{{ $c->kode ?? '---' }}
                        </flux:badge>
                    </button>

                    @include('livewire.staff.obe-management.obe-toolbar-table', [
                        'x' => $c,
                        'typeXString' => $switchTable,
                        'nameXString' => 'CPMK',
                    ])
                </flux:dropdown>
            </td>


            <td class="table-second min-w-84 text-justify leading-relaxed [hyphens:auto]">
                {{ $c->deskripsi_cpl ?? '-' }}</td>


            @if ($withCapaian ?? null)
                <td class="table-second table-border-l whitespace-nowrap text-center">
                    {{ $c->rekap_cpmk_pr ?? '0.00' }}</td>
                <td class="table-second whitespace-nowrap text-center">
                    {{ $c->index_cpmk_pr ?? '0.00' }}</td>
                <td class="table-sub table-border-l whitespace-nowrap text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @include('livewire.global.table.badge.nilai-huruf-badge', [
                                'xValue' => $c->akreditas_cpmk_pr ?? 'E',
                            ])
                        </button>
                        @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $c,
                            'typeXString' => $switchTable,
                            'nameXString' => 'CPMK',
                        ])
                    </flux:dropdown>
                </td>
            @endif

            <td class="table-second table-border-x whitespace-nowrap text-center">
                {{ $c->count_cpl ?? '-' }} CPL</td>
            @if (!($withCapaian ?? false))
                <td class="table-sub whitespace-nowrap text-center">
                    {{ $c->count_scpmk . ' Sub-CPMK' ?? '-' }}</td>
                <td class="table-sub text-center">{{ $c->total_bobot ? $c->total_bobot . '%' : '-' }}</td>
            @endif
            <td class="table-main text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.staff.obe-management.obe-toolbar-table', [
                        'x' => $c,
                        'typeXString' => $switchTable,
                        'nameXString' => 'CPMK',
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
            <td colspan="{{ $withCapaian ?? null ? 8 : 9 }}"
                class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Capaian Pembelajaran Mata Kuliah (CPMK) ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
