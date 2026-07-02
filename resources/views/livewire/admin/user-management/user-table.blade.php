<x-global.main-layout-table :paginator="$users" :onlyAdmin="!Auth::user()->admin">

    @if ($switchTable == 'mahasiswa')
        <x-slot:sortir>
            <div x-data="{ activeTab: @entangle('filterAngkatan') }"
                class="pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto">

                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'filterByAngkatan',
                    'xFilter' => 'filterAngkatan',
                    'tabFilter' => $totalSeluruhAngkatan ?? null,
                    'tabString' => '',
                    'tabNameString' => 'Semua',
                    'icon' => 'users',
                ])
                @foreach ($angkatanFilter as $angkatan)
                    @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                        'xString' => 'filterByAngkatan',
                        'xFilter' => 'filterAngkatan',
                        'tabFilter' => $totalAngkatan[$angkatan] ?? 0,
                        'tabString' => $angkatan,
                        'tabNameString' => $angkatan,
                        'icon' => 'calendar-days',
                    ])
                @endforeach
            </div>
        </x-slot:sortir>
    @endif

    <x-slot:header>
        <tr>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'id',
                'rowSpan' => 2,
                'isMain' => 1,
                'isCenter' => 1,
            ])

            @if ($switchTable == 'admin')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'admin_id',
                    'headString' => 'ADM ID',
                    'rowSpan' => 2,
                    'isBorderR' => 1,
                    'isCenter' => 1,
                ])
            @elseif ($switchTable == 'dosen')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'dosen_id',
                    'headString' => 'DSN ID',
                    'rowSpan' => 2,
                    'isBorderR' => 1,
                    'isCenter' => 1,
                ])
            @elseif ($switchTable == 'mahasiswa')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mahasiswa_id',
                    'headString' => 'MHS ID',
                    'rowSpan' => 2,
                    'isBorderR' => 1,
                    'isCenter' => 1,
                ])
            @endif


            @if ($switchTable == '')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'role',
                    'rowSpan' => 2,
                    'isCenter' => 1,
                ])
            @else
                <th rowspan="2" class="table-head ">Role</th>
            @endif

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'name',
                'headString' => 'Nama',
                'rowSpan' => 2,
                'isMain' => 1,
                'isSticky' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'email',
                'rowSpan' => 2,
            ])


            <th colspan="{{ $switchTable == 'mahasiswa' ? 2 : ($switchTable == 'admin' ? 3 : 4) }}"
                class="table-head-sub border-x">
                Identitas (ID)
            </th>

            {{-- Angkatan - Autocomplete Input --}}
            @if ($switchTable == 'mahasiswa')
                @if ($filterAngkatan == '')
                    @include('livewire.global.search-and-filters.table-search', [
                        'sortFieldString' => 'angkatan',
                        'modelString' => 'searchAngkatan',
                        'resetXFilter' => 'resetInputAngkatan()',
                        'wInput' => 20,
                        'numberOnly' => 1,
                        'maxLength' => 4,
                        'placeholder' => 'Tahun',
                        'rowSpan' => 2,
                        'isBorderR' => 1,
                    ])
                @else
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'angkatan',
                        'rowSpan' => 2,
                        'isCenter' => 1,
                        'isMain' => 1,
                    ])
                @endif
            @endif



            @if ($switchTable == 'admin' || $switchTable == 'mahasiswa')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'kampus',
                    'rowSpan' => 2,
                    'isCenter' => 1,
                ])
            @endif
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

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'created_at',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'updated_at',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])

        </tr>

        <tr>
            @include('livewire.global.table.head-table', [
                'sortFieldString' =>
                    $switchTable == '' ? 'identity1' : ($switchTable == 'mahasiswa' ? 'nim' : 'nip'),
                'headString' => $switchTable == '' ? 'NIP/NIM' : ($switchTable == 'mahasiswa' ? 'NIM' : 'NIP'),
                'isCenter' => 1,
                'isBorderL' => 1,
                'isBorderR' => $switchTable == 'mahasiswa' ? 0 : 1,
            ])
            @if ($switchTable !== 'mahasiswa')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' =>
                        $switchTable == '' ? 'identity2' : ($switchTable == 'dosen' ? 'nidn' : 'nitk'),
                    'headString' =>
                        $switchTable == '' ? 'NITK/NIDN' : ($switchTable == 'dosen' ? 'NIDN' : 'NITK'),
                    'isCenter' => 1,
                ])
                @if ($switchTable !== 'admin')
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'nidk',
                        'headString' => 'NIDK',
                        'isCenter' => 1,
                    ])
                @endif
            @endif
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'nik',
                'headString' => 'NIK',
                // 'isSubHeader' => 1,
                'isCenter' => 1,
                'isMain' => 1,
            ])
        </tr>
    </x-slot:header>


    @forelse($users as $user)
        @php
            $detail = $user->admin ?? ($user->dosen ?? $user->mahasiswa);
        @endphp

        <tr wire:key="user-{{ $user->id }}" data-user-id="{{ $user->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

            <td class="table-main text-center table-border-x">{{ $user->id }}</td>

            @if ($switchTable !== '')
                <td class="table-second table-border-r text-center">{{ $user->role_id }}</td>
            @endif

            <td class="table-second text-center">
                <flux:dropdown>

                    <button class="cursor-pointer">
                        @switch($user->role)
                            @case('Admin')
                                <flux:badge icon="cog-6-tooth" color="red" size="sm">Admin</flux:badge>
                            @break

                            @case('Dosen')
                                <flux:badge icon="briefcase" color="lime" size="sm">Dosen</flux:badge>
                            @break

                            @case('Mahasiswa')
                                <flux:badge icon="book-open" color="cyan" size="sm">Mahasiswa</flux:badge>
                            @break

                            @default
                                <flux:badge icon="user-circle" size="sm">{{ $user->role }}</flux:badge>
                        @endswitch
                    </button>
                    @include('livewire.admin.user-management.user-toolbar-table', ['key' => 1])
                </flux:dropdown>
            </td>


            <td class="{{ $withRPS ?? false ? 'table-second' : 'table-main-sticky' }} whitespace-nowrap">
                {{ $user->name ?? '-' }}</td>

            <td class="table-second">{{ $user->email }}</td>

            <td class="table-main {{ $switchTable == 'mahasiswa' ? 'table-border-l' : 'table-border-x' }}  text-center">
                {{ $user->identity1 ?? '-' }}</td>
            @if ($switchTable != 'mahasiswa')
                <td class="table-sub text-center">
                    {{ $user->identity2 ?? '-' }}
                </td>
            @endif

            @if ($switchTable == 'dosen' || $switchTable == '')
                <td class="table-sub text-center">
                    {{ $user->identity3 ?? '-' }}
                </td>
            @endif
            <td class="table-sub table-border-x text-center">{{ $user->nik ?? '-' }}</td>

            @if ($switchTable == 'mahasiswa')
                <td class="table-second table-border-r text-center">{{ $detail->angkatan ?? 'YYYY' }}</td>
            @endif

            @if ($switchTable == 'admin' || $switchTable == 'mahasiswa')
                <td class="table-second text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer focus:outline-none">
                            @include('livewire.global.table.badge.kode-wilayah-badge', [
                                'xValue' => $user->wilayah,
                                'sortir' => $user->kode_wilayah,
                            ])
                        </button>
                        @include('livewire.admin.user-management.user-toolbar-table', ['key' => 2])
                    </flux:dropdown>
                </td>
            @endif

            <td class="table-second text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.status-user-badge', [
                            'xValue' => $user->status,
                        ])
                    </button>
                    @include('livewire.admin.user-management.user-toolbar-table', ['key' => 3])
                </flux:dropdown>
            </td>

            <td class="table-second min-w-48">
                {{ $user->prodi ?? '-' }} ({{ $user->kode_pr ?? '---' }})</td>

            <td class="table-main text-center table-border-x">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>
                    @include('livewire.admin.user-management.user-toolbar-table', ['key' => 4])
                </flux:dropdown>
            </td>

            <td class="table-second whitespace-nowrap text-center">{{ $user->created_day ?? '-' }}</td>
            <td class="table-second whitespace-nowrap text-center">{{ $user->updated_day ?? '-' }}</td>
        </tr>

        @empty
            <tr>
                <td colspan="14" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada data {{ !empty($switchTable) ? ucfirst($switchTable) : 'Pengguna' }} ditemukan!
                </td>
            </tr>
        @endforelse

        </x-admin.global.table.main-layout-table>
