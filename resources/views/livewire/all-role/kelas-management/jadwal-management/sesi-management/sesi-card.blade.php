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
        {{-- @php
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
        @endphp --}}

        {{-- @php
            $pertemuan = $index + 1; 
            $pengajar_collection = $this->dosens_by_pertemuan[$pertemuan] ?? collect();
        @endphp --}}

        @foreach ($sesis as $index => $s)
            @php
                $isUjian = in_array(strtoupper($s->metode ?? ''), $daftarUjian);
                $kehadiran_mhs = Auth::user()->mahasiswa
                    ? $s->kehadirans->where('mahasiswa_id', Auth::user()->mahasiswa->id)->first()
                    : null;
            @endphp

            {{-- <template x-if="itemVisibilityMap[{{ $s->id }}]?.visible"> --}}
            <div x-show="itemVisibilityMap[{{ $s->id }}]?.visible" x-transition
                class="{{ $isUjian ? 'lg:col-span-2' : '' }}">
                <div wire:key="kelas-sesi-card-{{ $s->id }}" x-data="{
                    {{-- expanded: {{ $isUjian ? 'true' : 'false' }},
                    hasLoaded: {{ $isUjian ? 'true' : 'false' }} --}}
                    expanded: false,
                        hasLoaded: false
                }"
                    :style="'order: ' + (itemVisibilityMap[{{ $s->id }}]?.order ?? {{ $index }})"
                    @click="expanded = !expanded; hasLoaded = true"
                    class="flex flex-col h-full flex-shrink-0 rounded-[20px] overflow-hidden border transition-all duration-200 hover:shadow-lg active:shadow-lg cursor-pointer
                            {{ $isUjian ? 'ring-1 ring-[var(--focus-color-special)] border-[var(--border-table-color-special)] bg-[var(--main-table-trans-spceial)]/50' : 'border-[var(--border-table-color)] bg-[var(--main-table-trans)]/50' }}">

                    @php
                        if ($isUjian) {
                            $bgBorder = 'border-[var(--border-table-color-special)] bg-[var(--second-table-color-special)]';
                            $mainText = 'text-[var(--contrast-main-text-special)]';
                            $secondText = 'text-[var(--contrast-second-text-special)]';
                            $thirdText = 'text-[var(--contrast-third-text-special)]';
                        } else {
                            $bgBorder =  'border-[var(--border-table-color)] bg-[var(--second-table-color)]';
                            $mainText = 'text-[var(--contrast-main-text]';
                            $secondText = 'text-[var(--contrast-second-text)]';
                            $thirdText = 'text-[var(--contrast-third-text)]';
                        }
                    @endphp
                    {{-- ═══ HERO ═══ --}}
                    @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-card.sesi-card-header')

                    {{-- ═══ BODY ═══ --}}
                    <div class="flex flex-1 flex-col gap-2.5 p-4" @click.stop>
                        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-card.sesi-card-main')

                        <div x-show="expanded" x-collapse.duration.300ms>
                            @if (isset($this->dosens_by_sesi[$s->pertemuan_ke]))
                                @include(
                                    'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-card.sesi-card-expanded',
                                    [
                                        'allTimDosen' => $this->dosens_by_sesi[$s->pertemuan_ke],
                                    ]
                                )
                            @else
                                @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-card.sesi-card-expanded-skeleton')
                            @endif
                        </div>
                    </div>

                    {{-- ═══ FOOTER: toggle hint ═══ --}}
                    {{-- @if (!$isUjian) --}}
                    <div class="px-4 pb-4" @click.stop>
                        <button
                            class="cursor-pointer flex w-full items-center justify-center gap-1.5 rounded-[11px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] bg-transparent transition-all active:scale-[0.99]
                                {{ $isUjian
                                    ? 'btn-card-focus-state-special ring-1 ring-[var(--focus-color-special)]'
                                    : 'btn-card-focus-state text-[var(--focus-color)] ring-1 ring-[var(--focus-color)]'
                                }}"
                            @click="
                                if (!hasLoaded) { 
                                    $wire.loadData({{ $s->pertemuan_ke }}); 
                                    hasLoaded = true; 
                                }
                                expanded = !expanded;
                            ">
                            <flux:icon name="chevron-down" class="w-3.5 h-3.5 transition-transform duration-300"
                                ::class="{ 'rotate-180': expanded }" />

                            <span x-text="expanded ? 'Sembunyikan Detail' : 'Lihat Detail'"></span>
                        </button>
                    </div>
                    {{-- @endif --}}

                </div>
            </div>
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
