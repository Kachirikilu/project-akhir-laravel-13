<div wire:key="view-table-dosen">
    <x-global.main-layout-table :paginator="$users" :onlyAdmin="!Auth::user()->admin">

        @php
            $showMore = $showMore ?? false;
        @endphp
        <x-slot:leftSecHead>
            <div class="w-full pb-1 flex flex-wrap items-center gap-2.5 w-full lg:w-auto lg:justify-end">
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'kode',
                    'headString' => 'NIP',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'name',
                    'sortFieldString' => 'Nama',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'count_rps',
                    'sortFieldString' => 'Jumlah RPS',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'total_sks',
                    'sortFieldString' => 'Total SKS',
                ])
            </div>
        </x-slot:leftSecHead>

        <x-slot:rightSecHead>
            @include('livewire.global.table.detail-view-switch')
        </x-slot:rightSecHead>

        <x-slot:header>
            <tr>
                {{-- <th rowspan="2" class="table-head ">Role</th> --}}
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'role',
                    'rowSpan' => 2,
                    'isCenter' => 1,
                ])


                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'kode',
                    'headString' => $showMore ? 'NIP' : 'NIP / NIDN',
                    'rowSpan' => 2,
                    'isMain' => 1,
                    'isCenter' => 1,
                    'isSticky' => 1,
                ])

                @if ($showMore)
                    <th colspan="3" class="table-head-sub">
                        Identitas (ID)
                    </th>
                @endif



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
                @if ($showMore)
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'nidn',
                        // 'isMain' => 1,
                        'isCenter' => 1,
                    ])
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'nidk',
                        // 'isMain' => 1,
                        'isCenter' => 1,
                    ])
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'nik',
                        'isMain' => 1,
                        'isCenter' => 1,
                    ])
                @endif
                <th class="table-head border-x">Show</th>
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'count_rps',
                    'headString' => 'Jumlah RPS',
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
            <tr wire:key="dosen-rps-{{ $user->dosen->id }}" data-dosen-rps-id="{{ $user->dosen->id }}"
                class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

                <td class="table-second text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer" wire:click="$dispatch('trigger-user-modal')">
                            <flux:badge icon="briefcase" color="lime" size="sm">{{ $user->role_full }}
                            </flux:badge>
                        </button>
                        @include('livewire.staff.obe-management.dosen-management.dosen-toolbar-table', [
                            'key' => 1,
                        ])
                    </flux:dropdown>
                </td>
                {{-- Role --}}
                @if ($showMore)
                    <td class="table-main-sticky whitespace-nowrap text-center">{{ $user->dosen->nip ?? '-' }}</td>
                    <td class="table-sub whitespace-nowrap text-center">{{ $user->dosen->nidn ?? '-' }}</td>
                    <td class="table-sub whitespace-nowrap text-center">{{ $user->dosen->nidk ?? '-' }}</td>
                    <td class="table-second table-border-x whitespace-nowrap text-center">{{ $user->dosen->nik ?? '-' }}</td>
                @else
                    <td class="table-main-sticky whitespace-nowrap text-center">{{ $user->dosen->nip ?? '-' }} /
                        {{ $user->dosen->nidn ?? '-' }}</td>
                @endif



                <td class="table-second whitespace-nowrap">
                    {{ $user->name ?? '-' }}</td>

                <td class="table-second table-border-x text-center">
                    @if (!$user->trashed())
                        <x-button-action
                            @click="
                            $store.rps?.reset();
                            const type = '{{ strtolower($user->role) }}';
                            $store.user?.setType(type);
                            $store.user?.setEdit(1);
                            $store.user?.setColor('text-lime-700 dark:text-lime-400', 'bg-lime-50 dark:bg-lime-950/40');
                            $store.user?.setValueUserRPS(
                                '{{ $user->dosen->name ?? '' }}',
                                'NIP',
                                '{{ $user->dosen->nip ?? '' }}',
                                '{{ $user->dosen->pr_rel->prodi ?? '' }}',
                                '{{ $user->count_rps ?? '' }}',
                                '{{ $user->total_sks ?? '' }}',
                            );
                            $flux.modal('user-rps-modal').show();
                            {{-- $dispatch('open-edit-user-modal', { id: {{ $user->id }}, withRPS: 1, isRPS: 1 }); --}}
                            $dispatch('open-list-rps-user-modal', { id: {{ $user->id }}, withRPS: 1, isRPS: 1 });
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
                <td class="table-sub text-center">{{ $user->count_rps }} RPS</td>
                <td class="table-sub table-border-r text-center">{{ $user->total_sks }}
                    SKS</td>

                <td class="table-second text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer" wire:click="$dispatch('trigger-user-modal')">
                            @include('livewire.global.table.badge.status-user-badge', [
                                'xValue' => $user->status,
                            ])
                        </button>
                        @include('livewire.staff.obe-management.dosen-management.dosen-toolbar-table', [
                            'key' => 2,
                        ])
                    </flux:dropdown>

                </td>

                <td class="table-second min-w-48">
                    {{ $user->prodi ?? '-' }} ({{ $user->kode_pr ?? '---' }})</td>

                <td class="table-main text-center table-border-x">
                    <flux:dropdown>
                        <flux:button class="cursor-pointer" wire:click="$dispatch('trigger-user-modal')" variant="ghost"
                            size="sm" icon="ellipsis-horizontal" inset="top bottom">
                        </flux:button>
                        @include('livewire.staff.obe-management.dosen-management.dosen-toolbar-table', [
                            'key' => 3,
                        ])
                    </flux:dropdown>
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="13" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada data Dosen ditemukan!
                </td>
            </tr>
        @endforelse

        </x-global.main-layout-table>
</div>
