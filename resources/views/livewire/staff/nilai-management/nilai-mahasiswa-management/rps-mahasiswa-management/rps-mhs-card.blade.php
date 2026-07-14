@php
    $alpineData = $nilais
        ->map(function ($n, $index) use ($mahasiswa) {
            $semNum = (string) ($n->semester ?? '');

            return [
                'id' => (int) $n->id,
                'dbIndex' => $index,

                'semester' => (int) $n->semester,
                'akademik' => strtolower($n->akademik ?? ''),
                'ganjil_genap' => strtolower($n->ganjil_genap ?? ''),
                'sks' => (int) ($n->sks ?? ($n->sks ?? 0)),

                'is_trashed' => $n->trashed(),

                'kode_mk' => strtolower($n->kode_mk ?? ''),
                'mk' => strtolower($n->mk ?? ''),
                'digit_mk' => strtolower($n->digit_mk ?? ''),
                'kode_rps' => strtolower($n->kode_rps ?? ''),
                'nim' => strtolower($mahasiswa->nim ?? ''),

                'nilai' => strtolower($n->nilai ?? '-'),
                'nilai_index' => strtolower($n->nilai_index ?? '-'),
                'nilai_mutu' => strtolower($n->nilai_mutu ?? ''),

                'nilai_semester' => $n->nilai_semester ?? '0.00',
                'ip_semester' => $n->ip_semester ?? '0.00',
                'mutu_semester' => strtoupper($n->mutu_semester ?? ''),
            ];
        })
        ->values()
        ->toArray();

    $jsonFreshData = json_encode($alpineData);

    /*
    |--------------------------------------------------------------------------
    | PERUBAHAN
    |--------------------------------------------------------------------------
    | Hash ini berubah setiap data berubah
    */
    $alpineVersion = md5(
        json_encode(
            $nilais
                ->map(
                    fn($n) => [
                        'id' => $n->id,
                        'updated_at' => optional($n->updated_at)->timestamp,
                    ],
                )
                ->values(),
        ),
    );
