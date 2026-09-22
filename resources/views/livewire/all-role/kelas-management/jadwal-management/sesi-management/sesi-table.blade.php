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

                return [
                    'id' => $s->id,
                    'dbIndex' => $index,

                    // Nilai sorting utama
                    'pertemuan_ke' => $p,
                    'total_absensi' => (int) ($s->total_absensi ?? 0),

                    'hari' => trim($s->hari ?? ''),
                    'hari_jam' => trim("{$s->hari}, {$s->jam_pelaksanaan}"),
                    'hari_tanggal' => trim("{$s->hari}, {$s->tanggal_pelaksanaan}"),

                    'tanggal_pelaksanaan' => $s->tanggal_pelaksanaan ?? '',
                    'tanggal' => $s->tanggal ?? '',

                    'bobot_normalisasi' => $s->bobot_normalisasi ?? '',

                    // Khusus sorting metode
                    'metode' => trim(strtolower($s->metode ?? '')),

                    'tugas' => strtolower($s->tugas ?? ''),

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
                let kodeScpmk = String(item.kode_scpmk || '').toLowerCase();
                let searchScpmk = String(item.searchKodeSCPMK || '').toLowerCase();
                let kodeCpmk = String(item.kode_cpmk || '').toLowerCase();
                let searchCpmk = String(item.searchKodeCPMK || '').toLowerCase();
    
                let hari = String(item.hari || '').toLowerCase();
                let hariJam = String(item.hari_jam || '').toLowerCase().replace(/[\u2013\u2014]/g, '-');
                let hariTanggal = String(item.hari_tanggal || '').toLowerCase();
    
                if (metode.includes(query) || tugas.includes(query)) return true;
                if (kodeScpmk.includes(query) || (cleanQuery && searchScpmk.includes(cleanQuery))) return true;
                if (kodeCpmk.includes(query) || (cleanQuery && searchCpmk.includes(cleanQuery))) return true;
                if (item.searchPertemuan?.some(pText => String(pText).toLowerCase().includes(query))) return true;
    
                if (hari.includes(query) || hariTanggal.includes(query)) return true;
                if (hariJam.includes(normalizedQuery)) return true;
    
                if (item.bobot?.some(bText => {
                        let text = String(bText).toLowerCase();
                        return text.includes(query) || text.includes(dotQuery);
                    })) return true;
    
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
    
                    if (field === 'pertemuan_ke' || field === 'total_absensi') {
                        const numA = Number(field === 'pertemuan_ke' ? (a.pertemuan_ke ?? 0) : (a.total_absensi ?? 0));
                        const numB = Number(field === 'pertemuan_ke' ? (b.pertemuan_ke ?? 0) : (b.total_absensi ?? 0));
                        if (numA !== numB) return (numA - numB) * direction;
                        return fallbackOrder();
                    }
    
                    if (field === 'metode') {
                        const rankA = getMethodPriority(a.metode);
                        const rankB = getMethodPriority(b.metode);
                        if (rankA !== rankB) return (rankA - rankB) * direction;
    
                        const perA = Number(a.pertemuan_ke ?? 0);
                        const perB = Number(b.pertemuan_ke ?? 0);
                        if (perA !== perB) return (perA - perB) * direction;
                        return fallbackOrder();
                    }
    
                    if (field === 'bobot') {
                        const safeA = parseNumber(a.bobot_normalisasi);
                        const safeB = parseNumber(b.bobot_normalisasi);
                        if (safeA !== safeB) return (safeA - safeB) * direction;
    
                        const perA = Number(a.pertemuan_ke ?? 0);
                        const perB = Number(b.pertemuan_ke ?? 0);
                        if (perA !== perB) return (perA - perB) * direction;
                        return fallbackOrder();
                    }
    
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

            @php
                $isAdminOrDosen = Auth::user()->admin || Auth::user()->dosen;

                if ($showMore) {
                    $gridCols = $isAdminOrDosen
                        ? 'grid-cols-[100px_180px_180px_150px_200px_150px_180px_180px_200px_150px_minmax(280px,1fr)_150px_150px_200px_120px]'
                        : 'grid-cols-[100px_180px_180px_150px_200px_150px_180px_180px_200px_150px_minmax(280px,1fr)_150px_150px_200px]';
                    $minWidthClass = 'min-w-[1500px]';
                } else {
                    $gridCols = $isAdminOrDosen
                        ? 'grid-cols-[0.5fr_1.2fr_1fr_1fr_1.3fr_1fr_1.5fr_0.6fr]'
                        : 'grid-cols-[1.2fr_1fr_1fr_1.3fr_1fr_1.5fr]';
                    $minWidthClass = 'w-full';
                }
            @endphp

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

            {{-- HEADER TABEL --}}
            <x-slot:header>
                {{-- CSS Grid Utama untuk Header 2 Baris --}}
                <div
                    class="
                    {{-- py-2 px-3 --}}
                    grid {{ $gridCols }} {{ $minWidthClass }} w-full items-center text-xs sm:text-sm font-semibold bg-[var(--main-table-color)] border-b table-border gap-y-1">

                        @include('livewire.global.table.head-table', [
                            'sortFieldString' => 'id',
                            'alpine' => 'sesi',
                            'isCenter' => 1,
                            'rowSpan' => 2,
                            'withDiv' => 1,
                        ])

                    {{-- Metode (Rowspan 2 - Posisi Tengah Vertikal) --}}
                    <div class="row-span-2 self-center text-center truncate py-1">
                        Metode
                    </div>

                    {{-- Pertemuan (Rowspan 2 - Posisi Tengah Vertikal) --}}
                    <div class="row-span-2 self-center text-center truncate py-1">
                        Pertemuan
                    </div>

                    {{-- Group 1: Informasi Sesi Kelas (Span 4 atau 5 Kolom di Baris 1) --}}
                    <div
                        class="{{ $showMore ? 'col-span-5' : 'col-span-4' }} text-center font-bold tracking-wide border-x table-border py-1 px-2 bg-[var(--hover-table-color)]/50 rounded mb-1">
                        Informasi Sesi Kelas
                    </div>

                    {{-- Group 2: Informasi Sub-CPMK (Span 6 Kolom di Baris 1) --}}
                    @if ($showMore)
                        <div
                            class="col-span-6 text-center font-bold tracking-wide border-r table-border py-1 px-2 bg-[var(--hover-table-color)]/50 rounded mb-1">
                            Informasi Sub-CPMK
                        </div>
                    @endif

                    {{-- Aksi (Rowspan 2 - Posisi Tengah Vertikal) --}}
                    @if ($isAdminOrDosen)
                        <div class="row-span-2 self-center text-center truncate py-1">
                            Aksi
                        </div>
                    @endif

                    {{-- ═══ SUB-KOLOM BARIS 2 ═══ --}}

                    {{-- Sub-kolom Informasi Sesi Kelas --}}
                    <div class="text-center truncate text-[var(--contrast-second-text)]">Hari</div>
                    <div class="text-center truncate text-[var(--contrast-second-text)]">Jam</div>
                    <div class="text-center truncate text-[var(--contrast-second-text)]">Absensi</div>

                    @if ($showMore)
                        <div class="text-center truncate text-[var(--contrast-second-text)]">Absensi Terdata</div>
                    @endif

                    <div class="text-center truncate text-[var(--contrast-second-text)]">Tanggal</div>

                    {{-- Sub-kolom Informasi Sub-CPMK --}}
                    @if ($showMore)
                        <div class="text-center truncate text-[var(--contrast-second-text)]">Sub-CPMK</div>
                        <div class="text-center truncate text-[var(--contrast-second-text)]">Bobot</div>
                        <div class="text-left px-2 truncate text-[var(--contrast-second-text)]">Deskripsi Tugas</div>
                        <div class="text-center truncate text-[var(--contrast-second-text)]">W. Tugas</div>
                        <div class="text-center truncate text-[var(--contrast-second-text)]">W. Mandiri</div>
                        <div class="text-center truncate text-[var(--contrast-second-text)]">CPMK</div>
                    @endif

                </div>
            </x-slot:header>

            {{-- BODY TABEL --}}
            @forelse($sesis as $s)
                @php
                    $isUjian = in_array(strtoupper($s->metode ?? ''), $daftarUjian);
                    $kehadiran_mhs = Auth::user()->mahasiswa
                        ? $s->kehadirans->where('mahasiswa_id', Auth::user()->mahasiswa->id)->first()
                        : null;
                @endphp

                {{-- Layer 1: Visibilitas Murni (x-show) --}}
                <div x-show="filteredAndSortedIds.slice((currentPage - 1) * perPage, currentPage * perPage).some(item => Number(item.id) === Number({{ $s->id }}))"
                    class="contents">

                    {{-- Layer 2: Penanganan CSS Order & Dynamic CSS Grid --}}
                    <div :style="'order: ' + filteredAndSortedIds.findIndex(entry => Number(entry.id) === Number(
                        {{ $s->id }}))"
                        wire:key="kelas-sesi-row-{{ $s->id }}"
                        class="py-3 grid {{ $gridCols }} {{ $minWidthClass }} w-full items-center hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200 border-b table-border text-xs sm:text-sm">

                        <div class="text-center font-medium text-[var(--contrast-second-text)] truncate">
                            {{ $s->id }}
                        </div>

                        <div class="flex justify-center">
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

                        <div class="text-center font-semibold text-[var(--contrast-main-text)] truncate">
                            P-{{ $s->pertemuan_ke }}
                        </div>

                        <div class="text-center whitespace-nowrap truncate">{{ $s->hari }}</div>
                        <div class="text-center whitespace-nowrap text-[var(--contrast-second-text)] truncate">
                            {{ $s->jam_pelaksanaan }}</div>

                        <div class="text-center whitespace-nowrap truncate">
                            {{ $s->total_absensi . ' / ' . ($s->count_mahasiswa ?? 0) }}
                        </div>

                        @if ($showMore)
                            <div class="text-center whitespace-nowrap text-[var(--contrast-second-text)] truncate">
                                {{ $s->total_absensi_all ?? 0 }}
                            </div>
                        @endif

                        <div class="text-center whitespace-nowrap truncate">
                            {{ $s->tanggal_pelaksanaan }}
                        </div>

                        @if ($showMore)
                            <div class="flex justify-center">
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

                            <div class="text-center whitespace-nowrap font-medium truncate">
                                {{ $s->bobot_normalisasi ? $s->bobot_normalisasi . '%' : '-' }}
                            </div>

                            <div class="truncate px-2 text-[var(--contrast-second-text)]" title="{{ $s->tugas }}">
                                {{ $s->tugas ?? '-' }}
                            </div>

                            <div class="text-center whitespace-nowrap truncate">{{ $s->w_tugas ?? 0 }} mnt</div>
                            <div class="text-center whitespace-nowrap truncate">{{ $s->w_mandiri ?? 0 }} mnt</div>

                            <div class="flex justify-center">
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

                        @if ($isAdminOrDosen)
                            <div class="flex justify-center">
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
                        @endif
                    </div>
                </div>
            @empty
                <div class="w-full text-center p-8 text-[var(--contrast-second-text)]">
                    Tidak ada data Sesi Pertemuan Kelas ditemukan!
                </div>
            @endforelse

            <x-slot:emptys>
                <div x-show="totalFilteredItems === 0"
                    class="w-full text-center p-12 text-[var(--contrast-second-text)]">
                    Tidak ada data Sesi Pertemuan Kelas ditemukan!
                </div>
            </x-slot:emptys>

            <x-slot:footer>
                @include('livewire.global.table.pagination-alpine')
            </x-slot:footer>

        </x-global.main-layout-table-alpine>
    </div>
</div>
