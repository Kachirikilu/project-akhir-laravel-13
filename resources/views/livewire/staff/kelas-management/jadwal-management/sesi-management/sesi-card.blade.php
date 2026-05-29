<x-global.main-layout-card :paginator="$sesis">

    {{-- 1. Isi bagian Sortir --}}
    <x-slot:sortir>
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
        @include('livewire.global.table.head-sortir', ['sortFieldString' => 'metode'])
    </x-slot:sortir>
    <x-slot:search>
        <div class="w-full md:w-96 xl:w-108">
            <div class="col-start-1 row-start-1 w-full">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Sesi Pertemuan Kelas...',
                    'isLive' => 1,
                    'isBorder' => 2,
                ])
            </div>
        </div>
    </x-slot:search>


    {{-- 2. Isi Utama (Looping Card) masuk ke Default Slot --}}
    @forelse($sesis as $s)
        @php
            $daftarUjian = array_merge(config('app.uts_fields'), config('app.uas_fields'));
            $isUjian = in_array(strtoupper($s->metode), $daftarUjian);
        @endphp

        {{-- CARD ITEM --}}
        <div wire:key="kelas-{{ $s->id }}" data-kelas-id="{{ $s->id }}" x-data="{ expanded: {{ $isUjian ? 'true' : 'false' }} }"
            {{-- Klik area kartu untuk toggle expand (Kecuali jika UTS/UAS) --}} @if (!$isUjian) @click="expanded = !expanded" @endif
            class="{{ $isUjian ? 'lg:col-span-2 ring-1 ring-amber-500/30 bg-gradient-to-r from-[var(--main-table-trans)] to-amber-500/5' : 'cursor-pointer select-none' }} relative flex flex-col self-start p-3 rounded-xl border border-[var(--border-table-color)] bg-[var(--main-table-trans)] shadow-sm hover:shadow-md transition-all duration-300">

            {{-- CARD HEADER --}}
            <div class="flex items-start justify-between gap-4 pb-3 border-b border-[var(--border-table-color)]"
                @click.stop>
                <div class="flex items-center gap-3">
                    {{-- Badge Pertemuan --}}
                    <x-label-card type="sm">
                        P-{{ $s->pertemuan_ke }}
                    </x-label-card>

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
                                            <flux:badge icon="information-circle" color="zinc" size="sm" variant="pill">
                                                {{ $s->metode ?? '-' }}</flux:badge>
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

                <div class="flex items-center">
                    <flux:dropdown>
                        <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                            inset="top bottom" />
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

            {{-- CARD BODY --}}
            <div class="flex flex-col flex-1 justify-between mt-3 text-sm">

                {{-- 1. INFORMASI SESI (SELALU MUNCUL DI AWAL) --}}
                <div
                    class="p-3 rounded-lg bg-[var(--second-table-trans)] border border-[var(--border-table-color)]/30 space-y-3">

                    <span class="text-xs font-bold uppercase tracking-wide text-[var(--focus-color)] block">
                        Informasi Sesi
                    </span>

                    <div class="flex flex-col gap-2 text-xs text-[var(--contrast-main-text)]">

                        <div
                            class="flex items-center justify-between rounded-md bg-[var(--main-table-color)]/40 px-3 py-2">
                            <span class="text-[var(--contrast-second-text)]">
                                Waktu
                            </span>
                            <span class="font-medium text-right">
                                {{ $s->hari }}, {{ $s->jam_pelaksanaan }}
                            </span>
                        </div>

                        <div
                            class="flex items-center justify-between rounded-md bg-[var(--main-table-color)]/40 px-3 py-2">
                            <span class="text-[var(--contrast-second-text)]">
                                Tanggal
                            </span>
                            <span class="font-medium text-right">
                                {{ $s->tanggal_pelaksanaan }}
                            </span>
                        </div>

                        <div
                            class="flex items-center justify-between rounded-md bg-[var(--main-table-color)]/40 px-3 py-2">
                            <span class="text-[var(--contrast-second-text)]">
                                Absensi
                            </span>
                            <span class="font-medium text-[var(--focus-color)] text-right">
                                {{ $s->mhs_absensi ?? 0 }} / {{ $s->count_mahasiswa }}
                            </span>
                        </div>

                    </div>
                </div>

                {{-- INTERACTIVE DROPDOWN AREA --}}
                <div x-show="expanded" x-collapse class="mt-2 transition-all duration-300">

                    <div class="grid grid-cols-1 {{ $isUjian ? 'sm:grid-cols-3' : '' }} gap-2">

                        {{-- SUB-CPMK --}}
                        <div @click.stop
                            class="{{ $isUjian ? 'sm:col-span-2' : 'sm:col-span-1' }} p-3 rounded-xl bg-[var(--sub-table-trans)] border border-[var(--border-table-color)]/30 space-y-3">

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-bold uppercase tracking-wide text-[var(--focus-color)]">
                                    Sub-CPMK & Bobot
                                </span>

                                <span
                                    class="text-xs px-2 py-1 rounded-lg font-semibold bg-[var(--main-table-color)] border border-[var(--border-table-color)]">
                                    Bobot: {{ $s->bobot ? $s->bobot . '%' : '-' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-2 flex-wrap">
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

                                <x-button-action color="blue"
                                    @click="
                                    $store.sesi?.setEdit(0);
                                    $store.sesi?.setColor('text-blue-700 dark:text-blue-400');
                                    $flux.modal('sesi-absen').show();
                                    $store.sesi?.setValueAbsenSesi(
                                        '{{ $s->id ?? '' }}',
                                        '{{ $s->pertemuan_ke ?? '' }}',
                                    );
                                    ">
                                    <flux:icon name="user-plus" class="w-3.5 h-3.5" />
                                    <span>Absensi</span>
                                </x-button-action>
                            </div>

                            <div
                                class="pt-2 border-t border-[var(--border-table-color)]/30 text-xs flex flex-wrap items-center gap-2 text-[var(--contrast-second-text)]">

                                <span>Waktu Tugas:</span>
                                <span class="font-semibold text-[var(--contrast-main-text)]">
                                    {{ $s->w_tugas ?? 0 }} menit
                                </span>

                                <span>•</span>

                                <span>Mandiri:</span>
                                <span class="font-semibold text-[var(--contrast-main-text)]">
                                    {{ $s->w_mandiri ?? 0 }} menit
                                </span>
                            </div>
                        </div>

                        {{-- DESKRIPSI --}}
                        <div
                            class="{{ $isUjian ? 'sm:col-span-1' : 'sm:col-span-1' }}
                                        p-3 rounded-xl bg-[var(--second-table-trans)]
                                        border border-[var(--border-table-color)]/30">

                            <span
                                class="text-xs font-bold uppercase tracking-wide text-[var(--contrast-second-text)] block mb-2">
                                Deskripsi Tugas / Evaluasi
                            </span>

                            <p class="text-xs leading-relaxed text-[var(--contrast-main-text)]">
                                {{ $s->tugas ?? 'Tidak ada deskripsi tugas spesifik untuk sesi ini.' }}
                            </p>
                        </div>

                    </div>
                </div>

                {{-- INDIKATOR PENUNJUK / TOGGLE BUTTON (Disembunyikan jika UTS/UAS) --}}
                @if (!$isUjian)
                    <div
                        class="flex justify-center mt-2 pt-1.5 border-t border-dashed border-[var(--border-table-color)]/20">
                        <div
                            class="flex items-center gap-1 text-[10px] font-medium text-[var(--contrast-second-text)] transition-colors duration-150">
                            <span
                                x-text="expanded ? 'Klik untuk merapatkan' : 'Klik kartu untuk detail (Sub-CPMK & Tugas)'"></span>
                            <flux:icon name="chevron-down" class="w-3 h-3 transition-transform duration-300"
                                ::class="{ 'rotate-180': expanded }" />
                        </div>
                    </div>
                @endif

            </div>
        </div>
        @empty
            <div
                class="col-span-6 text-center p-12 rounded-xl border border-dashed border-[var(--border-table-color)] bg-[var(--main-table-trans)]">
                <flux:icon name="information-circle" class="mx-auto h-8 w-8 text-[var(--contrast-second-text)] mb-2" />
                <p class="text-sm text-[var(--contrast-second-text)]">Tidak ada data Sesi Pertemuan Kelas ditemukan!
                </p>
            </div>
        @endforelse

    </x-global.main-layout-card>
