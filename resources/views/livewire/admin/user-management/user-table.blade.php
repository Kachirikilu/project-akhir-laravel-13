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
                    'wInput' => 15,
                    'numberOnly' => 1,
                    'maxlength' => 4,
                    'placeholder' => 'Tahun',
                    'rowSpan' => 2,
                    'isBorderR' => 1,
                ])
            @endif

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'status',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'prodi',
                'headString' => 'Program Studi',
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
            @include('livewire.global.table.head-table', [
                'sortFieldString' => $switchTable == '' ? 'identity1' : ($switchTable == 'mahasiswa' ? 'nim' : 'nip'),
                'headString' => $switchTable == '' ? 'NIP/NIM' : ($switchTable == 'mahasiswa' ? 'NIM' : 'NIP'),
                'isCenter' => 1,
                'isMain' => 1,
            ])
            @if ($switchTable !== 'mahasiswa')
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => $switchTable == '' ? 'identity2' : ($switchTable == 'dosen' ? 'nidn' : 'nitk'),
                    'headString' => $switchTable == '' ? 'NITK/NIDN' : ($switchTable == 'dosen' ? 'NIDN' : 'NITK'),
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

            @if ($switchTable == 'admin')
                <td class="{{ $secondKolom }} {{ $borderR }} text-center">{{ $user->admin->id }}</td>
            @elseif ($switchTable == 'dosen')
                <td class="{{ $secondKolom }} {{ $borderR }} text-center">{{ $user->dosen->id }}</td>
            @elseif ($switchTable == 'mahasiswa')
                <td class="{{ $secondKolom }} {{ $borderR }} text-center">{{ $user->mahasiswa->id }}</td>
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
            @endif
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

            <td class="{{ $secondKolom }} text-center">
                <flux:dropdown>

                    <button class="cursor-pointer">
                        @switch($user->status)
                            {{-- HIJAU: Status Lulus --}}
                            @case('Lulus')
                                <flux:badge color="blue" size="sm">{{ $user->status }}</flux:badge>
                            @break

                            {{-- HIJAU: Status Aktif --}}
                            @case('Aktif')
                                <flux:badge color="green" size="sm">{{ $user->status }}</flux:badge>
                            @break

                            {{-- KUNING: Status Transisi/Sementara --}}
                            @case('Tugas Belajar')
                            @case('Izin Belajar')

                            @case('Mutasi')
                            @case('Cuti')

                            @case('Cuti Sabatika')
                            @case('Cuti Luar Tanggungan')

                            @case('Pindah')
                                <flux:badge color="yellow" size="sm">{{ $user->status }}</flux:badge>
                            @break

                            {{-- ORANGE: Keluar Prosedural / Masalah Administrasi --}}
                            @case('Resign')
                            @case('Pensiun')

                            @case('Alih Tugas')
                            @case('Mengundurkan Diri')

                            @case('Non-Aktif')
                                <flux:badge color="orange" size="sm">{{ $user->status }}</flux:badge>
                            @break

                            {{-- MERAH: Berhenti Permanen / Sanksi / Masalah Berat --}}
                            @case('Diberhentikan')
                            @case('Drop Out')

                            @case('Meninggal Dunia')
                            @case('Hilang')
                                <flux:badge color="red" size="sm">{{ $user->status }}</flux:badge>
                            @break

                            @default
                                <flux:badge size="sm">{{ $user->status }}</flux:badge>
                        @endswitch
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
                <td colspan="{{ match ($switchTable) {
                    'admin' => 13,
                    'dosen' => 14,
                    'mahasiswa' => 13,
                    default => 13,
                } }}"
                    class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada data {{ !empty($switchTable) ? ucfirst($switchTable) : 'Pengguna' }} ditemukan!
                </td>
            </tr>
        @endforelse

        </x-admin.global.table.main-layout-table>
