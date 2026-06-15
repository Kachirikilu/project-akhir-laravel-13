<x-global.main-layout-table :paginator="$users" :onlyAdmin="!Auth::user()->admin">

    @php
        $padingKolom = 'px-6 py-4 text-sm';
        $headKolom =
            'bg-[var(--main-table-color)] table-border text-[var(--contrast-main-text)] uppercase text-xs ' .
            $padingKolom;

        $mainKolom =
            'bg-[var(--main-table-trans)] table-border text-[var(--contrast-main-text)]' .
            ' border-x ' .
            $padingKolom;
        $secondKolom = 'bg-[var(--second-table-trans)] text-[var(--contrast-second-text)] ' . $padingKolom;

        $headSubKolom =
            'bg-[var(--main-table-color)] table-border text-[var(--focus-color)] border-x border-b text-center font-bold uppercase ' .
            $padingKolom;
        $subKolom =
            'bg-[var(--sub-table-trans)] table-border text-[var(--contrast-second-text)] ' .
            $padingKolom;
    @endphp

    @php
        $borderR = 'table-border border-r';
        $borderX = 'table-border border-x';
    @endphp

    <x-slot:header>
        <tr>
                <th rowspan="2" class="table-head'">ID</th>
                <th rowspan="2" class="table-head'">Email</th>
                <th rowspan="2" class="table-head'">Name</th>

        </tr>

    </x-slot:header>


    @forelse($users as $user)
        @php
            $detail = $user->admin ?? ($user->dosen ?? $user->mahasiswa);
        @endphp

        <tr wire:key="user-{{ $user->id }}" data-user-id="{{ $user->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] transition-colors duration-200">
            <td class="table-main text-center">{{ $user->id }}</td>
            <td class="table-main whitespace-nowrap">{{ $user->name ?? '-' }}</td>
            <td class="table-second">{{ $user->email }}</td>
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
