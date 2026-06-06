<x-global.main-layout-table :paginator="$jadwals">

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

            <th rowspan="2" class="{{ $headKolom }} border-x">Show</th>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'label_kelas',
                'headString' => 'Label',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])

            @if (Auth::user()->admin || Auth::user()->dosen)
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'password',
                    'isCenter' => 1,
                    'rowSpan' => 2,
                ])
            @else
                <th rowspan="2" class="{{ $headKolom }}">Password</th>
            @endif

            @if ($kelas == null)
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'mk',
                    'headString' => 'Mata Kuliah',
                    'rowSpan' => 2,
                ])
            @endif

            <th colspan="4" class="{{ $headSubKolom }}">
                Informasi Jadwal Kelas
            </th>

            @if ($kelas == null)
                <th colspan="5" class="{{ $headSubKolom }}">
                    Informasi Mata Kuliah
                </th>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'program_studi',
                'rowSpan' => 2,
            ])
            @endif

            @if (Auth::user()->admin || Auth::user()->dosen)
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
            @endif
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

            @if ($kelas == null)
                {{-- Informasi MK --}}
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'kode_mk',
                    'isCenter' => 1,
                    'isMain' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'semester',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'sks',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'pembelajaran',
                    'isCenter' => 1,
                ])
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'wajib',
                    'isCenter' => 1,
                    'isMain' => 1,
                ])
            @endif
        </tr>
    </x-slot:header>


    @forelse($jadwals as $j)
        <tr wire:key="kelas-jadwal-{{ $j->id }}" data-kelas-id="{{ $j->id }}"
            class="border-[var(--border-table-color)] hover:bg-[var(--hover-table-color)] transition-colors duration-200">

            <td class="{{ $secondKolom }} text-center">{{ $j->id }}</td>

            <td class="{{ $mainKolom }} text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.kode-wilayah-badge', [
                            'xValue' => $j->kode,
                            'sortir' => $j->kode_wilayah,
                        ])
                    </button>

                    @include('livewire.all-role.kelas-management.jadwal-management.jadwal-toolbar-table', [
                        'x' => $j,
                        'editString' => 'editJadwal',
                        'nameXString' => 'Jadwal',
                        'confirmDeleteString' => 'deleteJadwal',
                    ])

                </flux:dropdown>
            </td>

            <td class="{{ $secondKolom }} {{ $borderR }} text-center whitespace-nowrap">

                @if ($j->is_my_class || Auth::user()->admin || Auth::user()->dosen)
                    <x-button-action color="amber"
                        href="{{ route('sesi-management', [$j->kode_kelas, $j->kode_jadwal]) }}" wire:navigate>
                        <flux:icon name="calendar-days" class="w-3.5 h-3.5" />
                        <span>Lihat Kelas
                    </x-button-action>
                @else
                    @php
                        $buttonClass =
                            'inline-flex items-center justify-center gap-1.5 px-3 py-1 rounded-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/35 transition-all duration-200 text-sm font-medium shadow-sm cursor-pointer';
                    @endphp
                    @if (!empty($j->with_pw))
                        <x-button-action color="blue"
                            @click="
                                    $store.jadwal?.setEdit(0);
                                    $store.jadwal?.setColor('text-blue-700 dark:text-blue-400');
                                    $flux.modal('jadwal-join').show();
                                    $store.jadwal?.setValueJoinJadwal(
                                        '{{ $j->id ?? '' }}',
                                        '{{ $j->kode ?? '' }}',
                                        '{{ $j->kode_kelas ?? '' }}',
                                        '{{ $j->label_extra ?? '' }}',
                                    );
                                ">
                            <flux:icon name="user-plus" class="w-3.5 h-3.5" />
                            <span>Join</span>
                        </x-button-action>
                    @else
                        <form x-on:submit.prevent="$wire.joinJadwal($store.jadwal)" id="jadwalForm">
                            <x-button-action color="blue"
                                @click="
                                        $store.jadwal?.setEdit(0);
                                        $store.jadwal?.setColor('text-blue-700 dark:text-blue-400');
                                        $store.jadwal?.setValueJoinJadwal(
                                            '{{ $j->id ?? '' }}',
                                        );
                                    ">
                                <flux:icon name="user-plus" class="w-3.5 h-3.5" />
                                <span>Join</span>
                            </x-button-action>
                        </form>
                    @endif
                @endif

            </td>

            <td class="{{ $secondKolom }} text-center whitespace-nowrap">{{ $j->label_full }}</td>
            <td class="{{ $secondKolom }} text-center whitespace-nowrap">

                @if ($j->is_my_class)
                    <code
                        class="italic font-mono bg-[var(--second-table-color)] px-1.5 py-0.5 rounded border border-[var(--border-table-color)] text-[var(--contrast-main-text)]">
                        Terdaftar
                    </code>
                @else
                    @if (Auth::user()->admin || Auth::user()->dosen)
                        @if (!empty($j->password))
                            <code
                                class="font-mono bg-[var(--second-table-color)] px-1.5 py-0.5 rounded border border-[var(--border-table-color)] text-[var(--contrast-main-text)]">
                                {{ $j->password }}
                            </code>
                        @else
                            <span class="text-[10px] text-[var(--contrast-second-text)]">
                                Tanpa Password
                            </span>
                        @endif
                    @else
                        <span class="text-[10px] italic text-[var(--contrast-second-text)]">
                            @if (!empty($j->with_pw))
                                Memiliki Password
                            @else
                                Tanpa Password
                            @endif
                        </span>
                    @endif
                @endif

                @if ($kelas == null)
            <td class="{{ $secondKolom }} min-w-42">{{ $j->mk ?? '-' }}</td>
    @endif


    <td class="{{ $mainKolom }} text-center whitespace-nowrap">{{ $j->hari }}</td>
    <td class="{{ $subKolom }} text-center whitespace-nowrap">{{ $j->jam_pelaksanaan }}</td>
    <td class="{{ $subKolom }} text-center whitespace-nowrap">
        {{ $j->count_mhs_jadwal }}</td>
    <td class="{{ $subKolom }} text-center whitespace-nowrap">{{ $j->tanggal_pelaksanaan }}</td>


    @if ($kelas == null)
        <td class="{{ $mainKolom }} text-center">
            <flux:dropdown>
                <button class="cursor-pointer">
                    @include('livewire.global.table.badge.level-mk-badge', [
                        'xValue' => $j->kode_mk,
                        'sortir' => $j->rps_rel?->mk_rel?->level_mk,
                    ])
                </button>

                @include('livewire.all-role.kelas-management.kelas-toolbar-table', [
                    'x' => $j,
                    'editString' => 'editKelas',
                    'nameXString' => 'Kelas',
                    'confirmDeleteString' => 'deleteKelas',
                    'copyName' => 'Kode MK',
                    'copyText' => $j->kode_mk ?? '',
                ])

            </flux:dropdown>
        </td>
        <td class="{{ $subKolom }} text-center">{{ $j->semester ?? '-' }}</td>
        <td class="{{ $subKolom }} text-center whitespace-nowrap">{{ $j->sks ?? '-' }} SKS</td>
        <td class="{{ $subKolom }} text-center whitespace-nowrap">{{ $j->sks_text ?? '-' }}</td>

        <td class="{{ $secondKolom }} {{ $borderR }} {{ $borderL }} text-center">
            <flux:dropdown>
                <button class="cursor-pointer">
                    @include('livewire.global.table.badge.wajib-badge', [
                        'xValue' => $j->wajib_text,
                        'sortir' => $j->wajib,
                    ])
                </button>

                @include('livewire.all-role.kelas-management.kelas-toolbar-table', [
                    'x' => $j,
                    'editString' => 'editKelas',
                    'nameXString' => 'Kelas',
                    'confirmDeleteString' => 'deleteKelas',
                    'copyName' => 'Kode MK',
                    'copyText' => $j->kode_mk ?? '',
                ])

            </flux:dropdown>
        </td>
        <td class="{{ $secondKolom }} min-w-24">{{ $j->kelas_rel->pr_rel->prodi ?? '-' }} ({{ $j->kelas_rel->pr_rel->kode_pr ?? '---' }})</td>
    @endif
    @if (Auth::user()->admin || Auth::user()->dosen)
        <td class="{{ $mainKolom }} text-center">
            <flux:dropdown>
                <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                    inset="top bottom">
                </flux:button>

                @include('livewire.all-role.kelas-management.jadwal-management.jadwal-toolbar-table', [
                    'x' => $j,
                    'editString' => 'editJadwal',
                    'nameXString' => 'Jadwal',
                    'confirmDeleteString' => 'deleteJadwal',
                ])

            </flux:dropdown>
        </td>

        <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $j->created_day ?? '-' }}</td>
        <td class="{{ $secondKolom }} whitespace-nowrap text-center">{{ $j->updated_day ?? '-' }}</td>
    @endif
    </tr>
@empty
    <tr>
        @if ($kelas == null)
            <td colspan="{{ Auth::user()->admin || Auth::user()->dosen ? '19' : '16' }}"
                class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
            @else
            <td colspan="{{ Auth::user()->admin || Auth::user()->dosen ? '12' : '9' }}"
                class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
        @endif

        Tidak ada data Jadwal Kelas ditemukan!
        </td>
    </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
