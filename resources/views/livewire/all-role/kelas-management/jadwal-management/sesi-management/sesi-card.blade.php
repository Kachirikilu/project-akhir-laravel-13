@php
    $daftarUjian = array_merge(config('app.uts_fields'), config('app.uas_fields'));

    $alpineData = $sesis
        ->map(function ($s, $index) use ($daftarUjian) {
            $stringKodeSCPMK = $s->kode_scpmk ?? '';
            $stringKodeCPMK = $s->kode_cpmk ?? '';
            $p = (int) $s->pertemuan_ke;

            $bobotRaw = $s->bobot_normalisasi ?? '';
            $bobotClean = str_replace(',', '.', $bobotRaw);

            return [
                'id' => $s->id,
                'dbIndex' => $index,
                'pertemuan_ke' => $p,
                'jumlah_absensi' => (int) ($s->jumlah_absensi ?? 0),
                'tanggal_pelaksanaan' => $s->tanggal_pelaksanaan ?? '',
                'metode' => strtolower($s->metode ?? ''),
                'tugas' => strtolower($s->tugas ?? ''),
                'kode_scpmk' => strtolower($stringKodeSCPMK),
                'kode_cpmk' => strtolower($stringKodeCPMK),
                'searchKodeSCPMK' => preg_replace('/[^A-Za-z0-9]/', '', strtolower($stringKodeSCPMK)),
                'searchPertemuan' => [
                    (string) $p,
                    'p' . $p,
                    'p-' . $p,
                    'pertemuan' . $p,
                    'pertemuan ' . $p,
                    'ke' . $p,
                    'ke-' . $p,
                ],
                'bobot' => [
                    $bobotClean,
                    str_replace('.', ',', $bobotClean),
                    $bobotClean . '%',
                    str_replace('.', ',', $bobotClean) . '%',
                ],
            ];
        })
        ->values()
        ->toArray();
