<div class="space-y-6">

    <div class="scrollbar-thin overflow-x-auto flex items-center justify-between border-b border-[var(--border-table-color)] pb-1 mb-4">
        <div class="flex flex-row items-center gap-2 mb-2">
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'pertemuan_ke',
                'headString' => 'Pertemuan',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'jumlah_absensi',
                'headString' => 'Absensi',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'tanggal_pelaksanaan',
                'headString' => 'Tanggal',
            ])
             @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'metode',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'kode_scpmk',
                'headString' => 'Sub-CPMK',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'bobot',
            ])
        </div>
            <div class="hidden sm:block flex items-center ml-2">
            {{-- <flux:icon name="funnel" class="h-3.5 w-3.5" />
            <span>Sortir</span> --}}
            @if ($sesis->hasPages())
                <div class="p-4" id="pagination-links-container" wire:target="{{ $sesis->getPageName() }}">
                    {{ $sesis->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>
    {{-- CONTAINER GRID UTAMA --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5"
                wire:loading.class="opacity-50 pointer-events-none transition-opacity"
                wire:target="search, perPage, loadingTable, sortBy">
        @forelse($sesis as $s)
            @php
                $daftarUjian = array_merge(config('app.uts_fields'), config('app.uas_fields'));
                $isUjian = in_array(strtoupper($s->metode), $daftarUjian);
            @endphp

            {{-- CARD ITEM --}}
            <div wire:key="kelas-{{ $s->id }}" data-kelas-id="{{ $s->id }}"
                class="{{ $isUjian ? 'lg:col-span-2 ring-1 ring-amber-500/30 bg-gradient-to-r from-[var(--main-table-trans)] to-amber-500/5' : '' }} relative flex flex-col justify-between p-3 rounded-xl border border-[var(--border-table-color)] bg-[var(--main-table-trans)] shadow-sm hover:shadow-md transition-all duration-200">

                {{-- CARD HEADER --}}
                <div class="flex items-start justify-between gap-4 pb-3 border-b border-[var(--border-table-color)]">
                    <div class="flex items-center gap-3">
                        {{-- Badge Pertemuan --}}
                        <span
                            class="flex items-center justify-center h-6 w-9 text-xs font-bold rounded-lg bg-[var(--second-table-trans)] text-[var(--focus-color)] ring-1 ring-[var(--border-table-color)]">
                            P-{{ $s->pertemuan_ke }}
                        </span>
                        <div class="flex items-center gap-3">
                            <div>
                                <flux:dropdown>
                                    <button class="cursor-pointer focus:outline-none">
                                        @switch($s->metode)
                                            @case('Teori')
                                                <flux:badge icon="book-open" color="emerald" size="sm" variant="pill">Teori
                                                </flux:badge>
                                            @break

                                            @case('Praktik')
                                                <flux:badge icon="beaker" color="cyan" size="sm" variant="pill">Praktik
                                                </flux:badge>
                                            @break

                                            @case('Tugas')
                                                <flux:badge icon="pencil-square" color="blue" size="sm" variant="pill">
                                                    Tugas</flux:badge>
                                            @break

                                            @case('UTS')
                                            @case('UAS')
                                                <flux:badge icon="clipboard-document-check" color="amber" size="sm"
                                                    variant="pill">{{ $s->metode }}</flux:badge>
                                            @break

                                            @case('Hasil Proyek')
                                                <flux:badge icon="light-bulb" color="indigo" size="sm" variant="pill">Hasil
                                                    Proyek</flux:badge>
                                            @break

                                            @case('Kerja Praktek')
                                                <flux:badge icon="briefcase" color="violet" size="sm" variant="pill">Kerja
                                                    Praktek</flux:badge>
                                            @break

                                            @case('Skripsi')
                                                <flux:badge icon="academic-cap" color="fuchsia" size="sm" variant="pill">
                                                    Skripsi</flux:badge>
                                            @break

                                            @case('Aktivitas Partisipasif')
                                                <flux:badge icon="user-group" color="rose" size="sm" variant="pill">
                                                    Partisipasif</flux:badge>
                                            @break

                                            @case('Mandiri')
                                                <flux:badge icon="user" color="slate" size="sm" variant="pill">Mandiri
                                                </flux:badge>
                                            @break

                                            @default
                                                <flux:badge icon="information-circle" color="zinc" size="sm"
                                                    variant="pill">{{ $s->metode ?? '-' }}</flux:badge>
                                        @endswitch
                                    </button>

                                    @include(
                                        'livewire.staff.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                                        [
                                            'x' => $s,
                                            'editString' => 'editSesi',
                                            'nameXString' => 'Sesi',
                                            'confirmDeleteString' => 'deleteSesi',
                                            'copyName' => 'Kode Sub-CPMK',
                                            'copyText' => $s->kode_scpmk ?? '',
                                        ]
                                    )
                                </flux:dropdown>
                            </div>

                            {{-- 2. ID Sesi & Penanda Ujian (Tengah menuju Kanan) --}}
                            <div class="flex items-center gap-3">
                                @if ($isUjian)
                                    <span
                                        class="text-xs font-semibold uppercase tracking-wider text-amber-500 animate-pulse">Sesi
                                        Evaluasi Utama</span>
                                @endif
                                <span class="text-xs text-[var(--contrast-second-text)] font-mono">ID:
                                    {{ $s->id }}</span>
                            </div>

                        </div>
                    </div>

                    {{-- Tombol Aksi Dropdown Kanan --}}
                    <div class="flex items-center">
                        <flux:dropdown>
                            <flux:button class="cursor-pointer" variant="ghost" size="sm"
                                icon="ellipsis-horizontal" inset="top bottom" />
                            @include(
                                'livewire.staff.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                                [
                                    'x' => $s,
                                    'editString' => 'editSesi',
                                    'nameXString' => 'Sesi',
                                    'confirmDeleteString' => 'deleteSesi',
                                    'copyName' => 'Kode Sub-CPMK',
                                    'copyText' => $s->kode_scpmk ?? '',
                                ]
                            )
                        </flux:dropdown>
                    </div>
                </div>

                {{-- CARD BODY (GRID INTERNAL DATA) --}}
                <div class="grid grid-cols-1 {{ $isUjian ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }} gap-2 mt-3 text-sm">

                    {{-- Blok 1: Informasi Sesi Kelas --}}
                    <div
                        class="p-3 rounded-lg bg-[var(--second-table-trans)] space-y-2 border border-[var(--border-table-color)]/30">
                        <span
                            class="text-xs font-bold uppercase tracking-wide text-[var(--focus-color)] block">Informasi
                            Sesi</span>
                        <div class="grid gap-y-2 text-xs text-[var(--contrast-main-text)]">
                            <div class="flex flex-wrap items-baseline gap-x-1">
                                <span class="text-[var(--contrast-second-text)] col-span-1">Waktu:</span>
                                <span class="font-medium text-right sm:text-left col-span-3">{{ $s->hari }},
                                    {{ $s->jam_pelaksanaan }}</span>
                            </div>
                            <div class="flex flex-wrap items-baseline gap-x-1">
                                <span class="text-[var(--contrast-second-text)] col-span-1">Tanggal:</span>
                                <span
                                    class="font-medium text-right sm:text-left col-span-3">{{ $s->tanggal_pelaksanaan }}</span>
                            </div>
                            <div class="flex flex-wrap items-baseline gap-x-1">
                                <span class="text-[var(--contrast-second-text)] col-span-1">Absensi:</span>
                                <span
                                    class="font-medium text-right sm:text-left text-[var(--focus-color)] col-span-3">{{ $s->mhs_absensi }}
                                    / {{ $s->count_mahasiswa }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Blok 2: Informasi Sub-CPMK --}}
                    <div
                        class="p-3 rounded-lg bg-[var(--sub-table-trans)] space-y-2 border border-[var(--border-table-color)]/30">
                        <span class="text-xs font-bold uppercase tracking-wide text-[var(--focus-color)] block">Sub-CPMK
                            & Bobot</span>
                        <div class="flex flex-wrap items-center justify-between sm:justify-start gap-x-2 gap-y-1.5">
                            {{-- Dropdown / Kode Sub-CPMK --}}
                            <flux:dropdown>
                                <button class="cursor-pointer focus:outline-none">
                                    <flux:badge icon="academic-cap" color="fuchsia" size="sm">
                                        {{ $s->kode_scpmk ?? '---' }}
                                    </flux:badge>
                                </button>
                                @include(
                                    'livewire.staff.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                                    [
                                        'x' => $s,
                                        'editString' => 'editSesi',
                                        'nameXString' => 'Sesi',
                                        'confirmDeleteString' => 'deleteSesi',
                                        'copyName' => 'Kode Sub-CPMK',
                                        'copyText' => $s->kode_scpmk ?? '',
                                    ]
                                )
                            </flux:dropdown>

                            {{-- Bobot --}}
                            <span
                                class="text-xs px-2 py-0.5 font-semibold rounded bg-[var(--main-table-color)] text-[var(--contrast-main-text)] border border-[var(--border-table-color)]">
                                Bobot: {{ $s->bobot ? $s->bobot . '%' : '-' }}
                            </span>
                        </div>
                        <div
                            class="text-xs pt-1 border-t border-[var(--border-table-color)]/40 text-[var(--contrast-second-text)]">
                            <div class="flex flex-wrap items-baseline gap-x-1">
                                <span>W. Tugas/Mandiri:</span>
                                <b class="text-[var(--contrast-main-text)] whitespace-nowrap inline-block">
                                    {{ $s->w_tugas ?? 0 }}m
                                    <span class="text-[var(--contrast-second-text)] font-normal">/</span>
                                    {{ $s->w_mandiri ?? 0 }}m
                                </b>
                            </div>
                        </div>
                    </div>

                    {{-- Blok 3: Deskripsi Tugas (Jika UTS/UAS, bagian ini akan mendapat ruang proporsional) --}}
                    <div
                        class="p-3 rounded-lg bg-[var(--second-table-trans)] space-y-1 border border-[var(--border-table-color)]/30 {{ $isUjian ? 'sm:col-span-1' : 'sm:col-span-2' }}">
                        <span
                            class="text-xs font-bold uppercase tracking-wide text-[var(--contrast-second-text)] block">Deskripsi
                            Tugas / Evaluasi</span>
                        <p
                            class="text-xs text-[var(--contrast-main-text)] leading-relaxed line-clamp-3 hover:line-clamp-none transition-all duration-300">
                            {{ $s->tugas ?? 'Tidak ada deskripsi tugas spesifik untuk sesi ini.' }}
                        </p>
                    </div>

                </div>
            </div>
            @empty
                <div
                    class="col-span-1 lg:col-span-2 text-center p-12 rounded-xl border border-dashed border-[var(--border-table-color)] bg-[var(--main-table-trans)]">
                    <flux:icon name="information-circle" class="mx-auto h-8 w-8 text-[var(--contrast-second-text)] mb-2" />
                    <p class="text-sm text-[var(--contrast-second-text)]">Tidak ada data Sesi Pertemuan Kelas ditemukan!</p>
                </div>
            @endforelse
        </div>

        {{-- FOOTER / PAGINASI DATA --}}
        <div class="mt-4 pt-4 border-t border-[var(--border-table-color)]">
            @include('livewire.global.table.footer-table', [
                'typeXString' => $sesis,
            ])
        </div>
    </div>
