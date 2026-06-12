<x-global.main-layout-table :paginator="$users" :onlyAdmin="!Auth::user()->admin">

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
        $borderX = 'border-[var(--border-table-color)] border-x';
    @endphp

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
                    'isMain' => 1,
                    'isCenter' => 1,
                ])
            @elseif ($switchTable == 'dosen')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'dosen_id',
                    'headString' => 'DSN ID',
                    'rowSpan' => 2,
                    'isMain' => 1,
                    'isCenter' => 1,
                ])
            @elseif ($switchTable == 'mahasiswa')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mahasiswa_id',
                    'headString' => 'MHS ID',
                    'rowSpan' => 2,
                    'isMain' => 1,
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
                <th rowspan="2" class="{{ $headKolom }}'">Role</th>
            @endif

            @include('livewire.global.table.head-table', [
                'sortFieldString' => $withRPS ?? null ? 'kode' : 'name',
                'headString' => 'Nama',
                'rowSpan' => 2,
                'isMain' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'email',
                'rowSpan' => 2,
            ])

            @if (($withRPS ?? false) && $switchTable == 'dosen')
                <th colspan="3" class="{{ $headSubKolom }}">
                    RPS
                </th>
            @endif

            <th colspan="{{ $switchTable == 'mahasiswa' ? 2 : ($switchTable == 'admin' ? 3 : 4) }}"
                class="{{ $headSubKolom }}">
                Identitas (ID)
            </th>

            {{-- Angkatan - Autocomplete Input --}}
            @if ($switchTable == 'mahasiswa')
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
            <th rowspan="2" class="{{ $headKolom }} border-x">Aksi</th>

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
            @if (($withRPS ?? false) && $switchTable == 'dosen')
                <th class="{{ $headKolom }} border-x">Show</th>
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'count_rps',
                    'headString' => 'Total RPS',
                    'isCenter' => 1,
                    'isBorderL' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'total_sks',
                    'isCenter' => 1,
                ])
            @endif
            @include('livewire.global.table.head-table', [
                'sortFieldString' =>
                    $switchTable == '' ? 'identity1' : ($switchTable == 'mahasiswa' ? 'nim' : 'nip'),
                'headString' => $switchTable == '' ? 'NIP/NIM' : ($switchTable == 'mahasiswa' ? 'NIM' : 'NIP'),
                'isCenter' => 1,
                'isMain' => 1,
            ])
            @if ($switchTable !== 'mahasiswa')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' =>
                        $switchTable == '' ? 'identity2' : ($switchTable == 'dosen' ? 'nidn' : 'nitk'),
                    'headString' =>
                        $switchTable == '' ? 'NITK/NIDN' : ($switchTable == 'dosen' ? 'NIDN' : 'NITK'),
                    'isCenter' => 1,
                    'isBorderR' => $switchTable == 'admin' ? 1 : 0,
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
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="{{ $mainKolom }} text-center">{{ $user->id }}</td>

            @if ($switchTable !== '')
                <td class="{{ $secondKolom }} {{ $borderR }} text-center">{{ $user->role_id }}</td>
            @endif
            {{-- @php
                    
                    $jadwalId = 5;

                    $users = User::with(['mahasiswa'])
                        ->withCount(['kehadirans' => function ($query) use ($jadwalId) {
                            $query->whereHas('sesi', function ($q) use ($jadwalId) {
                                $q->where('jadwal_id', $jadwalId);
                            });
                        }])
                        ->get();

    
                @endphp
                <td class="{{ $secondKolom }} {{ $borderR }} text-center">{{ $user->kehadirans_count ?? 0 }} Sesi</td> --}}
            {{-- Role --}}
            <td class="{{ $secondKolom }} text-center">
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
                        'nameXString' => 'Pengguna',
                    ])

                </flux:dropdown>
            </td>
            <td class="{{ $mainKolom }} whitespace-nowrap">{{ $user->name ?? '-' }}</td>
            <td class="{{ $secondKolom }}">{{ $user->email }}</td>
            @if (($withRPS ?? false) && $switchTable == 'dosen')
                <td class="{{ $secondKolom }} {{ $borderX }} text-center">
                    <x-button-action
                        @click="
                            $store.user?.reset();
                            const type = '{{ strtolower($user->role) }}';
                            $store.user?.setColor('text-lime-700 dark:text-lime-400');
                            $flux.modal('user-rps-modal').show();
                        "
                        wire:click="editUser({{ $user->id }}, {{ $withRPS ?? false }}, 1)" color="blue"
                        wire:navigate>
                        <flux:icon name="eye" class="w-3.5 h-3.5" />
                        <span>RPS</span>
                    </x-button-action>
                </td>
                <td class="{{ $subKolom }} text-center">{{ $user->count_rps }} RPS</td>
                <td class="{{ $subKolom }} text-center">{{ $user->total_sks }} SKS</td>
            @endif
            <td class="{{ $mainKolom }} text-center">{{ $user->identity1 ?? '-' }}</td>
            @if ($switchTable != 'mahasiswa')
                <td class="{{ $subKolom }} {{ $switchTable == 'admin' ? 'border-r' : '' }} text-center">
                    {{ $user->identity2 ?? '-' }}
                </td>
            @endif

            @if ($switchTable == 'dosen' || $switchTable == '')
                <td
                    class="{{ $subKolom }} {{ $switchTable == '' || $switchTable == 'dosen' ? 'border-r' : '' }} text-center">
                    {{ $user->identity3 ?? '-' }}
                </td>
            @endif
            <td class="{{ $subKolom }} {{ $borderR }} text-center">{{ $user->nik ?? '-' }}</td>

            @if ($switchTable == 'mahasiswa')
                <td class="{{ $secondKolom }} {{ $borderR }} text-center">{{ $detail->angkatan ?? '-' }}</td>
            @endif

            @if ($switchTable == 'admin' || $switchTable == 'mahasiswa')
                <td class="{{ $secondKolom }} text-center">
                    <flux:dropdown>
                        <button class="cursor-pointer focus:outline-none">
                            @include('livewire.global.table.badge.kode-wilayah-badge', [
                                'xValue' => $user->wilayah,
                                'sortir' => $user->kode_wilayah,
                            ])
                        </button>

                        @include('livewire.admin.user-management.user-toolbar-table', [
                            'x' => $user,
                            'nameXString' => 'Pengguna',
                        ])
                    </flux:dropdown>
                </td>
            @endif

            <td class="{{ $secondKolom }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.status-user-badge', [
                            'xValue' => $user->status,
                        ])
                    </button>
                    @include('livewire.admin.user-management.user-toolbar-table', [
                        'x' => $user,
                        'nameXString' => 'Pengguna',
                    ])

                </flux:dropdown>
            </td>


            <td class="{{ $secondKolom }} min-w-48">
                {{ $user->prodi ?? '-' }} ({{ $user->kode_pr ?? '---' }})</td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.admin.user-management.user-toolbar-table', [
                        'x' => $user,
                        'nameXString' => 'Pengguna',
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $user->created_day ?? '-' }}</td>
            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $user->updated_day ?? '-' }}</td>
        </tr>

        @empty
            <tr>
                {{-- <td colspan="{{ match ($switchTable) {
                    'admin' => 14,
                    'dosen' => 14,
                    'mahasiswa' => 14,
                    default => 14,
                } }}"
                    class="text-[var(--contrast-second-text)] px-6 py-4 text-center"> --}}
                <td colspan="{{ $withRPS ?? null ? 17 : 14 }}" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada data {{ !empty($switchTable) ? ucfirst($switchTable) : 'Pengguna' }} ditemukan!
                </td>
            </tr>
        @endforelse

        </x-admin.global.table.main-layout-table>
