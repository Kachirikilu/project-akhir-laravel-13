<div wire:key="view-table-sesi">

    @php
        $showMore = $showMore ?? false;

        $daftarUjian = array_merge(config('app.uts_fields'), config('app.uas_fields'));

        $alpineData = $sesis
            ->map(function ($s, $index) use ($daftarUjian) {
                $stringKodeSCPMK = $s->kode_scpmk ?? '';
                $stringKodeCPMK = $s->kode_cpmk ?? '';
                $p = (int) $s->pertemuan_ke;

                $bobotRaw = $s->bobot_normalisasi ?? '';
                $bobotClean = str_replace(',', '.', $bobotRaw);

                $wTugas = (int) ($s->w_tugas ?? 0);
                $wMandiri = (int) ($s->w_mandiri ?? 0);

                return [
                    'id' => $s->id,
                    'dbIndex' => $index,

                    // Nilai sorting utama
                    'pertemuan_ke' => $p,
                    'total_absensi' => (int) ($s->total_absensi ?? 0),
                    'total_absensi_all' => (int) ($s->total_absensi_all ?? 0),

                    'hari' => trim($s->hari ?? ''),
                    'hari_jam' => trim("{$s->hari}, {$s->jam_pelaksanaan}"),
                    'hari_tanggal' => trim("{$s->hari}, {$s->tanggal_pelaksanaan}"),

                    'jam_pelaksanaan' => $s->jam_pelaksanaan ?? '',
                    'tanggal_pelaksanaan' => $s->tanggal_pelaksanaan ?? '',
                    'tanggal' => $s->tanggal ?? '',

                    'bobot_normalisasi' => $s->bobot_normalisasi ?? '',

                    // Khusus sorting metode
                    'metode' => trim(strtolower($s->metode ?? '')),

                    'tugas' => strtolower($s->tugas ?? ''),

                    // --- FIELD BARU: Materi, Metodologi, Indikator ---
                    'materi' => strtolower($s->materi ?? ''),
                    'metodologi' => strtolower($s->metodologi ?? ''),
                    'indikator' => strtolower($s->indikator ?? ''),

                    'kode_scpmk' => strtolower($stringKodeSCPMK),
                    'kode_cpmk' => strtolower($stringKodeCPMK),

                    'searchKodeCPMK' => preg_replace('/[^A-Za-z0-9]/', '', strtolower($stringKodeCPMK)),
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

                    // --- FIELD BARU: Durasi Waktu W_TUGAS & W_MANDIRI ---
                    'w_tugas' => $wTugas,
                    'searchWTugas' => [
                        (string) $wTugas,
                        $wTugas . 'm',
                        $wTugas . ' m',
                        $wTugas . 'mnt',
                        $wTugas . ' mnt',
                        $wTugas . 'menit',
                        $wTugas . ' menit',
                        $wTugas . 'min',
                        $wTugas . ' min',
                        $wTugas . 'minute',
                        $wTugas . ' minute',
                        $wTugas . 'minutes',
                        $wTugas . ' minutes',
                    ],

                    'w_mandiri' => $wMandiri,
                    'searchWMandiri' => [
                        (string) $wMandiri,
                        $wMandiri . 'm',
                        $wMandiri . ' m',
                        $wMandiri . 'mnt',
                        $wMandiri . ' mnt',
                        $wMandiri . 'menit',
                        $wMandiri . ' menit',
                        $wMandiri . 'min',
                        $wMandiri . ' min',
                        $wMandiri . 'minute',
                        $wMandiri . ' minute',
                        $wMandiri . 'minutes',
                        $wMandiri . ' minutes',
                    ],
                ];
            })
            ->values()
            ->toArray();

        $jsonFreshData = json_encode($alpineData);

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
    
        get currentPage() {
            return Number(this.$store.sesi?.currentPage ?? 1) || 1;
        },
        set currentPage(val) {
            this.$store.sesi.currentPage = Number(val) || 1;
        },
    
        get perPage() {
            return Number(this.$store.sesi?.perPage ?? 8) || 8;
        },
        set perPage(val) {
            const next = Number(val) || 8;
            if (this.$store.sesi?.perPage !== next) {
                this.$store.sesi.perPage = next;
            }
        },
    
        get sortField() {
            return this.$store.sesi?.sortField ?? 'pertemuan_ke';
        },
        set sortField(val) {
            this.$store.sesi.sortField = val ?? 'pertemuan_ke';
        },
    
        get sortDirection() {
            return this.$store.sesi?.sortDirection ?? 'asc';
        },
        set sortDirection(val) {
            this.$store.sesi.sortDirection = val ?? 'asc';
        },
    
        get filteredAndSortedIds() {
            let query = (this.$store.sesi?.search || '').toLowerCase().trim();
            let cleanQuery = query.replace(/[^a-z0-9]/g, '');
            let dotQuery = query.replace(',', '.');
            let normalizedQuery = query.replace(/[\u2013\u2014]/g, '-');
    
            let filtered = this.rawItems.filter(item => {
                if (!query) return true;
    
                let metode = String(item.metode || '').toLowerCase();
                let tugas = String(item.tugas || '').toLowerCase();
                let materi = String(item.materi || '').toLowerCase();
                let metodologi = String(item.metodologi || '').toLowerCase();
                let indikator = String(item.indikator || '').toLowerCase();
    
                let kodeScpmk = String(item.kode_scpmk || '').toLowerCase();
                let searchScpmk = String(item.searchKodeSCPMK || '').toLowerCase();
                let kodeCpmk = String(item.kode_cpmk || '').toLowerCase();
                let searchCpmk = String(item.searchKodeCPMK || '').toLowerCase();
    
                let hari = String(item.hari || '').toLowerCase();
                let hariJam = String(item.hari_jam || '').toLowerCase().replace(/[\u2013\u2014]/g, '-');
                let hariTanggal = String(item.hari_tanggal || '').toLowerCase();
    
                // 1. Pencarian Teks & Field Baru (materi, metodologi, indikator)
                if (metode.includes(query) || tugas.includes(query) || materi.includes(query) || metodologi.includes(query) || indikator.includes(query)) return true;
    
                // 2. Kode CPMK & Sub-CPMK
                if (kodeScpmk.includes(query) || (cleanQuery && searchScpmk.includes(cleanQuery))) return true;
                if (kodeCpmk.includes(query) || (cleanQuery && searchCpmk.includes(cleanQuery))) return true;
    
                // 3. Pertemuan Ke
                if (item.searchPertemuan?.some(pText => String(pText).toLowerCase().includes(query))) return true;
    
                // 4. Hari & Jam
                if (hari.includes(query) || hariTanggal.includes(query)) return true;
                if (hariJam.includes(normalizedQuery)) return true;
    
                // 5. Bobot Normalisasi
                if (item.bobot?.some(bText => {
                        let text = String(bText).toLowerCase();
                        return text.includes(query) || text.includes(dotQuery);
                    })) return true;
    
                // 6. Waktu Tugas & Waktu Mandiri (Variasi menit/mnt/minutes)
                if (item.searchWTugas?.some(wText => String(wText).toLowerCase().includes(query))) return true;
                if (item.searchWMandiri?.some(wText => String(wText).toLowerCase().includes(query))) return true;
    
                return false;
            });
    
            let field = this.$store.sesi?.sortField || this.sortField;
            let direction = (this.$store.sesi?.sortDirection || this.sortDirection) === 'desc' ? -1 : 1;
    
            const getMethodPriority = (value) => {
                const text = String(value ?? '').trim().toLowerCase();
                if (text.includes('uas')) return 3;
                if (text.includes('uts')) return 2;
                if (text.includes('teori')) return 1;
                if (text.includes('praktik')) return 0;
                if (text.includes('tugas')) return -1;
                return -2;
            };
    
            const parseNumber = (value) => {
                if (value === null || value === undefined || value === '') return 0;
                const normalized = String(value)
                    .trim()
                    .replace(/[^0-9,.-]/g, '')
                    .replace(',', '.');
                const num = Number(normalized);
                return Number.isFinite(num) ? num : 0;
            };
    
            const sortedFiltered = [...filtered];
    
            if (field) {
                sortedFiltered.sort((a, b) => {
                    const fallbackOrder = () => Number(a.dbIndex) - Number(b.dbIndex);
    
                    // Sorting Angka
                    if (['pertemuan_ke', 'total_absensi', 'total_absensi_all', 'w_tugas', 'w_mandiri'].includes(field)) {
                        const numA = Number(a[field] ?? 0);
                        const numB = Number(b[field] ?? 0);
                        if (numA !== numB) return (numA - numB) * direction;
                        return fallbackOrder();
                    }
    
                    // Sorting Metode
                    if (field === 'metode') {
                        const rankA = getMethodPriority(a.metode);
                        const rankB = getMethodPriority(b.metode);
                        if (rankA !== rankB) return (rankA - rankB) * direction;
    
                        const perA = Number(a.pertemuan_ke ?? 0);
                        const perB = Number(b.pertemuan_ke ?? 0);
                        if (perA !== perB) return (perA - perB) * direction;
                        return fallbackOrder();
                    }
    
                    // Sorting Bobot
                    if (field === 'bobot') {
                        const safeA = parseNumber(a.bobot_normalisasi);
                        const safeB = parseNumber(b.bobot_normalisasi);
                        if (safeA !== safeB) return (safeA - safeB) * direction;
    
                        const perA = Number(a.pertemuan_ke ?? 0);
                        const perB = Number(b.pertemuan_ke ?? 0);
                        if (perA !== perB) return (perA - perB) * direction;
                        return fallbackOrder();
                    }
    
                    // Sorting String Umum (Materi, Metodologi, Indikator, Tugas, CPMK, Sub-CPMK, dll.)
                    const valA = a[field];
                    const valB = b[field];
                    const textA = String(valA ?? '').trim().toLowerCase();
                    const textB = String(valB ?? '').trim().toLowerCase();
                    const result = textA.localeCompare(textB, 'id', { numeric: true, sensitivity: 'base' });
    
                    return result !== 0 ? result * direction : fallbackOrder();
                });
            } else {
                sortedFiltered.sort((a, b) => Number(a.dbIndex) - Number(b.dbIndex));
            }
    
            return sortedFiltered;
        },
    
        get pageIds() {
            const start = (this.currentPage - 1) * this.perPage;
            const end = Math.min(start + this.perPage, this.filteredAndSortedIds.length);
            return this.filteredAndSortedIds.slice(start, end).map(item => item.id);
        },
    
        get itemVisibilityMap() {
            let map = {};
            const visibleIds = new Set(this.pageIds);
    
            this.filteredAndSortedIds.forEach((item) => {
                map[item.id] = {
                    visible: visibleIds.has(item.id),
                    order: this.filteredAndSortedIds.findIndex(entry => entry.id === item.id)
                };
            });
            return map;
        },
    
        get totalFilteredItems() {
            return this.filteredAndSortedIds.length;
        },
        get totalPages() {
            return Math.max(1, Math.ceil(this.totalFilteredItems / this.perPage));
        },
    
        init() {
            this.$watch('$store.sesi.search', () => { this.currentPage = 1; });
            this.$watch('$store.sesi.sortField', () => { this.currentPage = 1; });
            this.$watch('$store.sesi.sortDirection', () => { this.currentPage = 1; });
            this.$watch('$store.sesi.perPage', (val) => {
                const next = Number(val) || 8;
                if (this.perPage !== next) {
                    this.perPage = next;
                }
                const totalPages = Math.max(1, Math.ceil(this.filteredAndSortedIds.length / this.perPage));
                this.currentPage = Math.min(this.currentPage || 1, totalPages);
            });
        }
    }" x-init="rawItems = {{ $jsonFreshData }};" class="w-full">



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
            @foreach($sesis as $s)
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

                        <div
                            class="w-32 shrink-0 text-center font-medium text-[var(--contrast-second-text)] truncate px-6">
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
                                <flux:button class="cursor-pointer" variant="ghost" size="sm"
                                    icon="ellipsis-horizontal" inset="top bottom">
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
                <div x-show="totalFilteredItems === 0"
                    class="w-full text-center px-12 py-5 text-[var(--contrast-second-text)]">
                    Tidak ada data Sesi Pertemuan Kelas ditemukan!
                </div>
            </x-slot:emptys>

            <x-slot:footer>
                @include('livewire.global.table.pagination-alpine')
            </x-slot:footer>

        </x-global.main-layout-table-alpine>
    </div>
</div>