@endphp
<div x-data="{
    rawItems: {{ json_encode($alpineData) }},
    currentPage: 1,
    perPage: 8,
    sortField: '',
    sortDirection: 'asc',

    get filteredAndSortedIds() {
        let query = (this.$store.sesi?.search || '').toLowerCase().trim();
        let cleanQuery = query.replace(/[^a-z0-9]/g, '');
        let dotQuery = query.replace(',', '.');

        let filtered = this.rawItems.filter(item => {
            if (!query) return true;

            if (String(item.metode).includes(query) || String(item.tugas).includes(query)) {
                return true;
            }

            if (item.kode_scpmk.includes(query) || (cleanQuery && item.searchKodeSCPMK.includes(cleanQuery))) {
                return true;
            }

            if (item.kode_cpmk.includes(query) || (cleanQuery && item.searchKodeCPMK.includes(cleanQuery))) {
                return true;
            }

            let cocokPertemuan = item.searchPertemuan.some(pText => pText === query || pText.includes(query));
            if (cocokPertemuan) return true;

            let cocokBobot = item.bobot.some(bText => bText === query || bText === dotQuery || bText.includes(query) || bText.includes(dotQuery));
            if (cocokBobot) return true;

            return false;
        });

        let field = this.$store.sesi?.sortField || this.sortField;
        let direction = (this.$store.sesi?.sortDirection || this.sortDirection) === 'desc' ? -1 : 1;

        if (field) {
            filtered.sort((a, b) => {
                let valA = a[field];
                let valB = b[field];
                if (typeof valA === 'number' && typeof valB === 'number') return (valA - valB) * direction;
                return String(valA).localeCompare(String(valB), undefined, { numeric: true, sensitivity: 'base' }) * direction;
            });
        } else {
            filtered.sort((a, b) => a.dbIndex - b.dbIndex);
        }

        return filtered;
    },

    get itemVisibilityMap() {
        let map = {};
        this.filteredAndSortedIds.forEach((item, visualIndex) => {
            let start = (this.currentPage - 1) * this.perPage;
            let end = start + this.perPage;

            map[item.id] = {
                visible: visualIndex >= start && visualIndex < end,
                order: visualIndex
            };
        });
        return map;
    },

    get totalFilteredItems() {
        return this.filteredAndSortedIds.length;
    },
    get totalPages() {
        return Math.ceil(this.totalFilteredItems / this.perPage) || 1;
    },
    init() {
        this.$watch('$store.sesi.search', () => { this.currentPage = 1; });
        this.$watch('$store.sesi.perPage', (val) => {
            this.perPage = val || 8;
            this.currentPage = 1;
        });
    }
}" class="w-full">

    <x-global.main-layout-card>

        {{-- Slot Sortir --}}
        <x-slot:sortir>
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'pertemuan_ke',
                'alpine' => 'sesi',
                'headString' => 'Pertemuan',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'jumlah_absensi',
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
        </x-slot:sortir>

        {{-- Slot Search --}}
        <x-slot:search>
            <div class="w-full md:w-96 xl:w-108">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Sesi Pertemuan Kelas...',
                    'alpine' => 'sesi',
                    'isLive' => 1,
                    'isBorder' => 2,
                ])
            </div>
        </x-slot:search>

        {{-- GRID UTAMA KARTU --}}
        @foreach ($sesis as $index => $s)
            @php
                $isUjian = in_array(strtoupper($s->metode ?? ''), $daftarUjian);
                $kehadiran_mhs = Auth::user()->mahasiswa
                    ? $s->kehadirans->where('mahasiswa_id', Auth::user()->mahasiswa->id)->first()
                    : null;
            @endphp

            <template x-if="itemVisibilityMap[{{ $s->id }}]?.visible">
                {{-- 2. CARD ITEM CONTAINER --}}
                <div wire:key="kelas-sesi-card-{{ $s->id }}" x-data="{ expanded: {{ $isUjian ? 'true' : 'false' }} }"
                    :style="'order: ' + (itemVisibilityMap[{{ $s->id }}]?.order ?? 999)"
                    @click="expanded = !expanded"
                    class="card-sesi-item relative flex flex-col self-start p-3 rounded-xl border border-[var(--border-table-color)] bg-[var(--main-table-trans)] shadow-sm hover:shadow-md transition-all duration-300 {{ $isUjian ? 'lg:col-span-2 ring-1 ring-amber-500/30 bg-gradient-to-r from-[var(--main-table-trans)] to-amber-500/5' : 'cursor-pointer select-none' }}">

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
                                            @include('livewire.global.table.badge.metode-badge', [
                                                'xValue' => $s->metode,
                                            ])
                                        </button>

                                        @include(
                                            'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
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
                                <flux:button class="cursor-pointer" variant="ghost" size="sm"
                                    icon="ellipsis-horizontal" inset="top bottom" />
                                @include(
                                    'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
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

                        {{-- 1. INFORMASI SESI --}}
                        <div
                            class="p-3 rounded-lg bg-[var(--second-table-trans)] border border-[var(--border-table-color)]/30 space-y-3">
                            <span
                                class="text-xs font-bold uppercase tracking-wide text-[var(--focus-color)] block">Informasi
                                Sesi</span>
                            <div class="flex flex-col gap-2 text-xs text-[var(--contrast-main-text)]">
                                <div
                                    class="flex items-center justify-between rounded-md bg-[var(--main-table-color)]/40 px-3 py-2">
                                    <span class="text-[var(--contrast-second-text)]">Waktu</span>
                                    <span class="font-medium text-right">{{ $s->hari }},
                                        {{ $s->jam_pelaksanaan }}</span>
                                </div>
                                <div
                                    class="flex items-center justify-between rounded-md bg-[var(--main-table-color)]/40 px-3 py-2">
                                    <span class="text-[var(--contrast-second-text)]">Tanggal</span>
                                    <span class="font-medium text-right">{{ $s->tanggal_pelaksanaan }}</span>
                                </div>
                                <div
                                    class="flex items-center justify-between rounded-md bg-[var(--main-table-color)]/40 px-3 py-2">
                                    <span class="text-[var(--contrast-second-text)]">Absensi</span>
                                    <span
                                        class="font-medium text-[var(--focus-color)] text-right">{{ $s->mhs_absensi ?? 0 }}
                                        / {{ $s->count_mahasiswa }}</span>
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
                                        <span
                                            class="text-xs font-bold uppercase tracking-wide text-[var(--focus-color)]">Sub-CPMK
                                            & Bobot</span>
                                        <span
                                            class="text-xs px-2 py-1 rounded-lg font-semibold bg-[var(--main-table-color)] border border-[var(--border-table-color)]">
                                            Bobot: {{ $s->bobot_normalisasi ? $s->bobot_normalisasi . '%' : '-' }}
                                        </span>
                                    </div>

                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between gap-2 flex-wrap pb-1">
                                            <flux:dropdown>
                                                <button class="cursor-pointer focus:outline-none group">
                                                    <flux:badge icon="academic-cap" color="fuchsia" size="sm"
                                                        class="group-hover:opacity-80 transition">
                                                        {{ $s->kode_scpmk ?? '---' }}
                                                    </flux:badge>
                                                </button>
                                                @include(
                                                    'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
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

                                            @if (Auth::user()->mahasiswa)
                                                <div x-data="{
                                                    sekarang: '',
                                                    mulai: '{{ $s->waktu_pelaksanaan }}',
                                                    dispensasi: '{{ $s->waktu_dispensasi }}',
                                                    getWaktuLokal() {
                                                        let d = new Date();
                                                        let tzOffset = d.getTimezoneOffset() * 60000;
                                                        return new Date(d.getTime() - tzOffset).toISOString().slice(0, 16);
                                                    },
                                                    init() {
                                                        this.sekarang = this.getWaktuLokal();
                                                        setInterval(() => { this.sekarang = this.getWaktuLokal(); }, 10000);
                                                    }
                                                }">
                                                    <template x-if="sekarang >= mulai && sekarang <= dispensasi">
                                                        <x-button-action color="blue" size="sm"
                                                            @click="
                                                        $store.sesi?.setEdit(0);
                                                        $store.sesi?.setColor('text-blue-700 dark:text-blue-400');
                                                        $flux.modal('sesi-absen').show();
                                                        $store.sesi?.setValueAbsenSesi(
                                                            '{{ $s->id ?? '' }}', '{{ $s->pertemuan_ke ?? '' }}',
                                                            '{{ $s->waktu_pelaksanaan ?? '' }}', '{{ $s->waktu_berakhir ?? '' }}',
                                                            '{{ $s->waktu_telat ?? '' }}', '{{ $s->waktu_dispensasi ?? '' }}'
                                                        );
                                                    ">
                                                            <flux:icon name="user-plus" class="w-3.5 h-3.5" />
                                                            <span>Absensi</span>
                                                        </x-button-action>
                                                    </template>
                                                </div>
                                            @endif
                                        </div>

                                        @if (Auth::user()->mahasiswa)
                                            <div
                                                class="pt-3 border-t border-zinc-200 dark:border-zinc-700 flex flex-col gap-2 text-sm">
                                                <span class="text-zinc-500 dark:text-zinc-400 font-medium">Status Absen
                                                    Anda:</span>
                                                <div class="w-full">
                                                    @if ($kehadiran_mhs)
                                                        @php
                                                            $badgeColor = match ($kehadiran_mhs->status) {
                                                                'Hadir' => 'green',
                                                                'Terlambat' => 'amber',
                                                                'Dispensasi' => 'blue',
                                                                'Sakit', 'Izin' => 'indigo',
                                                                default => 'red',
                                                            };
                                                        @endphp
                                                        <div class="flex flex-col items-start gap-1.5">
                                                            <div
                                                                class="flex items-center justify-between gap-2 flex-wrap pb-1 w-full">
                                                                <flux:badge color="{{ $badgeColor }}" size="sm"
                                                                    inset-top-bottom>
                                                                    {{ $kehadiran_mhs->status }}
                                                                </flux:badge>
                                                                <span
                                                                    class="text-xs text-zinc-400 dark:text-zinc-500 flex items-center gap-1">
                                                                    <flux:icon name="clock"
                                                                        class="w-3.5 h-3.5 inline" />
                                                                    {{ $kehadiran_mhs->waktu_presensi?->format('H:i') }}
                                                                    WIB
                                                                </span>
                                                            </div>
                                                            @if ($kehadiran_mhs->keterangan)
                                                                <span
                                                                    class="text-xs text-zinc-500 dark:text-zinc-400 italic bg-zinc-50 dark:bg-zinc-800/50 p-2 rounded-md border border-zinc-100 dark:border-zinc-800/80 w-full block mt-0.5">
                                                                    <strong
                                                                        class="not-italic text-zinc-600 dark:text-zinc-300 block mb-0.5 text-[11px] uppercase tracking-wider">Keterangan:</strong>
                                                                    {{ $kehadiran_mhs->keterangan }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <flux:badge color="zinc" size="sm" class="opacity-70">
                                                            Belum Presensi</flux:badge>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div
                                        class="pt-2 border-t border-[var(--border-table-color)]/30 text-xs flex flex-wrap items-center gap-2 text-[var(--contrast-second-text)]">
                                        <span>Waktu Tugas:</span>
                                        <span
                                            class="font-semibold text-[var(--contrast-main-text)]">{{ $s->w_tugas ?? 0 }}
                                            menit</span>
                                        <span>•</span>
                                        <span>Mandiri:</span>
                                        <span
                                            class="font-semibold text-[var(--contrast-main-text)]">{{ $s->w_mandiri ?? 0 }}
                                            menit</span>
                                    </div>
                                </div>

                                {{-- DESKRIPSI --}}
                                <div
                                    class="p-3 rounded-xl bg-[var(--second-table-trans)] border border-[var(--border-table-color)]/30">
                                    <span
                                        class="text-xs font-bold uppercase tracking-wide text-[var(--contrast-second-text)] block mb-2">Deskripsi
                                        Tugas / Evaluasi</span>
                                    <p class="text-xs leading-relaxed text-[var(--contrast-main-text)]">
                                        {{ $s->tugas ?? 'Tidak ada deskripsi tugas spesifik untuk sesi ini.' }}
                                    </p>
                                </div>

                            </div>
                        </div>

                        {{-- TOGGLE INDIKATOR FOOTER --}}
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
            </template>
        @endforeach

        {{-- EMPTY STATE ANCHOR --}}
        <div x-show="totalFilteredItems === 0"
            class="col-span-6 text-center p-12 rounded-xl border border-dashed border-[var(--border-table-color)] bg-[var(--main-table-trans)]">
            <p class="text-sm text-[var(--contrast-second-text)]">Tidak ada data Sesi Pertemuan Kelas ditemukan!</p>
        </div>

        {{-- Slot Footer Pagination --}}
        <x-slot:footer>
            @include('livewire.global.table.pagination-alpine')
            @include('livewire.global.table.trash-delete')
        </x-slot:footer>

    </x-global.main-layout-card>
</div>
