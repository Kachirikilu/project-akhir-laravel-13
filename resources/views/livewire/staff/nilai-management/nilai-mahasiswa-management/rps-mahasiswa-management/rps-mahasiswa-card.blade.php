<div wire:key="view-card-rps-mahasiswa">

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
    
        get currentPage() {
            return Number(this.$store.periode?.currentPage ?? 1) || 1;
        },
        set currentPage(val) {
            this.$store.periode.currentPage = Number(val) || 1;
        },
    
        get perPage() {
            return Number(this.$store.periode?.perPage ?? 8) || 8;
        },
        set perPage(val) {
            const next = Number(val) || 8;
            if (this.$store.periode?.perPage !== next) {
                this.$store.periode.perPage = next;
            }
        },
    
        get sortField() {
            return this.$store.periode?.sortField ?? 'digit_mk';
        },
        set sortField(val) {
            this.$store.periode.sortField = val ?? 'digit_mk';
        },
    
        get sortDirection() {
            return this.$store.periode?.sortDirection ?? 'desc';
        },
        set sortDirection(val) {
            this.$store.periode.sortDirection = val ?? 'desc';
        },
    
        get filteredAndSortedIds() {
            const normalize = (value) => {
                return String(value ?? '')
                    .toLowerCase()
                    .replace(/[^a-z0-9]/g, '');
            };
    
            const query = (this.$store.periode?.search || '')
                .toLowerCase()
                .trim();
    
            const cleanQuery = normalize(query);
    
            let filtered = this.rawItems.filter(item => {
                if (!query) return true;
    
                const targetText = [
                        item.kode_mk,
                        item.mk,
                        item.digit_mk,
                        item.kode_rps,
                        item.nim,
                        item.nilai,
                        String(item.nilai_index),
                        String(item.nilai_mutu),
                        item.akademik,
                        item.ganjil_genap,
                        String(item.semester),
                        String(item.sks),
                        'semester ' + item.semester,
                        's' + item.semester,
                    ]
                    .join(' ')
                    .toLowerCase();
    
                if (targetText.includes(query)) {
                    return true;
                }
    
                if (cleanQuery && normalize(targetText).includes(cleanQuery)) {
                    return true;
                }
    
                return false;
            });
    
            const field = this.$store.periode?.sortField || this.sortField;
            const direction =
                (this.$store.periode?.sortDirection || this.sortDirection) === 'desc' ?
                -1 :
                1;
    
            const sortedFiltered = [...filtered];
    
            const parseNumber = (value) => {
                if (value === null || value === undefined || value === '') {
                    return 0;
                }
    
                const normalized = String(value)
                    .trim()
                    .replace(/[^0-9,.-]/g, '')
                    .replace(',', '.');
    
                const num = Number(normalized);
    
                return Number.isFinite(num) ? num : 0;
            };
    
            if (field) {
                sortedFiltered.sort((a, b) => {
                    const fallbackOrder = () =>
                        Number(a.dbIndex) - Number(b.dbIndex);
    
                    /*
                    |--------------------------------------------------------------------------
                    | SORTING NUMERIK
                    |--------------------------------------------------------------------------
                    */
                    if (
                        field === 'semester' ||
                        field === 'sks' ||
                        field === 'nilai' ||
                        field === 'nilai_index' ||
                        field === 'nilai_mutu'
                    ) {
                        const numA = parseNumber(a[field]);
                        const numB = parseNumber(b[field]);
    
                        if (numA !== numB) {
                            return (numA - numB) * direction;
                        }
    
                        return fallbackOrder();
                    }
    
                    /*
                    |--------------------------------------------------------------------------
                    | SORTING TEKS
                    |--------------------------------------------------------------------------
                    */
                    const valA = a[field];
                    const valB = b[field];
    
                    const textA = String(valA ?? '')
                        .trim()
                        .toLowerCase();
    
                    const textB = String(valB ?? '')
                        .trim()
                        .toLowerCase();
    
                    const result = textA.localeCompare(
                        textB,
                        'id', {
                            numeric: true,
                            sensitivity: 'base'
                        }
                    );
    
                    return result !== 0 ?
                        result * direction :
                        fallbackOrder();
                });
            } else {
                sortedFiltered.sort((a, b) =>
                    Number(a.dbIndex) - Number(b.dbIndex)
                );
            }
    
            return sortedFiltered;
        },
    
        get pageIds() {
            const start = (this.currentPage - 1) * this.perPage;
            const end = Math.min(
                start + this.perPage,
                this.filteredAndSortedIds.length
            );
    
            return this.filteredAndSortedIds
                .slice(start, end)
                .map(item => item.id);
        },
    
        get itemVisibilityMap() {
            let map = {};
            const visibleIds = new Set(this.pageIds);
    
            this.filteredAndSortedIds.forEach((item) => {
                map[item.id] = {
                    visible: visibleIds.has(item.id),
                    order: this.filteredAndSortedIds.findIndex(
                        entry => entry.id === item.id
                    )
                };
            });
    
            return map;
        },
    
        get totalFilteredItems() {
            return this.filteredAndSortedIds.length;
        },
    
        get totalPages() {
            return Math.max(
                1,
                Math.ceil(this.totalFilteredItems / this.perPage)
            );
        },
    
        init() {
            if (this.$store.periode) {
                this.$store.periode.sortField = 'digit_mk';
            }
    
            this.$watch('$store.periode.search', () => {
                this.currentPage = 1;
            });
    
            this.$watch('$store.periode.sortField', () => {
                this.currentPage = 1;
            });
    
            this.$watch('$store.periode.sortDirection', () => {
                this.currentPage = 1;
            });
    
            this.$watch('$store.periode.perPage', (val) => {
                const next = Number(val) || 8;
    
                if (this.perPage !== next) {
                    this.perPage = next;
                }
    
                const totalPages = Math.max(
                    1,
                    Math.ceil(this.filteredAndSortedIds.length / this.perPage)
                );
    
                this.currentPage = Math.min(
                    this.currentPage || 1,
                    totalPages
                );
            });
        }
    }" x-init="rawItems = {{ $jsonFreshData }};"
        class="w-full">

        <x-global.main-layout-card>

            {{-- Slot Sortir --}}
            <x-slot:leftSecHead>
                <div
                    class="pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto">
                    @include('livewire.global.table.head-sortir', [
                        'sortFieldString' => 'digit_mk',
                        'headString' => 'No MK',
                        'alpine' => 'periode',
                    ])
                    @include('livewire.global.table.head-sortir', [
                        'sortFieldString' => 'kode_rps',
                        'alpine' => 'periode',
                    ])
                    @include('livewire.global.table.head-sortir', [
                        'sortFieldString' => 'mk',
                        'headString' => 'Mata Kuliah',
                        'alpine' => 'periode',
                    ])
                    @include('livewire.global.table.head-sortir', [
                        'sortFieldString' => 'sks',
                        'alpine' => 'periode',
                    ])
                    @include('livewire.global.table.head-sortir', [
                        'sortFieldString' => 'nilai',
                        'alpine' => 'periode',
                    ])
                </div>
            </x-slot:leftSecHead>


            <x-slot:rightSecHead>
                <div class="w-full md:w-110 xl:w-124">
                    <div class="col-start-1 row-start-1 w-full flex items-center justify-between gap-4">
                        <div class="flex-shrink-0">
                            @include('livewire.global.search-and-filters.page-control', [
                                'perPageOptions' => [2, 4, 8, 16],
                                'alpine' => 'periode',
                                'key' => 'page-control-rps-mahasiswa-card',
                                'withB' => 0,
                                'isSmall' => 1,
                            ])
                        </div>

                        <div class="flex-grow max-w-md">
                            @include('livewire.global.search-and-filters.main-search', [
                                'placeholder' => 'Cari Mata Kuliah, Nilai, Index, atau Mutu...',
                                'alpine' => 'periode',
                                'isLive' => 1,
                                'isBorder' => 2,
                            ])
                        </div>
                    </div>
                </div>
            </x-slot:rightSecHead>


            @foreach ($nilais as $index => $n)
                <div x-show="filteredAndSortedIds.slice((currentPage - 1) * perPage, currentPage * perPage).some(item => Number(item.id) === Number({{ $n->id }}))"
                    class="contents">

                    {{-- Layer 2: Menjadi Direct Child Visual Grid menggunakan CSS 'display: contents' --}}
                    <div :style="'order: ' + filteredAndSortedIds.findIndex(item => Number(item.id) === Number({{ $n->id }}))"
                        wire:key="rps-mahasiswa-{{ $n->id }}"
                        class="h-full flex flex-col rounded-[20px] overflow-hidden border border-[var(--border-table-color)] bg-[var(--main-table-trans)]/50 transition-all duration-200 hover:shadow-lg active:shadow-lg">

                        {{-- Layer 3: Card Body dengan wire:key terisolasi --}}

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
                                            'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mahasiswa-toolbar-table',
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
                                        'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mahasiswa-toolbar-table',
                                        [
                                            'key' => 2,
                                        ]
                                    )
                                </flux:dropdown>
                            </div>

                            {{-- Nama Mata Kuliah --}}
                            <p
                                class="mt-1 text-[14px] font-bold leading-[1.35] tracking-[0.1em] text-[var(--main-text)]">
                                {{ $n->mk ?? '-' }} {{ $n->digit_mk }}
                            </p>

                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 text-[11px] font-medium text-[var(--main-text)]/65">
                                    <flux:icon name="users" class="w-3 h-3" />
                                    {{ $nim_url ?? '-' }}
                                </span>
                                <span
                                    class="h-[3px] w-[3px] flex-shrink-0 rounded-full bg-[var(--main-text)]/30"></span>
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
                                    <flux:icon name="document-text"
                                        class="w-3.5 h-3.5 text-[var(--contrast-third-text)]" />
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-[0.06em] text-[var(--contrast-third-text)]">RPS</span>
                                    <span class="ml-auto text-xs font-semibold text-[var(--contrast-main-text)]">
                                        <button class="cursor-pointer focus:outline-none">
                                            @include('livewire.global.table.badge.tingkat-mk-badge', [
                                                'xValue' => $n->kode_rps,
                                                'sortir' => $n->rps_rel?->mk_rel?->tingkat_mk,
                                            ])
                                        </button>
                                    </span>
                                </div>
                                @include(
                                    'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.rps-mahasiswa-toolbar-table',
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
                @include('livewire.global.table.pagination-alpine', ['mx' => ''])
                @include('livewire.global.table.trash-delete-switch', ['mx' => ''])
            </x-slot:footer>

        </x-global.main-layout-card>
    </div>
</div>
