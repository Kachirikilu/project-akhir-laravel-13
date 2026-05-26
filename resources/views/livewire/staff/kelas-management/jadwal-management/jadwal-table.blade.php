<x-global.main-layout-table>

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
        {{-- BARIS PERTAMA --}}
        <tr>

            {{-- Kolom yang ditarik ke bawah (Tinggi 2 baris) --}}
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'id',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode',
                'isCenter' => 1,
                'isMain' => 1,
                'rowSpan' => 2,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'label_kelas',
                'headString' => 'Label',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'password',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])

            <th colspan="4" class="{{ $headSubKolom }}">
                Informasi Jadwal Kelas
            </th>

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

        <tr>
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'hari_pelaksanaan',
                'headString' => 'Hari',
                'isMain' => 1,
                'isCenter' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'jam_pelaksanaan',
                'headString' => 'Jam',
                'isCenter' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kapasitas',
                'headString' => 'Kapasitas',
                'isCenter' => 1,
            ])


            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'tanggal_pelaksanaan',
                'headString' => 'Tanggal',
                'isCenter' => 1,
            ])


        </tr>
    </x-slot:header>


    @forelse($jadwals as $j)
        <tr wire:key="kelas-{{ $j->id }}" data-kelas-id="{{ $j->id }}"
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="{{ $secondKolom }} text-center">{{ $j->id }}</td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @switch($j->kode_wilayah)
                            @case('IDL')
                                <flux:badge icon="academic-cap" color="emerald" size="sm">{{ $j->kode ?? '-' }}
                                </flux:badge>
                            @break

                            @case('PLG')
                                <flux:badge icon="academic-cap" color="amber" size="sm">{{ $j->kode ?? '-' }}</flux:badge>
                            @break

                            @default
                                <flux:badge icon="academic-cap" color="red" size="sm">{{ $j->kode ?? '-' }}</flux:badge>
                        @endswitch
                    </button>

                    @include('livewire.staff.kelas-management.jadwal-management.jadwal-toolbar-table', [
                        'x' => $j,
                        'editString' => 'editJadwal',
                        'nameXString' => 'Jadwal',
                        'confirmDeleteString' => 'deleteJadwal',
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} text-center whitespace-nowrap">{{ $j->label_full }}</td>
            <td class="{{ $secondKolom }} text-center whitespace-nowrap">
                {{ !empty($j->password) ? $j->password : '-' }}</td>

            <td class="{{ $mainKolom }} text-center whitespace-nowrap">{{ $j->hari }}</td>
            <td class="{{ $subKolom }} text-center whitespace-nowrap">{{ $j->jam_pelaksanaan }}</td>
            <td class="{{ $subKolom }} text-center whitespace-nowrap">
                {{ $j->mahasiswas_count . ' / ' . $j->kapasitas }}</td>
            <td class="{{ $subKolom }} text-center whitespace-nowrap">{{ $j->tanggal_pelaksanaan }}</td>


            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.staff.kelas-management.jadwal-management.jadwal-toolbar-table', [
                        'x' => $j,
                        'editString' => 'editJadwal',
                        'nameXString' => 'Jadwal',
                        'confirmDeleteString' => 'deleteJadwal',
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $j->created_day ?? '-' }}</td>
            <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $j->updated_day ?? '-' }}</td>
        </tr>
        @empty
            <tr>
                <td colspan="11" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                    Tidak ada data Jadwal Kelas ditemukan!
                </td>
            </tr>
        @endforelse


        <x-slot:footer>
            @include('livewire.global.table.footer-table', [
                'typeXString' => $jadwals,
            ])
        </x-slot:footer>

        </x-admin.global.table.main-layout-table>