@endphp
<div wire:key="rps-mahasiswa-wrapper-{{ $alpineVersion }}" x-data="{
    rawItems: [],
    currentPage: 1,
    perPage: 8,
    sortField: '',
    sortDirection: 'asc',
    {{-- isRealtime: true, --}}

    get filteredAndSortedIds() {
        let query = (this.$store.nilai?.search || '').toLowerCase().trim();
        let filtered = [...this.rawItems];

        if (query) {
            let dotQuery = query.replace(',', '.');
            let alphanumericQuery = query.replace(/[^a-z0-9]/g, '');
            let queryWords = query.split(/\s+/).filter(word => word.length > 0);

            filtered = filtered.filter(item => {
                let cleanAkademik = item.akademik.replace(/[^a-z0-9]/g, '');
                let cleanKodeMk = item.kode_mk.replace(/[^a-z0-9]/g, '');
                let cleanKodeRps = item.kode_rps.replace(/[^a-z0-9]/g, '');

                let sksNum = String(item.sks);
                let sksVariations = [
                    sksNum, sksNum + 'sks', sksNum + ' sks',
                    sksNum + 'sk', sksNum + ' sk'
                ].join(' ');

                if (item.mk.includes(query) || (item.mk + ' ' + item.digit_mk).includes(query)) {
                    return true;
                }

                let targetText = [
                    item.kode_mk,
                    cleanKodeMk,
                    item.digit_mk,
                    item.kode_rps,
                    cleanKodeRps,
                    item.nim,
                    item.nilai,
                    String(item.nilai_index),
                    String(item.nilai_mutu),
                    item.akademik,
                    cleanAkademik,
                    item.ganjil_genap,
                    sksVariations,
                    'semester ' + item.semester,
                    's' + item.semester
                ].join(' ');

                let cocokSemuaKata = queryWords.every(word => targetText.includes(word));
                if (cocokSemuaKata) return true;

                if (alphanumericQuery && targetText.replace(/[^a-z0-9]/g, '').includes(alphanumericQuery)) {
                    return true;
                }

                let cleanSksQuery = query.replace(/(sks|sk|bobot|kredit)/g, '').trim();
                if (cleanSksQuery && String(item.sks) === cleanSksQuery) {
                    return true;
                }

                if (String(item.nilai_index).includes(query) || String(item.nilai_index).includes(dotQuery)) {
                    return true;
                }

                return false;
            });
        }

        let field = this.$store.nilai?.sortField || this.sortField;
        let direction = (this.$store.nilai?.sortDirection || this.sortDirection) === 'desc' ? -1 : 1;

        if (field) {
            if (field === 'sks') field = 'sks';

            filtered.sort((a, b) => {
                let valA = a[field];
                let valB = b[field];

                if (field === 'nilai' || field === 'nilai_index' || field === 'sks') {
                    let numA = parseFloat(valA) || 0;
                    let numB = parseFloat(valB) || 0;
                    if (numA !== numB) return (numA - numB) * direction;
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
                class="pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto">
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'digit_mk',
                    'headString' => 'No MK',
                    'alpine' => 'nilai',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'kode_rps',
                    'alpine' => 'nilai',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'mk',
                    'headString' => 'Mata Kuliah',
                    'alpine' => 'nilai',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'sks',
                    'alpine' => 'nilai',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'nilai',
                    'alpine' => 'nilai',
                ])
            </div>
        </x-slot:sortir>

        {{-- Slot Search --}}
        <x-slot:search>
            <div class="w-full md:w-96 xl:w-108">
                @include('livewire.global.search-and-filters.main-search', [
                    'placeholder' => 'Cari Mata Kuliah, Nilai, Index, atau Mutu...',
                    'alpine' => 'nilai',
                    'isLive' => 1,
                    'isBorder' => 2,
                ])
            </div>
        </x-slot:search>

        {{-- CONTAINER UTAMA WAJIB MEMILIKI CLASS flex ATAU grid UNTUK MENDUKUNG CSS ORDER --}}
        @foreach ($nilais as $index => $n)
            <div x-show="itemVisibilityMap[{{ $n->id }}]?.visible" x-transition
                :style="'order:' + (itemVisibilityMap[{{ $n->id }}]?.order ?? {{ $index }})">

                <div wire:key="rps-mahasiswa-{{ $n->id }}"
                    class="flex flex-col rounded-[20px] overflow-hidden border border-[var(--border-table-color)] bg-[var(--main-table-trans)]/50 transition-all duration-200 hover:shadow-lg active:shadow-lg">

                    {{-- ═══ HERO ═══ --}}
                    <div class="flex flex-col gap-3 p-[18px] bg-[var(--main-color)]">
                        <div class="flex items-start justify-between gap-2">
                            {{-- Kode RPS --}}
                            <div class="flex items-center gap-2">
                                <flux:dropdown>
                                    <button
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-white/20 bg-white/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-white/75 transition-colors hover:bg-white/20 active:bg-white/50 focus:outline-none cursor-pointer">
                                        <flux:icon name="academic-cap" class="w-3 h-3" />
                                        {{ $n->text_kode_mk ?? $n->kode_mk }}
                                    </button>
                                    @include(
                                        'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-toolbar-table',
                                        [
                                            'key' => 1,
                                        ]
                                    )
                                </flux:dropdown>
                                @if (Auth::user()->admin || Auth::user()->dosen)
                                    <span class="text-xs text-white/60 font-mono">ID:
                                        {{ $n->id }}</span>
                                @endif
                            </div>

                            {{-- Tombol Menu --}}
                            <flux:dropdown>
                                <button
                                    class="flex h-[30px] w-[30px] flex-shrink-0 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white/80 transition-colors hover:bg-white/20 active:bg-white/50 focus:outline-none cursor-pointer">
                                    <flux:icon name="ellipsis-vertical" class="w-4 h-4" />
                                </button>
                                @include(
                                    'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-toolbar-table',
                                    [
                                        'key' => 2,
                                    ]
                                )
                            </flux:dropdown>
                        </div>

                        {{-- Nama Mata Kuliah --}}
                        <p class="mt-1 text-[14px] font-bold leading-[1.35] tracking-[0.1em] text-[var(--main-text)]">
                            {{ $n->mk ?? '-' }} {{ $n->digit_mk }}
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
                                {{ $n->sks ?? ($n->sks ?? '-') }} SKS
                            </span>
                        </div>
                    </div>

                    {{-- ═══ BODY ═══ --}}
                    <div class="flex flex-1 flex-col gap-2.5 p-4">
                        <flux:dropdown>
                            <div
                                class="flex w-full items-center gap-1.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] pl-4 pr-2.5 py-2 text-left transition-colors focus:outline-none cursor-pointer">
                                <flux:icon name="document-text" class="w-3.5 h-3.5 text-[var(--contrast-third-text)]" />
                                <span
                                    class="text-[10px] font-bold uppercase tracking-[0.06em] text-[var(--contrast-third-text)]">RPS</span>
                                <span class="ml-auto text-xs font-semibold text-[var(--contrast-main-text)]">
                                    <button class="cursor-pointer focus:outline-none">
                                        @include('livewire.global.table.badge.level-mk-badge', [
                                            'xValue' => $n->kode_rps,
                                            'sortir' => $n->rps_rel?->mk_rel?->level_mk,
                                        ])
                                    </button>
                                </span>
                            </div>
                            @include(
                                'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mhs-toolbar-table',
                                [
                                    'key' => 3,
                                ]
                            )
                        </flux:dropdown>

                        <div class="grid grid-cols-3 gap-1.5">
                            <div
                                class="py-3 flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                                <span
                                    class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Nilai</span>
                                <span
                                    class="text-base font-bold leading-none text-[var(--contrast-main-text)]">{{ $n->nilai ?? '-' }}</span>
                            </div>
                            <div
                                class="py-3 flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                                <span
                                    class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Index</span>
                                <span
                                    class="text-base font-bold leading-none text-[var(--contrast-main-text)]">{{ number_format($n->nilai_index, 2) ?? '-' }}</span>
                            </div>
                            @include(
                                'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.nilai-mutu',
                                ['value' => $n->nilai_mutu]
                            )
                        </div>
                    </div>

                    {{-- ═══ FOOTER ═══ --}}
                    <div class="px-4 pb-4 flex items-center gap-1.5">
                        <button
                            class="flex w-full items-center justify-center gap-1.5 rounded-bl-[11px] rounded-r-[4px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] transition-all
                            {{ $n->trashed()
                                ? 'cursor-not-allowed bg-gray-100 dark:bg-zinc-800/50 text-gray-400 dark:text-zinc-500 ring-1 ring-gray-200 dark:ring-zinc-800'
                                : 'cursor-pointer bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] btn-card-focus-state active:scale-[0.99]' }}"
                            {{ $n->trashed() ? 'disabled' : '' }}
                            @if (!$n->trashed()) @click="
                                $store.nilai?.reset();
                                $store.nilai?.setEdit(1);
                                $store.nilai?.setColor('text-cyan-700 dark:text-cyan-400');
                                $store.nilai?.setValueNilai(
                                    '{{ $n->id ?? '' }}',
                                    '{{ $mahasiswa->name ?? '' }}',
                                    '{{ $mahasiswa->nim ?? '' }}',

                                    '{{ $n->kode_rps ?? '' }}',
                                    '{{ $n->mk ?? '' }}',
                                    '{{ $n->sks ?? '' }}',

                                    JSON.parse('{{ json_encode($n->nilai_array ?? []) }}'),
                                    JSON.parse('{{ json_encode($n->bobot_rps_array ?? []) }}'),
                                    JSON.parse('{{ json_encode($n->kode_cpmk_array ?? []) }}'),
                                    JSON.parse('{{ json_encode($n->kode_scpmk_array ?? []) }}'),
                                    JSON.parse('{{ json_encode($n->metode_array ?? []) }}'),
                                );
                                $flux.modal('rps-mahasiswa-modal').show();
                                $dispatch('open-edit-rps-mahasiswa-modal', { id: {{ $n->id }} });
                            " @endif>
                            @if (Auth::user()->admin || Auth::user()->dosen)
                                <flux:icon name="pencil-square"
                                    class="w-3.5 h-3.5 {{ $n->trashed() ? 'opacity-40' : '' }}" />
                                <span>Edit Nilai</span>
                            @else
                                <flux:icon name="eye"
                                    class="w-3.5 h-3.5 {{ $n->trashed() ? 'opacity-40' : '' }}" />
                                <span>Lihat Nilai</span>
                            @endif
                        </button>
                        <button
                            class="cursor-pointer flex w-full items-center justify-center gap-1.5 rounded-br-[11px] rounded-l-[4px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] btn-card-focus-state transition-all active:scale-[0.99]"
                            @click="
                                $store.rps?.resetShow();
                                $store.rps?.setShowRPS(
                                    '{{ $n->rps_id ?? '' }}',
                                    '{{ $n->rps_rel->kode ?? '' }}',
                                    '{{ $mahasiswa->pr_id ?? '' }}',
                                );
                                $store.rps?.setColor('text-green-700 dark:text-green-400');
                                $flux.modal('rps-detail-modal').show();
                                $dispatch('open-show-rps-modal', { id: {{ $n->rps_id }}, prId: {{ $mahasiswa->pr_id }} });
                            ">
                            <flux:icon name="clipboard-document-list" class="w-3.5 h-3.5" />
                            <span>Lihat RPS</span>
                        </button>
                    </div>

                </div>
            </div>
            {{-- </template> --}}
        @endforeach

        {{-- EMPTY STATE ANCHOR --}}
        <x-slot:emptys>
            <div x-show="totalFilteredItems === 0"
                class="col-span-6 text-center p-12 rounded-xl border border-dashed table-border bg-[var(--main-table-trans)]">
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)]">Tidak ada rincian nilai Mata Kuliah
                    yang ditemukan
                    untuk Periode ini!</p>
            </div>
        </x-slot:emptys>

        {{-- Slot Footer Pagination --}}
        <x-slot:footer>
            @include('livewire.global.table.pagination-alpine')
            @include('livewire.global.table.trash-delete')
        </x-slot:footer>

    </x-global.main-layout-card>
</div>
