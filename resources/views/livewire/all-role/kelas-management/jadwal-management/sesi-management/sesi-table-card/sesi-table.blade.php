<x-global.main-layout-table-alpine :noTrash="true">

    <x-slot:leftSecHead>
        <div
            class="w-full pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto shrink-0">
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'pertemuan_ke',
                'alpine' => 'sesi',
                'headString' => 'Pertemuan',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'total_absensi',
                'alpine' => 'sesi',
                'headString' => 'Absensi',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'metode',
                'alpine' => 'sesi',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'bobot',
                'alpine' => 'sesi',
            ])
        </div>
    </x-slot:leftSecHead>

    <x-slot:rightSecHead>
        <div class="w-full md:w-110 xl:w-124">
            <div class="col-start-1 row-start-1 w-full flex items-center justify-between gap-4">
                <div class="flex-shrink-0">
                    @include('livewire.global.search-and-filters.page-control', [
                        'perPageOptions' => [2, 4, 8, 16],
                        'alpine' => 'sesi',
                        'key' => 'page-control-sesi-table',
                        'withB' => 0,
                        'isSmall' => 1,
                    ])
                </div>
                <div class="flex-grow max-w-md">
                    @include('livewire.global.search-and-filters.main-search', [
                        'placeholder' => 'Cari Sesi Pertemuan Kelas...',
                        'alpine' => 'sesi',
                        'isLive' => 1,
                        'isBorder' => 2,
                    ])
                </div>
            </div>
        </div>
    </x-slot:rightSecHead>



    <x-slot:header>
        <div
            class="flex flex-col min-w-full w-max text-xs sm:text-sm font-semibold bg-[var(--main-table-color)] border-b table-border">

            {{-- WADAH INDUK (items-stretch memaksa semua kolom setinggi grup 2 baris) --}}
            <div class="flex flex-row items-stretch w-full">

                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'id',
                    'alpine' => 'sesi',
                    'isCenter' => 1,
                    'withDiv' => 1,
                    'rowSpan' => 1,
                    'divStyle' => 'w-32',
                ])

                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'metode',
                    'alpine' => 'sesi',
                    'isMain' => 1,
                    'isCenter' => 1,
                    'withDiv' => 1,
                    'rowSpan' => 1,
                    'divStyle' => 'w-42',
                ])

                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'pertemuan_ke',
                    'alpine' => 'sesi',
                    'headString' => 'Pertemuan',
                    'isCenter' => 1,
                    // 'isSticky' => 1,
                    'withDiv' => 1,
                    'rowSpan' => 1,
                    'divStyle' => 'w-48',
                ])

                {{-- Group 1: Informasi Sesi Kelas --}}
                <div class="flex flex-col table-border {{ $showMore ? 'shrink-0' : 'flex-1' }}">
                    <div class="table-head-sub-no-x border-l tracking-wide">
                        Informasi Sesi Kelas
                    </div>
                    <div class="flex flex-row items-stretch h-full">
                        @include('livewire.global.table.head-table', [
                            'sortFieldString' => 'hari_pelaksanaan',
                            'alpine' => 'sesi',
                            'headString' => 'Hari',
                            'isMain' => 1,
                            'isCenter' => 1,
                            'withDiv' => 1,
                            'divStyle' => 'w-32',
                        ])
                        @include('livewire.global.table.head-table', [
                            'sortFieldString' => 'jam_pelaksanaan',
                            'alpine' => 'sesi',
                            'headString' => 'Jam',
                            'isCenter' => 1,
                            'withDiv' => 1,
                            'divStyle' => 'w-42',
                        ])
                        @include('livewire.global.table.head-table', [
                            'sortFieldString' => 'total_absensi',
                            'alpine' => 'sesi',
                            'headString' => 'Absensi',
                            'isCenter' => 1,
                            'withDiv' => 1,
                            'divStyle' => 'w-32',
                        ])
                        @if ($showMore)
                            @include('livewire.global.table.head-table', [
                                'sortFieldString' => 'total_absensi_all',
                                'alpine' => 'sesi',
                                'headString' => 'Absensi Terdata',
                                'isCenter' => 1,
                                'withDiv' => 1,
                                'divStyle' => 'w-56',
                            ])
                        @endif
                        @include('livewire.global.table.head-table', [
                            'sortFieldString' => 'tanggal_pelaksanaan',
                            'alpine' => 'sesi',
                            'headString' => 'Tanggal',
                            'isCenter' => 1,
                            'withDiv' => 1,
                            'divStyle' => $showMore ? 'w-36' : 'flex-1 min-w-[120px]',
                        ])
                    </div>
                </div>
                @if (!$showMore)
                    @include('livewire.global.table.head-table', [
                        'sortFieldString' => 'bobot',
                        'alpine' => 'sesi',
                        'isBorderL' => 1,
                        'isCenter' => 1,
                        'withDiv' => 1,
                        'rowSpan' => 1,
                        'divStyle' => 'w-32',
                    ])
                @endif
                {{-- Group 2: Informasi Sub-CPMK --}}
                @if ($showMore)
                    <div class="flex flex-col table-border flex-1">
                        <div class="table-head-sub-no-x border-l tracking-wide">
                            Informasi Sub-CPMK
                        </div>
                        <div class="flex flex-row items-stretch h-full">
                            @include('livewire.global.table.head-table', [
                                'sortFieldString' => 'kode_scpmk',
                                'alpine' => 'sesi',
                                'headString' => 'Sub-CPMK',
                                'isMain' => 1,
                                'isCenter' => 1,
                                'withDiv' => 1,
                                'divStyle' => 'w-48',
                            ])
                            @include('livewire.global.table.head-table', [
                                'sortFieldString' => 'bobot',
                                'alpine' => 'sesi',
                                'isBorderR' => 1,
                                'isCenter' => 1,
                                'withDiv' => 1,
                                'divStyle' => 'w-32',
                            ])
                            @include('livewire.global.table.head-table', [
                                'sortFieldString' => 'tugas',
                                'alpine' => 'sesi',
                                'headString' => 'Deskripsi Tugas',
                                'withDiv' => 1,
                                'divStyle' => 'flex-1 w-[320px]',
                            ])
                            @include('livewire.global.table.head-table', [
                                'sortFieldString' => 'w_tugas',
                                'alpine' => 'sesi',
                                'headString' => 'W. Tugas',
                                'isCenter' => 1,
                                'withDiv' => 1,
                                'divStyle' => 'w-42',
                            ])
                            @include('livewire.global.table.head-table', [
                                'sortFieldString' => 'w_mandiri',
                                'alpine' => 'sesi',
                                'headString' => 'W. Mandiri',
                                'isCenter' => 1,
                                'withDiv' => 1,
                                'divStyle' => 'w-42',
                            ])
                            @include('livewire.global.table.head-table', [
                                'sortFieldString' => 'kode_cpmk',
                                'alpine' => 'sesi',
                                'headString' => 'CPMK',
                                'isBorderL' => 1,
                                'isCenter' => 1,
                                'withDiv' => 1,
                                'divStyle' => 'w-48',
                            ])
                        </div>
                    </div>
                @endif

                {{-- Kolom Aksi --}}
                <div class="table-head border-x w-20 flex items-center justify-center">
                    Aksi
                </div>

            </div>
        </div>
    </x-slot:header>

    {{-- BODY TABEL DENGAN LEBAR TERPERCAYA DAN PRESISI --}}
    @foreach ($sesis as $s)
        @php
            $isPastDate =
                !empty($s->tanggal) &&
                \Carbon\Carbon::parse($s->tanggal)->isPast() &&
                !\Carbon\Carbon::parse($s->tanggal)->isToday();
            // $isUjian = in_array(strtoupper($s->metode ?? ''), $daftarUjian);
            $kehadiran_mhs = Auth::user()->mahasiswa
                ? $s->kehadirans->where('mahasiswa_id', Auth::user()->mahasiswa->id)->first()
                : null;
        @endphp

        {{-- Layer 1: Visibilitas Murni (x-show) --}}
        <div x-show="filteredAndSortedIds.slice((currentPage - 1) * perPage, currentPage * perPage).some(item => Number(item.id) === Number({{ $s->id }}))"
            class="contents">

            {{-- Layer 2: CSS Order & Flexbox Layout --}}
            <div :style="'order: ' + filteredAndSortedIds.findIndex(entry => Number(entry.id) === Number(
                {{ $s->id }}))"
                wire:key="kelas-sesi-row-{{ $s->id }}"
                class="flex flex-row items-center min-w-full w-max hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200 border-b table-border text-xs sm:text-sm">

                <div class="w-32 shrink-0 text-center font-medium text-[var(--contrast-second-text)] truncate px-6">
                    {{ $s->id }}
                </div>

                <div class="table-main w-42 shrink-0 flex justify-center px-6">
                    <flux:dropdown>
                        <button class="cursor-pointer">
                            @include('livewire.global.table.badge.metode-badge', [
                                'xValue' => $s->metode,
                            ])
                        </button>
                        @include(
                            'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                            ['key' => 1]
                        )
                    </flux:dropdown>
                </div>

                <div class="w-48 shrink-0 text-center font-semibold truncate px-6">
                    P-{{ $s->pertemuan_ke }}

                    @if ($isPastDate)
                        <span class="ml-2 font-mono">
                            Selesai
                        </span>
                    @endif
                </div>

                <div class="table-main w-32 shrink-0 text-center whitespace-nowrap truncate px-6">
                    {{ $s->hari }}
                </div>

                <div class="table-sub w-42 shrink-0 text-center whitespace-nowrap truncate px-6">
                    {{ $s->jam_pelaksanaan }}
                </div>

                <div class="table-second w-32 shrink-0 text-center whitespace-nowrap truncate px-6">
                    {{ $s->total_absensi . ' / ' . ($s->count_mahasiswa ?? 0) }}
                </div>

                @if ($showMore)
                    <div class="table-sub w-56 shrink-0 text-center whitespace-nowrap truncate px-6">
                        {{ $s->total_absensi_all ?? 0 }}
                    </div>
                @endif

                <div
                    class="table-second {{ $showMore ? 'w-36 shrink-0' : 'flex-1 min-w-[140px]' }} text-center whitespace-nowrap truncate px-6">
                    {{ $s->tanggal_pelaksanaan }}
                </div>

                @if ($showMore)
                    <div class="table-main w-48 shrink-0 flex justify-center px-6">
                        <flux:dropdown>
                            <button class="cursor-pointer">
                                <flux:badge icon="academic-cap" color="fuchsia" size="sm">
                                    {{ $s->kode_scpmk ?? '---' }}
                                </flux:badge>
                            </button>
                            @include(
                                'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                                ['key' => 2, 'isSCPMK' => 1]
                            )
                        </flux:dropdown>
                    </div>
                @endif

                {{-- @if (!$showMore) --}}
                <div
                    class="{{ $showMore ? 'table-second' : 'table-sub table-border-l' }}  w-32 shrink-0 text-center whitespace-nowrap font-medium truncate px-6">
                    {{ $s->bobot_normalisasi ? $s->bobot_normalisasi . '%' : '-' }}
                </div>
                {{-- @endif --}}
                @if ($showMore)
                    <div class="table-sub w-[320px] truncate px-6" title="{{ $s->tugas }}">
                        {{ $s->tugas ?? '-' }}
                    </div>


                    <div class="table-second w-42 shrink-0 text-center whitespace-nowrap truncate px-6">
                        {{ $s->w_tugas ?? 0 }} menit
                    </div>

                    <div class="table-sub w-42 shrink-0 text-center whitespace-nowrap truncate px-6">
                        {{ $s->w_mandiri ?? 0 }} menit
                    </div>

                    <div class="table-second table-border-l w-48 shrink-0 flex justify-center px-6">
                        <flux:dropdown>
                            <button class="cursor-pointer">
                                <flux:badge icon="academic-cap" color="sky" size="sm">
                                    {{ $s->kode_cpmk ?? '---' }}
                                </flux:badge>
                            </button>
                            @include(
                                'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                                ['key' => 3, 'isCPMK' => 1]
                            )
                        </flux:dropdown>
                    </div>
                @endif

                <div class="table-main w-20 shrink-0 flex justify-center px-2">
                    <flux:dropdown>
                        <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                            inset="top bottom">
                        </flux:button>
                        @include(
                            'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                            ['key' => 4]
                        )
                    </flux:dropdown>
                </div>

            </div>
        </div>
        {{-- @empty
                <div class="w-full text-center p-8 text-[var(--contrast-second-text)]">
                    Tidak ada data Sesi Pertemuan Kelas ditemukan!
                </div> --}}
    @endforeach

    <x-slot:emptys>
        <div x-show="totalFilteredItems === 0" class="w-full text-center px-12 py-5 text-[var(--contrast-second-text)]">
            Tidak ada data Sesi Pertemuan Kelas ditemukan!
        </div>
    </x-slot:emptys>

    <x-slot:footer>
        @include('livewire.global.table.pagination-alpine')
    </x-slot:footer>

</x-global.main-layout-table-alpine>
