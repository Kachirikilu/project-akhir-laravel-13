@php
    $alpineData = $periodes
        ->map(function ($p, $index) {
            $semNum = (string) ($p->semester ?? '');
            $ganjilGenap = strtolower($p->ganjil_genap ?? '');
            $akademik = strtolower($p->akademik ?? '');
            $cleanAkademik = str_replace(['/', '-'], '', $akademik);

            return [
                'id' => $p->id ?? $index,
                'dbIndex' => $index,
                'semester' => (int) $p->semester,
                'akademik' => $akademik,
                'ganjil_genap' => $ganjilGenap,
                'total_mk' => (int) ($p->total_mk ?? 0),
                'total_sks' => (int) ($p->total_sks ?? 0),
                'nilai_semester' => $p->nilai_semester ?? '0.00',
                'ip_semester' => $p->ip_semester ?? '0.00',
                'mutu_semester' => strtoupper($p->mutu_semester ?? ''),

                'search_combinations' => [
                    $ganjilGenap . ' ' . $akademik,
                    $ganjilGenap . ' ' . $cleanAkademik,
                    $akademik . ' ' . $ganjilGenap,
                    $cleanAkademik . ' ' . $ganjilGenap,
                    'semester ' . $semNum . ' ' . $ganjilGenap,
                ],
                'search_semester' => [
                    $semNum,
                    's' . $semNum,
                    's-' . $semNum,
                    'semester' . $semNum,
                    'semester ' . $semNum,
                ],
            ];
        })
        ->values()
        ->toArray();

    $jsonFreshData = json_encode($alpineData);

    $alpineVersion = md5(
        collect($periodes)
            ->map(
                fn($p) => [
                    $p->id ?? null,
                    $p->semester ?? null,
                    $p->akademik ?? null,
                    $p->ganjil_genap ?? null,
                    $p->ip_semester ?? null,
                    $p->nilai_semester ?? null,
                ],
            )
            ->toJson(),
    );
@endphp

