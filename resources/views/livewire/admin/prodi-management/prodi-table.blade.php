<div wire:key="view-table-prodi">

    <x-global.main-layout-table :paginator="$xResults" :onlyAdmin="!Auth::user()->admin">

        @php
            $showMore = $showMore ?? false;
        @endphp

        @php
            $x_rekap = match ($this->switchTable) {
                'fakultas' => 'fk',
                'departemen' => 'dp',
                default => 'pr',
            };
        @endphp

        <x-slot:leftSecHead>
            <div class="w-full pb-1 flex flex-wrap items-center gap-2.5 w-full lg:w-auto lg:justify-end">
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'kode',
                ])
                @if ($switchTable === '' || $switchTable === 'prodi')
                    @include('livewire.global.table.head-sortir', [
                        'sortFieldString' => 'program_studi',
                    ])
                @endif

                @if ($switchTable !== 'fakultas')
                    @include('livewire.global.table.head-sortir', [
                        'sortFieldString' => 'departemen',
                    ])
                @endif
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'fakultas',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'akreditas_' . $x_rekap,
                    'headString' => 'Akreditasi',
                ])
            </div>
        </x-slot:leftSecHead>

        <x-slot:rightSecHead>
            @include('livewire.global.table.detail-view-switch')
        </x-slot:rightSecHead>
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

                @if ($showMore)
                    <th colspan="2" class="table-head-sub table-border-x">
                        Pimpinan
                        @if ($switchTable === '' || $switchTable === 'prodi')
                            Program Studi
                        @else
                            {{ ucfirst($switchTable) }}
                        @endif
                    </th>
                @endif

                @if ($switchTable === '' || $switchTable === 'prodi')
                    <th colspan="{{ $showMore ? 4 : 3 }}" class="table-head-sub table-border-x">
                        Nilai Capaian
                    </th>
                    @if ($showMore)
                        <th colspan="4" class="table-head-sub table-border-x">
                            Mata Kuliah & RPS
                        </th>
                    @endif
                @endif

                @if ($switchTable === '' || $switchTable === 'prodi')
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'departemen',
                        'rowSpan' => 2,
                    ])
                @endif

                @if ($switchTable === 'departemen')
                    <th colspan="{{ $showMore ? 3 : 2 }}" class="table-head-sub table-border-x">
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
                    <th colspan="{{ $showMore ? 3 : 2 }}" class="table-head-sub table-border-x">
                        Nilai Capaian
                    </th>
                @endif

                @if ($showMore)
                    @if ($switchTable === '' || $switchTable === 'prodi')
                        @include('livewire.global.table.head-table', [
                            'sortFieldString' => 'strata',
                            'isCenter' => 1,
                            'rowSpan' => 2,
                            'isBorderL' => 1,
                        ])
                    @endif
                @endif
                <th rowspan="2"
                    class="table-head {{ $switchTable === 'fakultas' ? 'table-border-r' : 'table-border-x' }}">
                    Aksi</th>

                @if ($showMore)
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'created_at',
                        'isCenter' => 1,
                        'rowSpan' => 2,
                    ])
                @endif
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'updated_at',
                    'isCenter' => 1,
                    'rowSpan' => 2,
                ])

            </tr>

            <tr>
                @if ($showMore)
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => match ($switchTable ?? '') {
                            'departemen' => 'kadep',
                            'fakultas' => 'dekan',
                            default => 'kaprodi',
                        },
                        'headString' => match ($switchTable ?? '') {
                            'fakultas' => 'Dekan',
                            default => 'Ketua',
                        },
                        'isCenter' => 1,
                        'isMain' => 1,
                    ])

                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => match ($switchTable ?? '') {
                            'departemen' => 'sekdep',
                            'fakultas' => 'wadek',
                            default => 'sekprodi',
                        },
                              'headString' => match ($switchTable ?? '') {
                            'fakultas' => 'Wakil Dekan',
                            default => 'Sekretaris',
                        },
                        'isCenter' => 1,
                        'isMain' => 1,
                    ])
                @endif
                @if ($switchTable === '' || $switchTable === 'prodi')
                    <th class="table-head table-border-l whitespace-nowrap">Show</th>
                @endif

                @if ($showMore)
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'rekap_' . $x_rekap,
                        'headString' => 'Nilai',
                        'isCenter' => 1,
                        'isBorderL' => 1,
                    ])
                @endif

                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'index_' . $x_rekap,
                    'headString' => 'Index',
                    'isCenter' => 1,
                    'isBorderL' => $showMore ? 0 : 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'akreditas_' . $x_rekap,
                    'headString' => 'Akreditasi',
                    'isCenter' => 1,
                    'isMain' => 1,
                ])

                @if ($showMore)
                    @if ($switchTable === '' || $switchTable === 'prodi')
                        @include('livewire.global.table.head-table', [
                            'sortFieldString' => 'count_mk',
                            'headString' => 'Mata Kuliah',
                            'isCenter' => 1,
                        ])
                        @include('livewire.global.table.head-table', [
                            'sortFieldString' => 'target_sks',
                            'isCenter' => 1,
                            'isBorderR' => 1,
                        ])
                        @include('livewire.global.table.head-table', [
                            'sortFieldString' => 'count_rps_aktif',
                            'headString' => 'RPS Aktif',
                            'isCenter' => 1,
                        ])
                        @include('livewire.global.table.head-table', [
                            'sortFieldString' => 'count_rps_draf',
                            'headString' => 'RPS Draf',
                            'isCenter' => 1,
                            'isBorderR' => 1,
                        ])
                    @endif
                @endif


            </tr>
        </x-slot:header>


        @forelse($xResults as $x)
            <tr wire:key="{{ $switchTable }}-{{ $x->id }}" data-{{ $switchTable }}-id="{{ $x->id }}"
                class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

                <td class="table-second text-center">{{ $x->id }}</td>

                <td class="table-main-sticky text-center">
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
                        @include('livewire.admin.prodi-management.prodi-toolbar-table', ['key' => 1])
                    </flux:dropdown>
                </td>

                <td class="table-second whitespace-nowrap">
                    {{ $x->prodi ?? ($x->departemen_dp ?? ($x->fakultas_fk ?? '-')) }}</td>


                @if ($showMore)
                    @php
                        $namaKetua = $x->nama_kaprodi ?? ($x->nama_kadep ?? ($x->nama_dekan ?? '-'));
                        $nipKetua = $x->nip_kaprodi ?? ($x->nip_kadep ?? $x->nip_dekan);

                        $namaSekretaris = $x->nama_sekprodi ?? ($x->nama_sekdep ?? ($x->nama_wadek ?? '-'));
                        $nipSekretaris = $x->nip_sekprodi ?? ($x->nip_sekdep ?? $x->nip_wadek);
                    @endphp

                    <td class="table-main table-border-l whitespace-nowrap">
                        {{ $namaKetua }}
                        @if ($nipKetua)
                            <br>
                            NIP. {{ $nipKetua }}
                        @endif
                    </td>

                    <td class="table-sub table-border-l whitespace-nowrap">
                        {{ $namaSekretaris }}
                        @if ($nipSekretaris)
                            <br>
                            NIP. {{ $nipSekretaris }}
                        @endif
                    </td>
                @endif
                @if ($switchTable === '' || $switchTable === 'prodi')
                    <td class="table-second table-border-l text-center">
                        @if (!$x->trashed())
                            <x-button-action color="blue"
                                href="{{ route('capaian-management', [
                                    'kode_pr' => $x->kode,
                                ]) }}"
                                wire:navigate>
                                <flux:icon name="document-text" class="w-3.5 h-3.5" />
                                CPL
                            </x-button-action>
                        @else
                            <code
                                class="font-mono text-xs bg-[var(--second-table-color)] px-1.5 py-0.5 rounded border table-border text-[var(--contrast-main-text)] italic">
                                unfound
                            </code>
                        @endif
                    </td>
                @endif




                @php
                    if ($this->switchTable === 'fakultas') {
                        $x_rekap_x = $x->rekap_fk;
                        $index_x = $x->index_fk;
                        $akreditas_x = $x->akreditas_fk;
                    } elseif ($this->switchTable === 'departemen') {
                        $x_rekap_x = $x->rekap_dp;
                        $index_x = $x->index_dp;
                        $akreditas_x = $x->akreditas_dp;
                    } else {
                        $x_rekap_x = $x->rekap_pr;
                        $index_x = $x->index_pr;
                        $akreditas_x = $x->akreditas_pr;
                    }
                @endphp
                @if ($showMore)
                    <td class="table-second table-border-l whitespace-nowrap text-center">
                        {{ $x_rekap_x ?? '0.00' }}</td>
                @endif

                <td class="table-second {{ $showMore ? '' : 'table-border-l' }} whitespace-nowrap text-center">
                    {{ $index_x ?? '0.00' }}</td>
                <td class="table-sub table-border-x whitespace-nowrap text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @include('livewire.global.table.badge.nilai-mutu-badge', [
                                'xValue' => $akreditas_x ?? 'E',
                            ])
                        </button>
                        @include('livewire.admin.prodi-management.prodi-toolbar-table', ['key' => 2])
                    </flux:dropdown>
                </td>

                @if ($showMore)
                    @if ($switchTable === '' || $switchTable === 'prodi')
                        <td class="table-second whitespace-nowrap text-center">
                            {{ $x->count_mk ?? 0 }}</td>
                        <td class="table-sub table-border-r whitespace-nowrap text-center">
                            {{ $x->target_sks ?? 144 }}</td>
                        <td class="table-second whitespace-nowrap text-center">
                            {{ $x->count_rps_aktif ?? 0 }}</td>
                        <td class="table-sub table-border-r whitespace-nowrap text-center">
                            {{ $x->count_rps_draf ?? 0 }}</td>
                    @endif
                @endif

                @if ($switchTable === '' || $switchTable === 'prodi')
                    <td class="table-second whitespace-nowrap">
                        {{ $x->departemen . ' (' . $x->kode_dp . ')' }}
                    </td>
                @endif

                @if ($switchTable !== 'fakultas')
                    <td class="table-second whitespace-nowrap">{{ $x->fakultas . ' (' . $x->kode_fk . ')' }}</td>
                @endif

                @if ($showMore)
                    @if ($switchTable === '' || $switchTable === 'prodi')
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
                                    'key' => 3,
                                ])
                            </flux:dropdown>
                        </td>
                    @endif
                @endif

                <td
                    class="table-main text-center {{ $switchTable == 'fakultas' ? 'table-border-r' : 'table-border-x' }}">
                    <flux:dropdown>
                        <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                            inset="top bottom">
                        </flux:button>
                        @include('livewire.admin.prodi-management.prodi-toolbar-table', ['key' => 4])
                    </flux:dropdown>
                </td>

                @if ($showMore)
                    <td class="table-second whitespace-nowrap text-center">{{ $x->created_day ?? '-' }}</td>
                @endif
                <td class="table-second whitespace-nowrap text-center">{{ $x->updated_day ?? '-' }}</td>
            </tr>
            @empty
                <tr>
                    <td colspan="{{ match ($switchTable) {
                        'fakultas' => 11,
                        'departemen' => 12,
                        default => 19,
                    } }}"
                        class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                        Tidak ada data {{ $xNameString }} ditemukan!
                    </td>
                </tr>
            @endforelse

            </x-global.main-layout-table>
    </div>
