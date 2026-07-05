<x-global.main-layout-table :paginator="$tim_dosens">

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
                'rowSpan' => 2,
                'isCenter' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'nama_tim',
                'isMain' => 1,
                'rowSpan' => 2,
                'isSticky' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'ketua_tim',
                'rowSpan' => 2,
            ])



            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'nip_ketua',
                'rowSpan' => 2,
                'isMain' => 1,
                'isBorderR' => 1,
                'isCenter' => 1,
            ])

            <th colspan="4" class="table-head-sub">
                Jumlah Anggota Tim
            </th>

            <th colspan="3" class="table-head-sub">
                Rencana Pembelajaran Semester
            </th>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'program_studi',
                'rowSpan' => 2,
            ])

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

        <tr class="bg-gray-50">
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'count_dosen',
                'headString' => 'Total',
                'isCenter' => 1,
                'isMain' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'count_koordinator',
                'headString' => 'Koordinator',
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'count_pengajar',
                'headString' => 'Pengajar',
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'count_asisten',
                'headString' => 'Asisten',
                'isCenter' => 1,
                'isBorderR' => 1,
            ])

            <th class="table-head border-x">Show</th>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'count_rps',
                'headString' => 'Total RPS',
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'total_sks',
                'headString' => 'Total SKS',
                'isCenter' => 1,
                'isBorderR' => 1,
            ])
        </tr>
    </x-slot:header>


    @forelse($tim_dosens as $d)
        <tr wire:key="{{ $switchTable }}-{{ $d->id }}" data-{{ $switchTable }}-id="{{ $d->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

            <td class="table-second text-center">{{ $d->id }}</td>

            <td class="table-main text-center">
                <flux:dropdown>
                    <button class="cursor-pointer" wire:click="$dispatch('trigger-tim-dosen-modal')">
                        <flux:badge icon="user-group" color="blue" size="sm">{{ $d->kode }}
                        </flux:badge>
                    </button>
                    @include('livewire.staff.obe-management.tim-dosen-management.tim-dosen-toolbar-table', ['key' => 1])
                </flux:dropdown>
            </td>
            <td class="table-second-sticky table-border-r whitespace-nowrap">{{ $d->tim ?? '-' }}</td>
            <td class="table-second table-border-r whitespace-nowrap">{{ $d->ketua ?? '-' }}</td>
            <td class="table-second table-border-r whitespace-nowrap">{{ $d->nip ?? '-' }}</td>

            <td class="table-second table-border-r text-center">{{ $d->count_dosen ?? '-' }}</td>
            <td class="table-sub text-center">{{ $d->count_koordinator ?? '-' }}</td>
            <td class="table-second text-center">{{ $d->count_pengajar ?? '-' }}</td>
            <td class="table-sub table-border-r text-center">{{ $d->count_asisten ?? '-' }}</td>

            <td class="table-second table-border-r text-center">

                @if (!$d->trashed())
                    <x-button-action
                        @click="
                            $store.tim_dosen?.reset();
                            $store.tim_dosen?.setEdit(1);
                            $store.tim_dosen?.setColor('text-blue-700 dark:text-blue-400');
                            $flux.modal('tim-dosen-rps-modal').show();
                            $store.tim_dosen?.setValueTimDosenRPS(
                                    '{{ $d->tim ?? '' }}',
                                    '{{ $d->ketua ?? '' }}',
                                    '{{ $d->nip ?? '' }}',
                                    '{{ $d->prodi ?? '' }}',

                                    '{{ $d->count_koordinator ?? '' }}',
                                    '{{ $d->count_pengajar ?? '' }}',
                                    '{{ $d->count_asisten ?? '' }}',

                                    '{{ $d->count_rps ?? '' }}',
                                    '{{ $d->total_sks ?? '' }}'
                                );
                            $dispatch('open-list-rps-tim-dosen-modal', { id: {{ $d->id }}, withRPS: 1, isRPS: 1 });
                        "
                        color="emerald" wire:navigate>
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
            <td class="table-sub text-center">{{ $d->count_rps ?? '-' }}</td>
            <td class="table-sub table-border-r text-center">{{ $d->total_sks ?? '-' }}</td>

            <td class="table-second min-w-48"">{{ $d->prodi ?? '-' }} ({{ $d->kode_pr ?? '---' }})</td>

            <td class="table-main text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" wire:click="$dispatch('trigger-tim-dosen-modal')" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>
                    @include('livewire.staff.obe-management.tim-dosen-management.tim-dosen-toolbar-table', ['key' => 2])
                </flux:dropdown>
            </td>


            <td class="table-second whitespace-nowrap text-center">{{ $d->created_day ?? '-' }}</td>
            <td class="table-second whitespace-nowrap text-center">{{ $d->updated_day ?? '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="16" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Tim Dosen ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
