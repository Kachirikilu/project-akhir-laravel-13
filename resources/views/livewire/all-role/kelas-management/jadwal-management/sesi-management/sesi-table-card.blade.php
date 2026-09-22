<div wire:key="view-{{ $this->switchTable }}-sesi">

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

        @if ($this->switchTable == 'table')
            @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-table-card.sesi-table')
        @elseif ($this->switchTable == 'card' || $this->switchTable == 'hari-ini')
            @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-table-card.sesi-card')
        @endif

    </div>
</div>
