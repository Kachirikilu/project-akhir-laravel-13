<div wire:key="view-card-sesi">

    @php
        $showMore = $showMore ?? false;

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
    ready: false, // Flag status delay
    rawItems: [],

    get currentPage() {
        return Number(this.$store?.sesi?.currentPage ?? 1) || 1;
    },
    set currentPage(val) {
        if (this.$store?.sesi) this.$store.sesi.currentPage = Number(val) || 1;
    },

    get perPage() {
        return Number(this.$store?.sesi?.perPage ?? 8) || 8;
    },
    set perPage(val) {
        const next = Number(val) || 8;
        if (this.$store?.sesi && this.$store.sesi.perPage !== next) {
            this.$store.sesi.perPage = next;
        }
    },

    get sortField() {
        return this.$store?.sesi?.sortField ?? 'pertemuan_ke';
    },
    set sortField(val) {
        if (this.$store?.sesi) this.$store.sesi.sortField = val ?? 'pertemuan_ke';
    },

    get sortDirection() {
        return this.$store?.sesi?.sortDirection ?? 'asc';
    },
    set sortDirection(val) {
        if (this.$store?.sesi) this.$store.sesi.sortDirection = val ?? 'asc';
    },

    get filteredAndSortedIds() {
        if (!this.ready || !Array.isArray(this.rawItems)) return [];

        let query = (this.$store?.sesi?.search || '').toLowerCase().trim();
        let cleanQuery = query.replace(/[^a-z0-9]/g, '');
        let dotQuery = query.replace(',', '.');
        let normalizedQuery = query.replace(/[\u2013\u2014]/g, '-');

        let filtered = this.rawItems.filter(item => {
            if (!item) return false;
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

        let field = this.$store?.sesi?.sortField || this.sortField;
        let direction = (this.$store?.sesi?.sortDirection || this.sortDirection) === 'desc' ? -1 : 1;

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
                const fallbackOrder = () => Number(a.dbIndex ?? 0) - Number(b.dbIndex ?? 0);

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
            sortedFiltered.sort((a, b) => Number(a.dbIndex ?? 0) - Number(b.dbIndex ?? 0));
        }

        return sortedFiltered;
    },

    get totalFilteredItems() {
        return (this.filteredAndSortedIds || []).length;
    },
    get totalPages() {
        return Math.max(1, Math.ceil(this.totalFilteredItems / this.perPage));
    },

    init() {
        if (this.$store?.sesi) {
            this.$watch('$store.sesi.search', () => { this.currentPage = 1; });
            this.$watch('$store.sesi.sortField', () => { this.currentPage = 1; });
            this.$watch('$store.sesi.sortDirection', () => { this.currentPage = 1; });
            this.$watch('$store.sesi.perPage', (val) => {
                const next = Number(val) || 8;
                if (this.perPage !== next) {
                    this.perPage = next;
                }
                const totalPages = Math.max(1, Math.ceil(this.totalFilteredItems / this.perPage));
                this.currentPage = Math.min(this.currentPage || 1, totalPages);
            });
        }
    }
}" 

