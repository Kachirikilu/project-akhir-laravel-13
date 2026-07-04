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
                'total_absensi' => (int) ($s->total_absensi ?? 0),
                'tanggal_pelaksanaan' => $s->tanggal_pelaksanaan ?? '',
                'tanggal' => $s->tanggal ?? '',
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

    $jsonFreshData = json_encode($alpineData);

    /*
    |--------------------------------------------------------------------------
    | PERUBAHAN
    |--------------------------------------------------------------------------
    */
    $alpineVersion = md5(
        json_encode(
            $sesis
                ->map(
                    fn($s) => [
                        'id' => $s->id,
                        'updated_at' => optional($s->updated_at)->timestamp,
                    ],
                )
                ->values(),
        ),
    );
@endphp
<div wire:key="sesi-wrapper-{{ $alpineVersion }}" x-data="{
    rawItems: [],
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

            let metode = String(item.metode || '').toLowerCase();
            let tugas = String(item.tugas || '').toLowerCase();
            let kodeScpmk = String(item.kode_scpmk || '').toLowerCase();
            let searchScpmk = String(item.searchKodeSCPMK || '').toLowerCase();
            let kodeCpmk = String(item.kode_cpmk || '').toLowerCase();
            let searchCpmk = String(item.searchKodeCPMK || '').toLowerCase();

            if (metode.includes(query) || tugas.includes(query)) {
                return true;
            }

            if (kodeScpmk.includes(query) || (cleanQuery && searchScpmk.includes(cleanQuery))) {
                return true;
            }

            if (kodeCpmk.includes(query) || (cleanQuery && searchCpmk.includes(cleanQuery))) {
                return true;
            }

            let cocokPertemuan = item.searchPertemuan?.some(pText => String(pText).includes(query)) || false;
            if (cocokPertemuan) return true;

            let cocokBobot = item.bobot?.some(bText => {
                let text = String(bText);
                return text === query || text === dotQuery || text.includes(query) || text.includes(dotQuery);
            }) || false;

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
}" x-init="rawItems = {{ $jsonFreshData }};" class="w-full">

    <x-global.main-layout-card>

        {{-- Slot Sortir --}}
        <x-slot:sortir>
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
        @php
            $allTimDosen = $tim_dosen->flatMap(function ($tim) {
                return $tim->dosens->map(function ($dosen) {
                    return [
                        'id' => $dosen->id,
                        'name' => $dosen->name,
                        'nip' => $dosen->nip,
                        'is_ketua' => (bool) $dosen->pivot->is_ketua,
                        'pertemuan_ke' => json_decode($dosen->pivot->pertemuan_ke ?? '[]'),
                    ];
                });
            });

            $sesis->each(function ($s, $index) use ($allTimDosen) {
                $pertemuan = $index + 1;
                $s->dosens_collection = $allTimDosen->filter(function ($dosen) use ($pertemuan) {
                    return in_array($pertemuan, $dosen['pertemuan_ke']);
                });
            });
        @endphp

        @foreach ($sesis as $index => $s)
            @php
                $isUjian = in_array(strtoupper($s->metode ?? ''), $daftarUjian);
                $kehadiran_mhs = Auth::user()->mahasiswa
                    ? $s->kehadirans->where('mahasiswa_id', Auth::user()->mahasiswa->id)->first()
                    : null;
            @endphp

            {{-- <template x-if="itemVisibilityMap[{{ $s->id }}]?.visible"> --}}
            <div x-show="itemVisibilityMap[{{ $s->id }}]?.visible" x-transition>
                <div wire:key="kelas-sesi-card-{{ $s->id }}" x-data="{ expanded: {{ $isUjian ? 'true' : 'false' }} }"
                    :style="'order: ' + (itemVisibilityMap[{{ $s->id }}]?.order ?? {{ $index }})"
                    @click="expanded = !expanded"
                    class="flex flex-col h-full flex-shrink-0 rounded-[20px] overflow-hidden border border-[var(--border-table-color)] bg-[var(--main-table-trans)]/50 transition-all duration-200 hover:shadow-lg active:shadow-lg cursor-pointer {{ $isUjian ? 'lg:col-span-2 ring-1 ring-amber-500/40' : '' }}">

                    {{-- ═══ HERO ═══ --}}
                    <div class="flex flex-col gap-3 p-[18px] {{ $isUjian ? 'bg-amber-700' : 'bg-[var(--main-color)]' }}"
                        @click.stop>

                        {{-- Baris atas: badge pertemuan + tombol menu --}}
                        <div class="flex items-start justify-between gap-2">

                            {{-- Badge Pertemuan + Metode --}}
                            <div class="flex items-center gap-2">
                                <flux:dropdown>
                                    <button
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-white/20 bg-white/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-white/75 transition-colors hover:bg-white/20 active:bg-white/50 focus:outline-none cursor-pointer">
                                        <flux:icon name="bookmark" class="w-3 h-3" />
                                        P-{{ $s->pertemuan_ke }}
                                    </button>
                                    @include(
                                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                                        ['key' => 1]
                                    )
                                </flux:dropdown>



                                <flux:dropdown>
                                    <button
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-white/20 bg-white/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-white/75 transition-colors hover:bg-white/20 active:bg-white/50 focus:outline-none cursor-pointer">
                                        <flux:icon name="academic-cap" class="w-3 h-3" />
                                        {{ $s->metode }}
                                    </button>
                                    @include(
                                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                                        ['key' => 2]
                                    )
                                </flux:dropdown>

                                @if (Auth::user()->admin || Auth::user()->dosen)
                                    <span class="text-xs text-white/60 font-mono">ID:
                                        {{ $s->id }}</span>
                                @endif
                            </div>

                            {{-- Tombol Menu --}}
                            <flux:dropdown>
                                <button
                                    class="flex h-[30px] w-[30px] flex-shrink-0 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white/80 transition-colors hover:bg-white/20 active:bg-white/50 focus:outline-none cursor-pointer"
                                    @click.stop>
                                    <flux:icon name="ellipsis-vertical" class="w-4 h-4" />
                                </button>
                                @include(
                                    'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                                    ['key' => 3]
                                )
                            </flux:dropdown>
                        </div>

                        {{-- Judul: Sub-CPMK atau label Ujian --}}
                        <p class="text-[15px] font-bold leading-[1.35] tracking-[0.1em] text-[var(--main-text)]">
                            @if ($isUjian)
                                <span class="text-amber-200">Sesi Evaluasi Utama</span>
                            @else
                                {{ $s->kode_scpmk ?? 'Sub-CPMK' }}
                            @endif
                        </p>

                        {{-- Sub info: hari + tanggal --}}
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="inline-flex items-center gap-1 text-[11px] font-medium text-[var(--main-text)]/65">
                                <flux:icon name="calendar-days" class="w-3 h-3" />
                                {{ $s->hari ?? '-' }}, {{ $s->jam_pelaksanaan ?? '-' }}
                            </span>
                            <span class="h-[3px] w-[3px] flex-shrink-0 rounded-full bg-[var(--main-text)]/30"></span>
                            <span
                                class="inline-flex items-center gap-1 text-[11px] font-medium text-[var(--main-text)]/65">
                                <flux:icon name="clock" class="w-3 h-3" />
                                {{ $s->tanggal_pelaksanaan ?? '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- ═══ BODY ═══ --}}
                    <div class="flex flex-1 flex-col gap-2.5 p-4" @click.stop>

                        {{-- Stat boxes: Absensi + Bobot + ID --}}
                        <div class="grid grid-cols-3 gap-1.5">

                            {{-- Absensi --}}
                            <div
                                class="flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                                <span
                                    class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Absensi</span>
                                <span
                                    class="text-base font-bold leading-none text-[var(--contrast-main-text)]">{{ $s->total_absensi ?? 0 }}</span>
                                <span class="text-[9px] font-semibold text-[var(--contrast-second-text)]">/
                                    {{ $s->count_mahasiswa }}</span>
                            </div>

                            {{-- Bobot --}}
                            <div
                                class="flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                                <span
                                    class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Bobot</span>
                                <span
                                    class="text-base font-bold leading-none text-[var(--contrast-main-text)]">{{ $s->bobot_normalisasi ?? '-' }}</span>
                                <span class="text-[9px] font-semibold text-[var(--contrast-second-text)]">%</span>
                            </div>

                            {{-- ID Sesi --}}
                            <div
                                class="flex flex-col items-center justify-center gap-1 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                                <span
                                    class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Metode</span>
                                <flux:dropdown>
                                    <button class="cursor-pointer focus:outline-none">
                                        @include('livewire.global.table.badge.metode-badge', [
                                            'xValue' => $s->metode,
                                            'variant' => '',
                                        ])
                                    </button>
                                    @include(
                                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                                        ['key' => 4]
                                    )
                                </flux:dropdown>
                            </div>
                        </div>

                        {{-- Expandable: Sub-CPMK + Deskripsi + Status Absen --}}
                        <div x-show="expanded" x-collapse class="flex flex-col gap-2">

                            {{-- Sub-CPMK & Bobot detail --}}
                            <div
                                class="rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-4 py-3 flex flex-col gap-2">
                                <div class="flex items-center justify-between gap-2">
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-[0.06em] text-[var(--contrast-third-text)]">Sub-CPMK</span>
                                    <flux:dropdown>
                                        <button class="cursor-pointer focus:outline-none">
                                            <flux:badge icon="academic-cap" color="fuchsia" size="sm">
                                                {{ $s->kode_scpmk ?? '---' }}</flux:badge>
                                        </button>
                                        @include(
                                            'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                                            ['key' => 5]
                                        )
                                    </flux:dropdown>
                                </div>
                                <div
                                    class="flex items-center justify-between text-xs text-[var(--contrast-second-text)]">
                                    <span>Waktu Tugas</span>
                                    <span class="font-semibold text-[var(--contrast-main-text)]">{{ $s->w_tugas ?? 0 }}
                                        menit</span>
                                </div>
                                <div
                                    class="flex items-center justify-between text-xs text-[var(--contrast-second-text)]">
                                    <span>Mandiri</span>
                                    <span
                                        class="font-semibold text-[var(--contrast-main-text)]">{{ $s->w_mandiri ?? 0 }}
                                        menit</span>
                                </div>
                            </div>

                            {{-- Deskripsi Tugas --}}
                            <div
                                class="rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-4 py-3">
                                <span
                                    class="text-[10px] font-bold uppercase tracking-[0.06em] text-[var(--contrast-third-text)] block mb-1.5">Deskripsi
                                    Tugas / Evaluasi</span>
                                <p class="text-xs leading-relaxed text-[var(--contrast-main-text)]">
                                    {{ $s->tugas ?? 'Tidak ada deskripsi tugas spesifik untuk sesi ini.' }}
                                </p>
                            </div>

                            {{-- Tambahkan ini di view --}}

                            <div
                                class="rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-4 py-3">
                                <span
                                    class="text-[10px] font-bold uppercase tracking-[0.06em] text-[var(--contrast-third-text)] block mb-1.5">
                                    Referensi
                                </span>
                                @php
                                    $referensiList = $s->referensi_sesi ?? collect();
                                @endphp
                                @forelse($referensiList as $refs)
                                    <div class="text-xs text-[var(--contrast-main-text)] flex items-start gap-2">
                                        <div
                                            class="{{ $referensiList->count() > 1 ? 'indent-[-15px] pl-[15px]' : '' }} mb-1">

                                            @if ($referensiList->count() > 1)
                                                <span class="mr-[5px]">{{ $loop->iteration }}.</span>
                                            @endif
                                            <span>{{ $refs->citation }}</span>
                                            @if ($refs->link)
                                                <a href="{{ $refs->link }}" target="_blank"
                                                    class="inline-flex items-center ml-2 hover:opacity-70 transition-opacity {{ $theme['link'] ?? 'text-blue-600' }}">
                                                    <flux:icon.link variant="micro" />
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-xs text-zinc-400 italic">Tidak ada data Referensi</div>
                                @endforelse
                            </div>

                            <div
                                class="rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-4 py-3">
                                <span
                                    class="text-[10px] font-bold uppercase tracking-[0.06em] text-[var(--contrast-third-text)] block mb-1.5">
                                    Dosen Pengajar
                                </span>
                                @php
                                    $hasSesiDosen = isset($s->dosens_collection) && $s->dosens_collection->isNotEmpty();
                                    $pengajar_collection = $hasSesiDosen
                                        ? $s->dosens_collection
                                        : $tim_dosen->flatMap->dosens;
                                    $pengajar_collection = collect($pengajar_collection)->map(function ($d) {
                                        return (object) [
                                            'name' => $d->name ?? ($d['name'] ?? 'Tanpa Nama'),
                                            'nip' => $d->nip ?? ($d['nip'] ?? '-'),
                                            'is_ketua' => isset($d->pivot)
                                                ? (bool) $d->pivot->is_ketua
                                                : (bool) ($d['is_ketua'] ?? false),
                                        ];
                                    });
                                @endphp

                                @forelse($pengajar_collection as $dosen)
                                    <div class="text-xs text-[var(--contrast-main-text)] flex items-center gap-2">
                                        <div
                                            class="{{ $pengajar_collection->count() > 1 ? 'indent-[-15px] pl-[15px]' : '' }} mb-1">

                                            @if ($pengajar_collection->count() > 1)
                                                <span class="mr-[5px]">{{ $loop->iteration }}.</span>
                                            @endif

                                            {{ $dosen->name }}

                                            @if ($dosen->is_ketua)
                                                <span
                                                    class="ml-2 px-1.5 py-0.5 text-[9px] font-semibold bg-blue-100 text-blue-700 rounded">
                                                    KETUA
                                                </span>
                                            @endif

                                            <br>NIP: {{ $dosen->nip }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-xs text-zinc-400 italic">Tidak ada data Dosen</div>
                                @endforelse
                            </div>

                            {{-- Tombol Absensi (Mahasiswa) --}}
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
                                        <x-button-action color="blue" size="sm" class="w-full justify-center"
                                            @click="
                                                $store.sesi?.setEdit(0);
                                                $store.sesi?.setColor('text-blue-700 dark:text-blue-400');
                                                $flux.modal('absensi-sesi-modal').show();
                                                $store.sesi?.setValueAbsenSesi(
                                                    '{{ $s->id ?? '' }}', '{{ $jadwal->kode }}', '{{ $s->pertemuan_ke ?? '' }}', '{{ $s->kode_scpmk }}',
                                                    '{{ $kehadiran_mhs->keterangan ?? null }}',
                                                    '{{ $s->waktu_pelaksanaan ?? '' }}', '{{ $s->waktu_berakhir ?? '' }}',
                                                    '{{ $s->waktu_telat ?? '' }}', '{{ $s->waktu_dispensasi ?? '' }}'
                                                );
                                                $flux.modal('absensi-sesi-modal').show();
                                                $dispatch('open-absensi-sesi-modal');
                                            ">
                                            <flux:icon name="user-plus" class="w-3.5 h-3.5" />
                                            <span>Absensi</span>
                                        </x-button-action>
                                    </template>
                                </div>

                                {{-- Status Absen Mahasiswa --}}
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
                                    <div
                                        class="rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-2.5 py-2.5 flex items-center justify-between gap-2">
                                        <flux:badge color="{{ $badgeColor }}" size="sm" inset-top-bottom>
                                            {{ $kehadiran_mhs->status }}</flux:badge>
                                        <span
                                            class="text-xs text-[var(--contrast-second-text)] flex items-center gap-1">
                                            <flux:icon name="clock" class="w-3.5 h-3.5" />
                                            {{ $kehadiran_mhs->waktu_presensi?->format('H:i') }} WIB
                                        </span>
                                    </div>
                                    @if ($kehadiran_mhs->keterangan)
                                        <div
                                            class="rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-2.5 py-2">
                                            <span
                                                class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)] block mb-1">Keterangan</span>
                                            <p class="text-xs italic text-[var(--contrast-main-text)]">
                                                {{ $kehadiran_mhs->keterangan }}</p>
                                        </div>
                                    @endif
                                @else
                                    <flux:badge color="zinc" size="sm" class="font-mono opacity-70 px-4">Belum
                                        Presensi
                                    </flux:badge>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- ═══ FOOTER: toggle hint ═══ --}}
                    @if (!$isUjian)
                        <div class="px-4 pb-4" @click.stop>
                            <button
                                class="cursor-pointer flex w-full items-center justify-center gap-1.5 rounded-[11px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] btn-card-focus-state transition-all active:scale-[0.99]"
                                @click="expanded = !expanded">
                                <flux:icon name="chevron-down" class="w-3.5 h-3.5 transition-transform duration-300"
                                    ::class="{ 'rotate-180': expanded }" />
                                <span x-text="expanded ? 'Sembunyikan Detail' : 'Lihat Detail'"></span>
                            </button>
                        </div>
                    @endif

                </div>
            </div>

            {{-- </template> --}}
        @endforeach

        {{-- EMPTY STATE ANCHOR --}}
        <x-slot:emptys>
            <div x-show="totalFilteredItems === 0"
                class="col-span-6 text-center p-12 rounded-xl border border-dashed table-border bg-[var(--main-table-trans)]">
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)]">Tidak ada data Sesi Pertemuan Kelas
                    ditemukan!</p>
            </div>
        </x-slot:emptys>

        {{-- Slot Footer Pagination --}}
        <x-slot:footer>
            @include('livewire.global.table.pagination-alpine')
            @include('livewire.global.table.trash-delete')
        </x-slot:footer>

    </x-global.main-layout-card>
</div>
