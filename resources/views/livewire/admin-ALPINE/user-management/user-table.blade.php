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

            @if (!($withNilai ?? false))
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
            @endif



            @if (($withRPS ?? false) && ($switchTable == 'dosen' || $switchTable == 'mahasiswa'))
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'kode',
                    'headString' => $switchTable == 'dosen' ? 'NIP / NIDN' : 'NIM',
                    'rowSpan' => 2,
                    'isMain' => 1,
                    'isCenter' => 1,
                    'isSticky' => 1,
                ])
            @else
                @if ($switchTable == '')
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'role',
                        'rowSpan' => 2,
                        'isCenter' => 1,
                    ])
                @else
                    <th rowspan="2" class="table-head ">Role</th>
                @endif
            @endif

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'name',
                'headString' => 'Nama',
                'rowSpan' => 2,
                'isMain' => 1,
                'isSticky' => $withRPS ?? false ? 0 : 1,
            ])
            @if ($withCapaian ?? null && $switchTable == 'mahasiswa')
                <th colspan="{{ $withNilai ?? false ? 4 : 3 }}" class="table-head-sub">
                    Nilai Capaian
                </th>
            @endif

            @if (!($withRPS ?? false))
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'email',
                    'rowSpan' => 2,
                ])
            @endif

            @if (($withRPS ?? false) && ($switchTable == 'dosen' || $switchTable == 'mahasiswa'))
                <th colspan="3" class="table-head-sub">
                    Rencana Pembelajaran Semester
                </th>
            @endif

            @if (!($withRPS ?? false))
                <th colspan="{{ $switchTable == 'mahasiswa' ? 2 : ($switchTable == 'admin' ? 3 : 4) }}"
                    class="table-head-sub border-x">
                    Identitas (ID)
                </th>
            @endif

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
            @if ((($withCapaian ?? false) && ($withProdi ?? false)) || !isset($withCapaian))
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'program_studi',
                    'rowSpan' => 2,
                ])
            @endif
            <th rowspan="2" class="table-head border-x">Aksi</th>

            @if (!(($withRPS ?? false) || ($withNilai ?? false) || Auth::user()?->dosen))
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
            @endif

        </tr>

        <tr>
            @if ($withCapaian ?? null && $switchTable == 'mahasiswa')

                @if ($withNilai ?? null)
                    <th class="table-head border-x">Show</th>
                @endif
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'rekap_mhs',
                    'headString' => 'Nilai',
                    'isCenter' => 1,
                    'isBorderL' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'ipk_mhs',
                    'headString' => 'IPK',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mutu_mhs',
                    'headString' => 'Mutu',
                    'isCenter' => 1,
                    'isMain' => 1,
                ])
            @endif
            @if (($withRPS ?? false) && ($switchTable == 'dosen' || $switchTable == 'mahasiswa'))
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
            @endif

            @if (!($withRPS ?? false))
                @include('livewire.global.table.head-table', [
                    'sortFieldString' =>
                        $switchTable == '' ? 'identity1' : ($switchTable == 'mahasiswa' ? 'nim' : 'nip'),
                    'headString' =>
                        $switchTable == '' ? 'NIP/NIM' : ($switchTable == 'mahasiswa' ? 'NIM' : 'NIP'),
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
                        // 'isBorderR' => $switchTable == 'admin' ? 1 : 0,
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
            @endif
        </tr>
    </x-slot:header>


    @forelse($users as $user)
        @php
            $detail = $user->admin ?? ($user->dosen ?? $user->mahasiswa);
        @endphp

        <tr wire:key="user-{{ $user->id }}" data-user-id="{{ $user->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

            @if (!($withNilai ?? false))
                <td class="table-main text-center table-border-x">{{ $user->id }}</td>

                @if ($switchTable !== '')
                    <td class="table-second table-border-r text-center">{{ $user->role_id }}</td>
                @endif
            @endif
            {{-- @php
                    
                    $jadwalId = 5;

                    $users = User::with(['mahasiswa'])
                        ->withCount(['kehadirans' => function ($query) use ($jadwalId) {
                            $query->whereHas('sesi', function ($q) use ($jadwalId) {
                                $q->where('kj_id', $jadwalId);
                            });
                        }])
                        ->get();

    
                @endphp
                <td class="table-second table-border-r text-center">{{ $user->kehadirans_count ?? 0 }} Sesi</td> --}}
            {{-- Role --}}
            @if ($withRPS ?? false)
                @if ($switchTable == 'dosen')
                    <td class="table-main-sticky whitespace-nowrap text-center">{{ $user->identity1 ?? '-' }} /
                        {{ $user->identity2 ?? '-' }}</td>
                @elseif ($switchTable == 'mahasiswa')
                    <td class="table-main-sticky whitespace-nowrap text-center">{{ $user->identity1 ?? '-' }}</td>
                @endif
            @else
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

                        @include('livewire.admin.user-management.user-toolbar-table', [
                            'x' => $user,
                            'nameXString' => $user->role,
                        ])

                    </flux:dropdown>
                </td>
            @endif


            <td class="{{ $withRPS ?? false ? 'table-second' : 'table-main-sticky' }} whitespace-nowrap">
                {{ $user->name ?? '-' }}</td>

            @if ($withCapaian ?? null && $switchTable == 'mahasiswa')
                @if ($withNilai ?? null)
                    <td class="table-second table-border-x whitespace-nowrap">
                        @if (!$user->trashed())
                            <x-button-action color="emerald"
                                href="{{ route('nilai-mahasiswa-management', [
                                    'nim' => $user->mahasiswa->nim ?? null,
                                ]) }}"
                                wire:navigate>
                                <flux:icon name="document-text" class="w-3.5 h-3.5" />
                                Nilai
                            </x-button-action>
                        @else
                            <code
                                class="font-mono text-xs bg-[var(--second-table-color)] px-1.5 py-0.5 rounded border table-border text-[var(--contrast-main-text)] italic">
                                unfound
                            </code>
                        @endif
                    </td>
                @endif
                <td class="table-second table-border-l whitespace-nowrap text-center">
                    {{ $user->mahasiswa->rekap_mhs ?? '0.00' }}</td>
                <td class="table-second whitespace-nowrap text-center">
                    {{ $user->mahasiswa->ipk_mhs ?? '0.00' }}</td>
                <td class="table-sub table-border-l whitespace-nowrap text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @include('livewire.global.table.badge.nilai-mutu-badge', [
                                'xValue' => $user->mahasiswa->mutu_mhs ?? 'E',
                            ])
                        </button>
                        @include('livewire.admin.user-management.user-toolbar-table', [
                            'x' => $user,
                            'nameXString' => $user->role,
                        ])
                    </flux:dropdown>
                </td>
            @endif
            @if (!($withRPS ?? false))
                <td class="table-second">{{ $user->email }}</td>
            @endif
            @if (($withRPS ?? false) && ($switchTable == 'dosen' || $switchTable == 'mahasiswa'))
                <td class="table-second table-border-x text-center">

                    @if (!$user->trashed())
                        <x-button-action
                            @click="
                            $store.user?.reset();

                            const type = '{{ strtolower($user->role) }}';

                            $store.user?.setType(type);
                            $store.user?.setEdit(1);

                            const colors = {
                                admin: 'text-red-700 dark:text-red-400',
                                dosen: 'text-lime-700 dark:text-lime-400',
                                mahasiswa: 'text-cyan-700 dark:text-cyan-400',
                            };
                            const colors2 = {
                                admin: 'bg-red-50 dark:bg-red-950/40',
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
                                "
                            wire:click="editUser({{ $user->id }}, {{ $withRPS ?? false }}, 1)" color="emerald"
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
            @endif

            @if (!($withRPS ?? false))
                <td
                    class="table-main {{ $switchTable == 'mahasiswa' ? 'table-border-l' : 'table-border-x' }}  text-center">
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
            @endif

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

                        @include('livewire.admin.user-management.user-toolbar-table', [
                            'x' => $user,
                            'nameXString' => $user->role,
                        ])
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
                    @include('livewire.admin.user-management.user-toolbar-table', [
                        'x' => $user,
                        'nameXString' => $user->role,
                    ])

                </flux:dropdown>
            </td>

            @if ((($withCapaian ?? false) && ($withProdi ?? false)) || !isset($withCapaian))
                <td class="table-second min-w-48">
                    {{ $user->prodi ?? '-' }} ({{ $user->kode_pr ?? '---' }})</td>
            @endif

            <td class="table-main text-center table-border-x">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.admin.user-management.user-toolbar-table', [
                        'x' => $user,
                        'nameXString' => $user->role,
                    ])

                </flux:dropdown>
            </td>

            @if (!(($withRPS ?? false) || ($withNilai ?? false) || Auth::user()?->dosen))
                <td class="table-second whitespace-nowrap text-center">{{ $user->created_day ?? '-' }}</td>
                <td class="table-second whitespace-nowrap text-center">{{ $user->updated_day ?? '-' }}</td>
            @endif
        </tr>

        @empty
            <tr>
                <td colspan="{{ match ($switchTable) {
                    'admin' => 14,
                    'dosen' => $withRPS ?? null ? 10 : 14,
                    'mahasiswa' => $withRPS ?? null ? 14 : 14,
                    default => 14,
                } }}"
                    class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    {{-- <td colspan="{{ $withRPS ?? null ? 12 : 14 }}"
                    class="text-[var(--contrast-second-text)] px-6 py-4 text-center"> --}}
                    Tidak ada data {{ !empty($switchTable) ? ucfirst($switchTable) : 'Pengguna' }} ditemukan!
                </td>
            </tr>
        @endforelse

        </x-admin.global.table.main-layout-table>
