<x-global.main-layout-table :paginator="$cpls">

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
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode',
                'rowSpan' => 2,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'deskripsi',
                'rowSpan' => 2,
            ])
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

    </x-slot:header>


    @forelse($cpls as $cpl)
        <tr wire:key="{{ $switchTable }}-{{ $cpl->id }}" data-{{ $switchTable }}-id="{{ $cpl->id }}"
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="{{ $secondKolom }} text-center">{{ $cpl->id }}</td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        <flux:badge icon="beaker" color="sky" size="sm">{{ $cpl->kode ?? '---' }}
                        </flux:badge>
                    </button>

                    {{-- @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $cpl,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString,
                        ]) --}}
                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} {{ $borderR }}">
                <x-button-action color="emerald"
                    href="{{ route('rps-capaian-management', [
                        'kode_cpl' => $cpl->kode,
                        'strata' => $strata_pr_url,
                        'kode_pr' => $kode_pr_url,
                    ]) }}"
                    wire:navigate>
                    <flux:icon name="document-text" class="w-3.5 h-3.5" />
                    RPS
                </x-button-action>
            </td>

            <td class="{{ $secondKolom }} min-w-84 text-justify leading-relaxed [hyphens:auto]">
                {{ $cpl->deskripsi ?? '-' }}</td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    {{-- @include('livewire.staff.obe-management.obe-toolbar-table', [
                            'x' => $cpl,
                            'typeXString' => $switchTable,
                            'nameXString' => $xNameString,
                        ]) --}}

                </flux:dropdown>
            </td>


            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $cpl->created_day ?? '-' }}</td>
            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $cpl->updated_day ?? '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data CPL ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