<div wire:key="periode-wrapper-{{ $alpineVersion }}" x-data="{
    rawItems: [],
    currentPage: 1,
    perPage: 8,
    sortField: '',
    sortDirection: 'asc',

    isRealtime: true,

    get filteredAndSortedIds() {
        let query = (this.$store.periode?.search || '').toLowerCase().trim();

        let dotQuery = query.replace(',', '.');
        let cleanQuery = query.replace(/[\/\s\-]+/g, ' ').trim();
        let alphanumericQuery = query.replace(/[^a-z0-9]/g, '');

        let cleanDataQuery = query
            .replace(/^(index|mutu|huruf|nilai|ip|ips|khs)\s+/g, '')
            .replace(/\s*(index|mutu|huruf|nilai|ip|ips|khs)\s*/g, '')
            .trim();
        let dotCleanDataQuery = cleanDataQuery.replace(',', '.');

        let queryWords = query.split(/\s+/).filter(word => word.length > 0);

        // 1. PROSES FILTERING
        let filtered = [...this.rawItems];

        if (query) {
            filtered = filtered.filter(item => {
                let itemCleanAkademik = item.akademik.replace(/[^a-z0-9]/g, '');

                let mkNum = String(item.total_mk);
                let mkVariations = [
                    mkNum,
                    mkNum + 'mk',
                    mkNum + ' mk',
                    mkNum + 'mata kuliah',
                    mkNum + ' kuliah',
                    mkNum + ' kuliahan',
                ].join(' ');

                let sksNum = String(item.total_sks);
                let sksVariations = [
                    sksNum,
                    sksNum + 'sks',
                    sksNum + ' sks',
                    sksNum + 'sk',
                    sksNum + ' sk',
                    sksNum + 'bobot',
                    sksNum + ' bobot',
                    sksNum + 'kredit',
                    sksNum + ' kredit'
                ].join(' ');

                // Satukan semua data target teks panjang
                let targetText = [
                    item.akademik,
                    itemCleanAkademik,
                    item.ganjil_genap,
                    item.mutu_semester.toLowerCase(),
                    String(item.ip_semester),
                    String(item.nilai_semester),
                    mkVariations,
                    sksVariations,
                    'semester ' + item.semester,
                    's' + item.semester
                ].join(' ');

                let cocokSemuaKata = queryWords.every(word => targetText.includes(word));
                if (cocokSemuaKata) return true;

                if (item.mutu_semester === query.toUpperCase() ||
                    (cleanDataQuery && item.mutu_semester === cleanDataQuery.toUpperCase())) {
                    return true;
                }

                if (String(item.ip_semester).includes(query) || String(item.ip_semester).includes(dotQuery) ||
                    String(item.nilai_semester).includes(query) || String(item.nilai_semester).includes(dotQuery)) {
                    return true;
                }

                if (cleanDataQuery && (
                        String(item.ip_semester).includes(cleanDataQuery) ||
                        String(item.ip_semester).includes(dotCleanDataQuery) ||
                        String(item.nilai_semester).includes(cleanDataQuery) ||
                        String(item.nilai_semester).includes(dotCleanDataQuery)
                    )) {
                    return true;
                }

                let cleanMkQuery = query.replace(/(mk|mata kuliah|kuliah|kuliahan)/g, '').trim();
                if (cleanMkQuery && String(item.total_mk) === cleanMkQuery) {
                    return true;
                }

                let cleanSksQuery = query.replace(/(sks|sk|bobot|kredit)/g, '').trim();
                if (cleanSksQuery && String(item.total_sks) === cleanSksQuery) {
                    return true;
                }

                return false;
            });
        }

        // 2. PROSES SORTING
        let field = this.$store.periode?.sortField || this.sortField;
        let direction = (this.$store.periode?.sortDirection || this.sortDirection) === 'desc' ? -1 : 1;

        if (field) {
            filtered.sort((a, b) => {
                let valA = a[field];
                let valB = b[field];

                if (field === 'ip_semester' || field === 'nilai_semester') {
                    return (parseFloat(valA) - parseFloat(valB)) * direction;
                }

                if (typeof valA === 'number' && typeof valB === 'number') {
                    return (valA - valB) * direction;
                }

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
        this.$watch('$store.periode.search', () => { this.currentPage = 1; });

        this.$watch('$store.periode.sortField', () => { this.currentPage = 1; });
        this.$watch('$store.periode.sortDirection', () => { this.currentPage = 1; });

        this.$watch('$store.periode.perPage', (val) => {
            this.perPage = val || 8;
            this.currentPage = 1;
        });
    }
}" x-init="rawItems = {{ $jsonFreshData }};" class="w-full">

    <x-global.main-layout-card>

        {{-- Slot Sortir --}}
        <x-slot:sortir>
            <div
                class="pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto">
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'semester',
                    'alpine' => 'periode',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'ip_semester',
                    'alpine' => 'periode',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'total_mk',
                    'alpine' => 'periode',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'total_sks',
                    'alpine' => 'periode',
                ])
            </div>
        </x-slot:sortir>

        {{-- Slot Search --}}
        <x-slot:search>
            <div class="w-full md:w-96 xl:w-108">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Semester, IP, Mutu, atau Tahun Akademik...',
                    'alpine' => 'periode',
                    'isLive' => 1,
                    'isBorder' => 2,
                ])
            </div>
        </x-slot:search>

        {{-- GRID UTAMA KARTU --}}
        @foreach ($periodes as $index => $p)
            @if (empty($p->ganjil_genap) || empty($p->akademik))
                @continue
            @endif
            @php $currentId = $p->id ?? $index; @endphp
            <template x-if="itemVisibilityMap[{{ $currentId }}]?.visible">
                <div wire:key="kelas-periode-card-{{ $currentId }}"
                    :style="'order: ' + (itemVisibilityMap[{{ $currentId }}]?.order ?? 999)"
                    class="flex flex-col rounded-[20px] overflow-hidden border border-[var(--border-table-color)] bg-[var(--main-table-trans)]/50 transition-all duration-200 hover:shadow-lg active:shadow-lg">

                    {{-- ═══ HERO ═══ --}}
                    <div class="flex flex-col gap-3 p-[18px] bg-[var(--main-color)]">
                        <div class="flex items-start justify-between gap-2">
                            <button
                                class="inline-flex items-center gap-1.5 rounded-lg border border-white/20 bg-white/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-white/75 transition-colors hover:bg-white/20 active:bg-white/50 focus:outline-none cursor-pointer">
                                <flux:icon name="academic-cap" class="w-3 h-3" />
                                Semester {{ $p->semester ?? '-' }}
                            </button>
                        </div>

                        <p class="mt-1 text-[15px] font-bold leading-[1.35] tracking-[0.24em] text-[var(--main-text)]">
                            {{ $p->ganjil_genap }} - {{ $p->akademik }}
                        </p>

                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="inline-flex items-center gap-1.5 text-[11px] font-medium text-[var(--main-text)]/65">
                                <flux:icon name="users" class="w-3 h-3" />
                                {{ $nim_url ?? '-' }}
                            </span>
                            <span class="h-[3px] w-[3px] flex-shrink-0 rounded-full bg-[var(--main-text)]/30"></span>
                            <span
                                class="inline-flex items-center gap-1.5 text-[11px] font-medium text-[var(--main-text)]/65">
                                <flux:icon name="academic-cap" class="w-3 h-3" />
                                {{ $mahasiswa->pr_rel->prodi }}
                            </span>
                        </div>
                    </div>

                    {{-- ═══ BODY ═══ --}}
                    <div class="flex flex-1 flex-col gap-2.5 p-4">

                        <div class="flex flex-col gap-1.5">
                        <div
                            class="flex w-full items-center gap-1.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-4 py-2 text-left transition-colors focus:outline-none cursor-pointer">
                            <flux:icon name="rectangle-stack" class="w-3.5 h-3.5 text-[var(--contrast-third-text)]" />
                            <span
                                class="text-[10px] font-bold uppercase tracking-[0.06em] text-[var(--contrast-third-text)]">Total
                                Mata Kuliah</span>
                            <span class="ml-auto text-xs font-semibold text-[var(--contrast-main-text)]">
                                {{ $p->total_mk }} MK
                            </span>
                        </div>
                        <div
                            class="flex w-full items-center gap-1.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-4 py-2 text-left transition-colors focus:outline-none cursor-pointer">
                            <flux:icon name="document-text" class="w-3.5 h-3.5 text-[var(--contrast-third-text)]" />
                            <span
                                class="text-[10px] font-bold uppercase tracking-[0.06em] text-[var(--contrast-third-text)]">Total
                                SKS</span>
                            <span class="ml-auto text-xs font-semibold text-[var(--contrast-main-text)]">
                                {{ $p->total_sks }} SKS
                            </span>
                        </div>
                        </div>


                        <div class="grid grid-cols-3 gap-1.5">
                            <div
                                class="py-3 flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                                <span
                                    class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Nilai</span>
                                <span
                                    class="text-base font-bold leading-none text-[var(--contrast-main-text)]">{{ $p->nilai_semester ?? '-' }}</span>
                            </div>

                            <div
                                class="py-3 flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                                <span
                                    class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">IP
                                    Semester</span>
                                <span
                                    class="text-base font-bold leading-none text-[var(--contrast-main-text)]">{{ $p->ip_semester ?? '-' }}</span>
                            </div>

                            @include(
                                'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.nilai-mutu',
                                ['value' => $p->mutu_semester]
                            )
                        </div>
                    </div>

                    {{-- ═══ FOOTER ═══ --}}
                    <div class="px-4 pb-4 flex items-center gap-1.5">
                        <button
                            class="flex w-full items-center justify-center gap-1.5 rounded-b-[11px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] transition-all
                            {{ $showDeleted
                                ? 'cursor-not-allowed bg-gray-100 dark:bg-zinc-800/50 text-gray-400 dark:text-zinc-500 ring-1 ring-gray-200 dark:ring-zinc-800'
                                : 'cursor-pointer bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] btn-card-focus-state active:scale-[0.99]' }}"
                            {{ $showDeleted ? 'disabled' : 'href=' . ($isNilaiMhs ? route('rps-mahasiswa', ['ganjil_genap' => $p->ganjil_genap, 'akademik' => str_replace('/', '-', $p->akademik)]) : route('rps-mahasiswa-management', ['nim' => $this->nim_url, 'ganjil_genap' => $p->ganjil_genap, 'akademik' => str_replace('/', '-', $p->akademik)])) . ' wire:navigate' }}>
                            <flux:icon name="eye" class="w-3.5 h-3.5 {{ $showDeleted ? 'opacity-40' : '' }}" />
                            <span>Lihat Detail Nilai</span>
                        </button>
                    </div>
                </div>
            </template>
        @endforeach

        {{-- EMPTY STATE ANCHOR --}}
        <x-slot:emptys>
            <div x-show="totalFilteredItems === 0"
                class="col-span-6 text-center p-12 rounded-xl border border-dashed table-border bg-[var(--main-table-trans)]">
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)]">Tidak ada data Periode Semester
                    ditemukan!</p>
            </div>
        </x-slot:emptys>

        {{-- Slot Footer Pagination --}}
        <x-slot:footer>
            @include('livewire.global.table.pagination-alpine', ['mx' => ''])
            @include('livewire.global.table.trash-delete', ['mx' => ''])
        </x-slot:footer>

    </x-global.main-layout-card>
</div>
