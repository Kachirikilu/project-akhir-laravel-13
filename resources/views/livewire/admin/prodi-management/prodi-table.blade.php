<x-global.main-layout-table :paginator="$xResults" :onlyAdmin="!Auth::user()->admin">

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


            @php
                if ($this->switchTable === 'fakultas') {
                    $mainHead = 'fakultas';
                } elseif ($this->switchTable === 'departemen') {
                    $mainHead = 'departemen';
                } else {
                    $mainHead = 'program_studi';
                }
            @endphp

            @include('livewire.global.table.head-table', [
                'sortFieldString' => $mainHead,
                'rowSpan' => 2,
            ])

            @if ($switchTable === 'prodi')
                <th colspan="4" class="table-head-sub">
                    Nilai Capaian
                </th>
            @endif

            @if ($switchTable === 'prodi')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'departemen',
                    'rowSpan' => 2,
                ])
            @endif

            @if ($switchTable === 'departemen')
                <th colspan="3" class="table-head-sub">
                    Nilai Capaian
                </th>
            @endif

            @if ($switchTable !== 'fakultas')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'fakultas',
                    'rowSpan' => 2,
                ])
            @endif

            @if ($switchTable === 'fakultas')
                <th colspan="3" class="table-head-sub">
                    Nilai Capaian
                </th>
            @endif

            @if ($switchTable === 'prodi')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'strata',
                    'isCenter' => 1,
                    'isMain' => 1,
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

        <tr>
            @if ($switchTable === 'prodi')
                <th class="table-head border-x whitespace-nowrap">Show</th>
            @endif

            @php
                $rekap = match ($this->switchTable) {
                    'fakultas' => 'fk',
                    'departemen' => 'dp',
                    default => 'pr',
                };
            @endphp
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'rekap_' . $rekap,
                'headString' => 'Nilai',
                'isCenter' => 1,
                'isBorderL' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'index_' . $rekap,
                'headString' => 'Index',
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'akreditas_' . $rekap,
                'headString' => 'Akreditas',
                'isCenter' => 1,
                'isMain' => 1,
            ])

        </tr>
    </x-slot:header>


    @forelse($xResults as $x)
        <tr wire:key="{{ $switchTable }}-{{ $x->id }}" data-{{ $switchTable }}-id="{{ $x->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="table-second text-center">{{ $x->id }}</td>

            <td class="table-main text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @switch($x->tingkatan_prodi)
                            @case(1)
                                <flux:badge icon="academic-cap" color="emerald" size="sm">
                                    {{ $x->kode ?? '---' }}
                                </flux:badge>
                            @break

                            @case(2)
                                <flux:badge icon="book-open" color="amber" size="sm">
                                    {{ $x->kode ?? '---' }}
                                </flux:badge>
                            @break

                            @case(3)
                                <flux:badge icon="building-library" color="indigo" size="sm">
                                    {{ $x->kode ?? '---' }}
                                </flux:badge>
                            @break

                            @default
                                <flux:badge icon="globe-alt" color="red" size="sm">
                                    {{ $x->kode ?? '---' }}
                                </flux:badge>
                        @endswitch
                    </button>

                    @include('livewire.admin.prodi-management.prodi-toolbar-table', [
                        'x' => $x,
                        'typeXString' => $switchTable,
                        'nameXString' => $xNameString,
                    ])
                </flux:dropdown>
            </td>

            <td class="table-second table-border-r whitespace-nowrap">
                {{ $x->prodi ?? ($x->departemen_dp ?? ($x->fakultas_fk ?? '-')) }}</td>

            @if ($switchTable === 'prodi')
                <td class="table-second table-border-r">
                    <x-button-action color="blue"
                        href="{{ route('capaian-management', [
                            'kode_pr' => $x->kode,
                        ]) }}"
                        wire:navigate>
                        <flux:icon name="document-text" class="w-3.5 h-3.5" />
                        CPL
                    </x-button-action>
                </td>
            @endif


            @php
                if ($this->switchTable === 'fakultas') {
                    $rekap_x = $x->rekap_fk;
                    $index_x = $x->index_fk;
                    $akreditas_x = $x->akreditas_fk;
                } elseif ($this->switchTable === 'departemen') {
                    $rekap_x = $x->rekap_dp;
                    $index_x = $x->index_dp;
                    $akreditas_x = $x->akreditas_dp;
                } else {
                    $rekap_x = $x->rekap_pr;
                    $index_x = $x->index_pr;
                    $akreditas_x = $x->akreditas_pr;
                }
            @endphp
            <td class="table-second table-border-l whitespace-nowrap text-center">
                {{ $rekap_x ?? '0.00' }}</td>
            <td class="table-second whitespace-nowrap text-center">
                {{ $index_x ?? '0.00' }}</td>
            <td class="table-sub table-border-x whitespace-nowrap text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.nilai-huruf-badge', [
                            'xValue' => $akreditas_x ?? 'E',
                        ])
                    </button>
                    @include('livewire.staff.obe-management.obe-toolbar-table', [
                        'x' => $x,
                        'typeXString' => $switchTable,
                        'nameXString' => $xNameString,
                    ])
                </flux:dropdown>
            </td>

            @if ($switchTable === 'prodi')
                <td class="table-second whitespace-nowrap">
                    {{ $x->departemen . ' (' . $x->kode_dp . ')' }}
                </td>
            @endif

            @if ($switchTable !== 'fakultas')
                <td class="table-second whitespace-nowrap">{{ $x->fakultas . ' (' . $x->kode_fk . ')' }}</td>
            @endif


            @if ($switchTable === 'prodi')
                <td class="table-second table-border-l text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @switch($x->strata)
                                @case('Sarjana')
                                    <flux:badge icon="academic-cap" color="sky" size="sm">Sarjana</flux:badge>
                                @break

                                @case('Magister')
                                    <flux:badge icon="building-library" color="emerald" size="sm">Magister
                                    </flux:badge>
                                @break

                                @case('Doktor')
                                    <flux:badge icon="light-bulb" color="amber" size="sm">Doktor</flux:badge>
                                @break

                                @default
                                    <flux:badge icon="academic-cap" size="sm">{{ $x->strata }}</flux:badge>
                            @endswitch
                        </button>

                        @include('livewire.admin.prodi-management.prodi-toolbar-table', [
                            'x' => $x,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString,
                        ])
                    </flux:dropdown>
                </td>
            @endif

            <td class="table-main text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.admin.prodi-management.prodi-toolbar-table', [
                        'x' => $x,
                        'typeXString' => $switchTable,
                        'nameXString' => $xNameString,
                    ])

                </flux:dropdown>
            </td>

            <td class="table-second whitespace-nowrap text-center">{{ $x->created_day ?? '-' }}</td>
            <td class="table-second whitespace-nowrap text-center">{{ $x->updated_day ?? '-' }}</td>
        </tr>
        @empty
            <tr>
                <td colspan="{{ match ($switchTable) {
                    'prodi' => 13,
                    'departemen' => 10,
                    'fakultas' => 9,
                    default => 13,
                } }}"
                    class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada data {{ $xNameString }} ditemukan!
                </td>
            </tr>
        @endforelse

        </x-admin.global.table.main-layout-table>