x-init="$store.sesi?.initData({{ $jsonFreshData }})"
class="w-full">

        <x-global.main-layout-card :noTrash="true">

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
            {{-- <x-slot:rightSecHead>
                <div class="w-full md:w-96 xl:w-108">
                    @include('livewire.global.search-and-filters.main-search', [
                        'placeholder' => 'Cari Sesi Pertemuan Kelas...',
                        'alpine' => 'sesi',
                        'isLive' => 1,
                        'isBorder' => 2,
                    ])
                </div>
            </x-slot:rightSecHead> --}}


            <x-slot:rightSecHead>
                <div class="w-full md:w-110 xl:w-124">
                    <div class="col-start-1 row-start-1 w-full flex items-center justify-between gap-4">
                        <div class="flex-shrink-0">
                            @include('livewire.global.search-and-filters.page-control', [
                                'perPageOptions' => [2, 4, 8, 16],
                                'alpine' => 'sesi',
                                'key' => 'page-control-sesi-card',
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


            @foreach ($sesis as $index => $s)
                @php
                    $isUjian = in_array(strtoupper($s->metode ?? ''), $daftarUjian);
                    $isPastDate =
                        !empty($s->tanggal) &&
                        \Carbon\Carbon::parse($s->tanggal)->isPast() &&
                        !\Carbon\Carbon::parse($s->tanggal)->isToday();

                    $kehadiran_mhs = Auth::user()->mahasiswa
                        ? $s->kehadirans->where('mahasiswa_id', Auth::user()->mahasiswa->id)->first()
                        : null;

                    if ($isUjian) {
                        if ($isPastDate) {
                            $focusDiv = 'border-[var(--border-table-color-special)]';
                            $focusButton =
                                'text-[var(--focus-color-special)] btn-card-focus-state-special-64 ring-[var(--focus-color)]/64';
                            $mainColor = 'bg-[var(--main-color-special)]/64';
                        } else {
                            $focusDiv =
                                'ring-1 ring-[var(--focus-color-special)] border-[var(--border-table-color-special)] bg-[var(--main-table-trans-spceial)]/64';
                            $focusButton =
                                'text-[var(--focus-color-special)] btn-card-focus-state-special ring-[var(--focus-color-special)]';
                            $mainColor = 'bg-[var(--main-color-special)]';
                        }

                        $borderTable = 'border-[var(--border-table-color-special)]';
                        $mainText = 'text-[var(--contrast-main-text-special)]';
                        $secondText = 'text-[var(--contrast-second-text-special)]';
                        $thirdText = 'text-[var(--contrast-third-text-special)]';

                        $focusColor = 'bg-[var(--focus-color-special)]';
                        $mainTable = 'bg-[var(--main-table-color-special)]';
                        $secondTable = 'bg-[var(--second-table-color-special)]';
                        $subTable = 'bg-[var(--sub-table-color-special)]';
                    } else {
                        if ($isPastDate) {
                            $focusDiv = 'border-[var(--border-table-color)]';
                            $focusButton =
                                'text-[var(--focus-color)] btn-card-focus-state-64 ring-[var(--focus-color)]/64';
                            $mainColor = 'bg-[var(--main-color)]/64';
                        } else {
                            $focusDiv = 'border-[var(--border-table-color)] bg-[var(--main-table-trans)]/64';
                            $focusButton = 'text-[var(--focus-color)] btn-card-focus-state ring-[var(--focus-color)]';
                            $mainColor = 'bg-[var(--main-color)]';
                        }
                        $borderTable = 'border-[var(--border-table-color)]';

                        $mainText = 'text-[var(--contrast-main-text)]';
                        $secondText = 'text-[var(--contrast-second-text)]';
                        $thirdText = 'text-[var(--contrast-third-text)]';

                        $focusColor = 'bg-[var(--focus-color)]';
                        $mainTable = 'bg-[var(--main-table-color)]';
                        $secondTable = 'bg-[var(--second-table-color)]';
                        $subTable = 'bg-[var(--sub-table-color)]';
                    }
                @endphp

<div x-cloak
        x-show="(() => { try { return window.checkSesiCardVisible({{ $s->id }}) === true; } catch(e) { return false; } })()"
        :style="'order: ' + (() => { try { return window.getSesiCardOrder({{ $s->id }}, {{ $index }}); } catch(e) { return {{ $index }}; } })()"
        class="{{ $isUjian ? 'lg:col-span-2' : '' }}">

                        <div wire:key="kelas-sesi-card-{{ $s->id }}" x-data="{
                            expanded: false,
                            hasLoaded: false
                        }"
                            @click="expanded = !expanded; hasLoaded = true"
                            class="{{ $focusDiv }} flex flex-col h-full flex-shrink-0 rounded-[20px] overflow-hidden border transition-all duration-200 hover:shadow-lg active:shadow-lg cursor-pointer">

                            {{-- ═══ HERO ═══ --}}
                            @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-table-card.sesi-card-partial.sesi-card-header')

                            {{-- ═══ BODY ═══ --}}
                            <div class="flex flex-1 flex-col gap-2.5 p-4" @click.stop>
                                @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-table-card.sesi-card-partial.sesi-card-main')

                                <div x-show="expanded" x-collapse.duration.300ms>
                                    @if (isset($this->dosens_by_sesi[$s->pertemuan_ke]))
                                        @include(
                                            'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-table-card.sesi-card-partial.sesi-card-expanded',
                                            [
                                                'allTimDosen' => $this->dosens_by_sesi[$s->pertemuan_ke],
                                            ]
                                        )
                                    @else
                                        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-table-card.sesi-card-partial.sesi-card-expanded-skeleton')
                                    @endif
                                </div>
                            </div>

                            {{-- ═══ FOOTER: toggle hint ═══ --}}
                            @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-table-card.sesi-card-partial.sesi-card-button')

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
                @include('livewire.global.table.pagination-alpine', ['mx' => ''])
                {{-- @if (Auth::user()->admin)
                    @include('livewire.global.table.trash-delete-switch', ['mx' => ''])
                @endif --}}
            </x-slot:footer>

<script>
    window.checkSesiCardVisible = function(id) {
        try {
            if (typeof window.Alpine === 'undefined') return false;
            
            const store = window.Alpine.store('sesi');
            if (!store || !Array.isArray(store.rawItems) || store.rawItems.length === 0) {
                return false;
            }

            const cp = Number(store.currentPage || 1) || 1;
            const pp = Number(store.perPage || 8) || 8;
            const start = (cp - 1) * pp;
            const end = start + pp;

            const list = store.filteredAndSortedIds || [];
            if (!Array.isArray(list) || list.length === 0) return false;

            const pageItems = list.slice(start, end);
            const isExist = pageItems.some(item => Number(item?.id) === Number(id));

            return isExist === true; // PASTI RETURN BOOLEAN
        } catch (e) {
            return false; // JIKA ERROR, PASTI RETURN FALSE
        }
    };

    window.getSesiCardOrder = function(id, fallbackIndex) {
        try {
            if (typeof window.Alpine === 'undefined') return fallbackIndex;
            const store = window.Alpine.store('sesi');
            const list = store?.filteredAndSortedIds || [];
            const idx = list.findIndex(entry => Number(entry?.id) === Number(id));
            return idx !== -1 ? idx : fallbackIndex;
        } catch (e) {
            return fallbackIndex;
        }
    };
</script>

        </x-global.main-layout-card>
    </div>
</div>
