<x-global.main-layout-table :paginator="$users" :onlyAdmin="!Auth::user()->admin">

    <x-slot:header>
        <tr>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode',
                'headString' => $switchTable == 'dosen' ? 'NIP / NIDN' : 'NIM',
                'rowSpan' => 2,
                'isMain' => 1,
                'isCenter' => 1,
                'isSticky' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'name',
                'headString' => 'Nama',
                'rowSpan' => 2,
                'isMain' => 1,
            ])

            <th colspan="3" class="table-head-sub">
                Rencana Pembelajaran Semester
            </th>


            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'status',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'program_studi',
                'rowSpan' => 2,
            ])
            <th rowspan="2" class="table-head border-x">Aksi</th>

        </tr>

        <tr>
            <th class="table-head border-x">Show</th>
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'count_rps',
                'headString' => 'Total RPS',
                'isCenter' => 1,
                'isBorderL' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'total_sks',
                'isCenter' => 1,
                'isBorderR' => 1,
            ])
        </tr>
    </x-slot:header>


    @forelse($users as $user)
        @php
            $detail = $user->admin ?? ($user->dosen ?? $user->mahasiswa);
        @endphp

        <tr wire:key="user-{{ $user->id }}" data-user-id="{{ $user->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

            {{-- Role --}}
            <td class="table-main-sticky whitespace-nowrap text-center">{{ $user->identity1 ?? '-' }} /
                {{ $user->identity2 ?? '-' }}</td>

            <td class="table-second whitespace-nowrap">
                {{ $user->name ?? '-' }}</td>

            <td class="table-second table-border-x text-center">
                @if (!$user->trashed())
                    <x-button-action
                        @click="
                            $store.user?.reset();

                            const type = '{{ strtolower($user->role) }}';

                            $store.user?.setType(type);
                            $store.user?.setEdit(1);

                            const colors = {
                                dosen: 'text-lime-700 dark:text-lime-400',
                                mahasiswa: 'text-cyan-700 dark:text-cyan-400',
                            };
                            const colors2 = {
                                dosen: 'bg-lime-50 dark:bg-lime-950/40',
                                mahasiswa: 'bg-cyan-50 dark:bg-cyan-950/40',
                            };
                            $store.user?.setColor(colors[type] ?? 'text-gray-700 dark:text-gray-400', colors2[type] ?? 'bg-gray-50 dark:bg-gray-950/40');

                                $store.user?.setValueUserRPS (
                                    '{{ $user->name ?? '' }}',
                                    '{{ $user->dosen->nip ?? '' }}',
                                    '{{ $user->mahasiswa->nim ?? '' }}',
                                    '{{ $user->mahasiswa->angkatan ?? '' }}',
                                    '{{ $user->mahasiswa->count_rps ?? ($user->count_rps ?? 0) }}',
                                    '{{ $user->mahasiswa->total_sks ?? ($user->total_sks ?? 0) }}',

                                    '{{ $user->mahasiswa->rekap_mhs ?? '0.00' }}',
                                    '{{ $user->mahasiswa->ipk_mhs ?? '0.00' }}',
                                    '{{ $user->mahasiswa->mutu_mhs ?? 'E' }}',
                                    '{{ $user->pr_id ?? '' }}',
                                );
                    
                            $flux.modal('user-rps-modal').show();
                            $dispatch('open-edit-user-modal', { id: {{ $user->id }}, withRPS: 1, isRPS: 1 });
                        "
                        color="emerald"
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
            <td class="table-sub text-center">{{ $user->mahasiswa->count_rps ?? $user->count_rps }} RPS</td>
            <td class="table-sub table-border-r text-center">{{ $user->mahasiswa->total_sks ?? $user->total_sks }}
                SKS</td>

            <td class="table-second text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.status-user-badge', [
                            'xValue' => $user->status,
                        ])
                    </button>
                    <flux:menu
                        class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
                        <livewire:staff.obe-management.dosen-management.toolbar-dosen-management lazy :id="$user->id"
                            :email="$user->email" :label_id1="$user->label_id1" :identity1="$user->identity1"
                            :role="$user->role" :count_rps="$user->count_rps" :total_sks="$user->total_sks" :isTrashed="$user->trashed()" wire:key="toolbar-dosen-{{ $user->id }}-1" />
                    </flux:menu>
                </flux:dropdown>
            </td>

            <td class="table-second min-w-48">
                {{ $user->prodi ?? '-' }} ({{ $user->kode_pr ?? '---' }})</td>

            <td class="table-main text-center table-border-x">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    <flux:menu
                        class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">
                        <livewire:staff.obe-management.dosen-management.toolbar-dosen-management lazy :id="$user->id"
                            :email="$user->email" :label_id1="$user->label_id1" :identity1="$user->identity1"
                            :role="$user->role" :count_rps="$user->count_rps" :total_sks="$user->total_sks" :isTrashed="$user->trashed()" wire:key="toolbar-dosen-{{ $user->id }}-2" />
                    </flux:menu>

                </flux:dropdown>
            </td>
        </tr>

    @empty
        <tr>
            <td colspan="10" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Dosen ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
