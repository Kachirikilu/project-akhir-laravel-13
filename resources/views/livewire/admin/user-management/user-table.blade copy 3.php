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
                <th rowspan="2" class="{{ $headKolom }}'">ID</th>
                <th rowspan="2" class="{{ $headKolom }}'">Email</th>
                <th rowspan="2" class="{{ $headKolom }}'">Name</th>

        </tr>

    </x-slot:header>


    @forelse($users as $user)
        @php
            $detail = $user->admin ?? ($user->dosen ?? $user->mahasiswa);
        @endphp

        <tr wire:key="user-{{ $user->id }}" data-user-id="{{ $user->id }}"
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">
            <td class="{{ $mainKolom }} text-center">{{ $user->id }}</td>
            <td class="{{ $mainKolom }} whitespace-nowrap">{{ $user->name ?? '-' }}</td>
            <td class="{{ $secondKolom }}">{{ $user->email }}</td>
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
                <td colspan="14" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada data {{ !empty($switchTable) ? ucfirst($switchTable) : 'Pengguna' }} ditemukan!
                </td>
            </tr>
        @endforelse

        </x-admin.global.table.main-layout-table>
