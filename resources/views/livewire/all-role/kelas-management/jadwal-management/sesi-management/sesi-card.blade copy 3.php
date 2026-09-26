<div wire:key="view-card-sesi">

    @php
        $daftarUjian = array_merge(config('rps.uts_fields'), config('rps.uas_fields'));

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

        <x-global.main-layout-card>

            {{-- Slot Sortir --}}
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

            {{-- Slot Search --}}
            <x-slot:rightSecHead>
                <div class="w-full md:w-96 xl:w-108">
                    @include('livewire.global.search-and-filters.main-search', [
                        'placeholder' => 'Cari Sesi Pertemuan Kelas...',
                        'alpine' => 'sesi',
                        'isLive' => 1,
                        'isBorder' => 2,
                    ])
                </div>
            </x-slot:rightSecHead>


            <template x-for="item in filteredAndSortedIds.slice((currentPage - 1) * perPage, currentPage * perPage)" :key="item.id">
                <div
                    x-data="{ expanded: false }"
                    :style="'order: ' + filteredAndSortedIds.findIndex(entry => entry.id === item.id)"
                    :class="[(String(item.metode || '').toUpperCase().includes('UAS') || String(item.metode || '').toUpperCase().includes('UTS')) ? 'lg:col-span-2' : '', (String(item.metode || '').toUpperCase().includes('UAS') || String(item.metode || '').toUpperCase().includes('UTS')) ? 'ring-1 ring-[var(--focus-color-special)] border-[var(--border-table-color-special)] bg-[var(--main-table-trans-spceial)]/50' : 'border-[var(--border-table-color)] bg-[var(--main-table-trans)]/50']"
                    class="flex flex-col h-full flex-shrink-0 rounded-[20px] overflow-hidden border transition-all duration-200 hover:shadow-lg active:shadow-lg cursor-pointer"
                    @click="expanded = !expanded">

                    <div class="flex flex-col gap-3 p-[18px]"
                        :class="(String(item.metode || '').toUpperCase().includes('UAS') || String(item.metode || '').toUpperCase().includes('UTS')) ? 'bg-[var(--main-color-special)]' : 'bg-[var(--main-color)]'">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <button class="inline-flex items-center gap-1.5 rounded-lg border border-white/20 bg-white/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-white/75 transition-colors hover:bg-white/20 active:bg-white/50 focus:outline-none cursor-pointer">
                                    <flux:icon name="bookmark" class="w-3 h-3" />
                                    <span x-text="'P-' + (item.pertemuan_ke ?? '-')"></span>
                                </button>

                                <button class="inline-flex items-center gap-1.5 rounded-lg border border-white/20 bg-white/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-white/75 transition-colors hover:bg-white/20 active:bg-white/50 focus:outline-none cursor-pointer">
                                    <flux:icon name="academic-cap" class="w-3 h-3" />
                                    <span x-text="item.metode || '-' "></span>
                                </button>
                            </div>

                            <button class="flex h-[30px] w-[30px] flex-shrink-0 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white/80 transition-colors hover:bg-white/20 active:bg-white/50 focus:outline-none cursor-pointer" @click.stop>
                                <flux:icon name="ellipsis-vertical" class="w-4 h-4" />
                            </button>
                        </div>

                        <p class="mt-1 text-[15px] font-bold leading-[1.35] tracking-[0.1em] text-[var(--main-text)]">
                            <span x-show="(String(item.metode || '').toUpperCase().includes('UAS') || String(item.metode || '').toUpperCase().includes('UTS'))">Sesi Evaluasi Utama</span>
                            <span x-show="!(String(item.metode || '').toUpperCase().includes('UAS') || String(item.metode || '').toUpperCase().includes('UTS'))" x-text="item.kode_scpmk || 'Sub-CPMK'"></span>
                        </p>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-[var(--main-text)]/64">
                                <flux:icon name="calendar-days" class="w-3 h-3" />
                                <span x-text="(item.hari || '-') + ', ' + (item.hari_jam || '-')"></span>
                            </span>
                            <span class="h-[3px] w-[3px] flex-shrink-0 rounded-full bg-[var(--main-text)]/30"></span>
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-[var(--main-text)]/64">
                                <flux:icon name="clock" class="w-3 h-3" />
                                <span x-text="item.tanggal || '-'"></span>
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col gap-2.5 p-4" @click.stop>
                        <div class="grid grid-cols-3 gap-1.5">
                            <div class="flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                                <span class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Absensi</span>
                                <span class="text-base font-bold leading-none text-[var(--contrast-main-text)]" x-text="item.total_absensi ?? 0"></span>
                                <span class="text-[9px] font-semibold text-[var(--contrast-second-text)]">/ <span x-text="item.count_mahasiswa ?? 0"></span></span>
                            </div>

                            <div class="flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                                <span class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Bobot</span>
                                <span class="text-base font-bold leading-none text-[var(--contrast-main-text)]" x-text="item.bobot_normalisasi ?? '-'"></span>
                                <span class="text-[9px] font-semibold text-[var(--contrast-second-text)]">%</span>
                            </div>

                            <div class="flex flex-col items-center justify-center gap-1 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                                <span class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Metode</span>
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-[9px] font-bold uppercase tracking-[0.08em] text-white"
                                    :class="String(item.metode || '').toUpperCase().includes('UAS')
                                        ? 'bg-rose-500'
                                        : String(item.metode || '').toUpperCase().includes('UTS')
                                            ? 'bg-amber-500'
                                            : 'bg-cyan-500'"
                                    x-text="item.metode || '-'">
                                </span>
                            </div>
                        </div>

                        <div x-show="expanded" x-collapse.duration.300ms class="mt-2 rounded-xl border border-[var(--border-table-color)] bg-[var(--second-table-color)] p-3 text-xs text-[var(--contrast-second-text)]">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-[var(--contrast-main-text)]">Detail Sesi</span>
                                <span class="text-[10px] uppercase tracking-[0.08em]">P-<span x-text="item.pertemuan_ke ?? '-'" ></span></span>
                            </div>
                            <div class="mt-2 space-y-1">
                                <div>Metode: <span x-text="item.metode || '-'" class="font-medium text-[var(--contrast-main-text)]"></span></div>
                                <div>Bobot: <span x-text="item.bobot_normalisasi || '-'" class="font-medium text-[var(--contrast-main-text)]"></span></div>
                                <div>Hari: <span x-text="item.hari || '-'" class="font-medium text-[var(--contrast-main-text)]"></span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
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
                @include('livewire.global.table.pagination-alpine', ['mx' => ''])
                @if (Auth::user()->admin)
                    @include('livewire.global.table.trash-delete-switch', ['mx' => ''])
                @endif
            </x-slot:footer>

        </x-global.main-layout-card>
    </div>
</div>
