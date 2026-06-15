@php
    $alpineData = $jadwals
        ->map(function ($s, $index) {
            $stringKodeJadwal = $s->kode ?? '';

            return [
                'id' => $s->id,
                'dbIndex' => $index,

                'tanggal_pelaksanaan' => $s->tanggal_pelaksanaan ?? '',

                'jam_pelaksanaan' => $s->jam_pelaksanaan ?? '',
                'searchJamPelaksanaan' => preg_replace('/[^A-Za-z0-9]/', '', strtolower($s->jam_pelaksanaan ?? '')),
                'label_extra' => $s->label_full ?? '',
                'hari' => $s->hari ?? '',

                'kode' => strtolower($stringKodeJadwal),
                'searchKodeJadwal' => preg_replace('/[^A-Za-z0-9]/', '', strtolower($stringKodeJadwal)),
                'label_full' => $s->label_full ?? '',
                'searchLabelFull' => preg_replace('/[^A-Za-z0-9]/', '', strtolower($s->label_full ?? '')),

                'searchHariPelaksanaan' => preg_replace('/[^A-Za-z0-9]/', '', strtolower($s->hari ?? '')),
                'tanggal' => $s->tanggal ?? '',
                'searchTanggalPelaksanaan' => preg_replace('/[^A-Za-z0-9]/', '', strtolower($s->tanggal ?? '')),
                'kapasitas' => $s->kapasitas ?? '',
                'searchKapasitas' => preg_replace('/[^A-Za-z0-9]/', '', strtolower($s->kapasitas ?? '')),
            ];
        })
        ->values()
        ->toArray();
@endphp
<div x-data="{
    rawItems: {{ json_encode($alpineData) }},
    currentPage: 1,
    perPage: 8,
    sortField: '',
    sortDirection: 'asc',

get filteredAndSortedIds() {
        let query = (this.$store.jadwal?.search || '').toLowerCase().trim();
        let cleanQuery = query.replace(/[^a-z0-9]/g, '');

        let filtered = this.rawItems.filter(item => {
            if (!query) return true;
            if (item.kode.includes(query) || (cleanQuery && item.searchKodeJadwal.includes(cleanQuery))) {
                return true;
            }
            if (String(item.label_full).toLowerCase().includes(query) || (cleanQuery && item.searchLabelFull.includes(cleanQuery))) {
                return true;
            }
            if (String(item.hari).toLowerCase().includes(query) || (cleanQuery && item.searchHariPelaksanaan.includes(cleanQuery))) {
                return true;
            }
            if (String(item.jam_pelaksanaan).toLowerCase().includes(query) || (cleanQuery && item.searchJamPelaksanaan.includes(cleanQuery))) {
                return true;
            }
            if (String(item.tanggal).toLowerCase().includes(query) || (cleanQuery && item.searchTanggalPelaksanaan.includes(cleanQuery))) {
                return true;
            }
            if (String(item.kapasitas).includes(query) || (cleanQuery && item.searchKapasitas.includes(cleanQuery))) {
                return true;
            }

            return false;
        });

        let field = this.$store.jadwal?.sortField || this.sortField;
        let direction = (this.$store.jadwal?.sortDirection || this.sortDirection) === 'desc' ? -1 : 1;

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
        this.$watch('$store.jadwal.search', () => { this.currentPage = 1; });
        this.$watch('$store.jadwal.perPage', (val) => {
            this.perPage = val || 8;
            this.currentPage = 1;
        });
    }
}" class="w-full">

    <x-global.main-layout-card>

        {{-- Slot Sortir --}}
        <x-slot:sortir>
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'kode',
                'alpine' => 'jadwal',
                'headString' => 'Kode',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'label_full',
                'alpine' => 'jadwal',
                'headString' => 'Label',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'hari_pelaksanaan',
                'alpine' => 'jadwal',
                'headString' => 'Hari',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'tanggal_pelaksanaan',
                'alpine' => 'jadwal',
                'headString' => 'Tanggal',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'kapasitas',
                'alpine' => 'jadwal',
            ])
        </x-slot:sortir>

  

        {{-- GRID UTAMA KARTU --}}
        @foreach ($jadwals as $index => $j)
            <template x-if="itemVisibilityMap[{{ $j->id }}]?.visible">
                {{-- 2. CARD ITEM CONTAINER --}}
                <div wire:key="kelas-jadwal-card-{{ $j->id }}"
                    :style="'order: ' + (itemVisibilityMap[{{ $j->id }}]?.order ?? 999)"
                    class="card-jadwal-item relative flex flex-col self-start p-3 rounded-xl border table-border bg-[var(--main-table-trans)] shadow-sm hover:shadow-md transition-all duration-300">

                    {{-- HEADER CARD (Kode Jadwal Wilayah & Tombol Aksi) --}}
                    <div
                        class="flex items-start justify-between gap-2 pb-3 border-b table-border/60">
                        <div class="flex flex-wrap items-center gap-2">
                            {{-- BADGE KODE BERDASARKAN WILAYAH --}}
                            <flux:dropdown>
                                <button class="cursor-pointer focus:outline-none">
                                    @include('livewire.global.table.badge.kode-wilayah-badge', [
                                        'xValue' => $j->kode,
                                        'sortir' => $j->kode_wilayah,
                                    ])
                                </button>

                                @include(
                                    'livewire.all-role.kelas-management.jadwal-management.jadwal-toolbar-table',
                                    [
                                        'x' => $j,
                                        'editString' => 'editJadwal',
                                        'nameXString' => 'Jadwal',
                                        'confirmDeleteString' => 'deleteJadwal',
                                    ]
                                )
                            </flux:dropdown>

                            {{-- BADGE ID JADWAL --}}
                            <x-label-card type="sm">
                                {{ $j->label_extra ?? '-' }}
                            </x-label-card>
                        </div>

                        {{-- TOMBOL AKSI ELLIPSIS (KANAN ATAS) --}}
                        <flux:dropdown>
                            <flux:button class="cursor-pointer" variant="ghost" size="sm"
                                icon="ellipsis-horizontal" inset="top bottom" />

                            @include(
                                'livewire.all-role.kelas-management.jadwal-management.jadwal-toolbar-table',
                                [
                                    'x' => $j,
                                    'editString' => 'editJadwal',
                                    'nameXString' => 'Jadwal',
                                    'confirmDeleteString' => 'deleteJadwal',
                                ]
                            )
                        </flux:dropdown>
                    </div>


                    {{-- BODY CARD (Detail Label, Password, dan Waktu) --}}
                    <div class="flex-1 py-2 flex flex-col justify-between gap-3">
                        {{-- INFORMASI UTAMA JADWAL --}}
                        <div
                            class="text-xs bg-[var(--second-table-color)]/30 p-2 rounded-lg border table-border/40">
                            <div class="space-y-1">
                                <p
                                    class="font-semibold text-sm text-[var(--contrast-main-text)] leading-snug tracking-tight">
                                    Tanggal Kelas
                                </p>
                                <p class="text-xs font-medium text-[var(--focus-color)] flex items-center gap-1.5">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ $j->tanggal_pelaksanaan ?? '-' }}
                                </p>
                            </div>
                            {{-- TOMBOL NAVIGASI JADWAL (KANAN BAWAH BODY) --}}
                            <div class="mt-2 pt-1 flex items-center justify-between gap-2">
                                <div class="text-xs">

                                    @if ($j->is_my_class)
                                        <code
                                            class="italic font-mono bg-[var(--second-table-color)] px-1.5 py-0.5 rounded border table-border text-[var(--contrast-main-text)]">
                                            Saya Terdaftar
                                        </code>
                                    @else
                                        <span
                                            class="text-[10px] text-[var(--contrast-second-text)] block">Password:</span>
                                        @if (Auth::user()->admin || Auth::user()->dosen)
                                            @if (!empty($j->password))
                                                <div class="mt-1">
                                                    <code
                                                        class="font-mono bg-[var(--second-table-color)] px-1.5 py-0.5 rounded border table-border text-[var(--contrast-main-text)]">
                                                        {{ $j->password }}
                                                    </code>
                                                </div>
                                            @else
                                                <span class="text-[10px] italic text-[var(--contrast-second-text)]">
                                                    Tanpa Password
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-[10px] italic text-[var(--contrast-second-text)]">
                                                @if (!empty($j->with_pw))
                                                    Memiliki Password
                                                @else
                                                    Tanpa Password
                                                @endif
                                            </span>
                                        @endif
                                    @endif


                                </div>

                                @if ($j->is_my_class || Auth::user()->admin || Auth::user()->dosen)
                                    <x-button-action color="amber"
                                        href="{{ $isJadwalMhs ?? null ? route('sesi-mahasiswa', [$j->kode_kelas, $j->kode_jadwal]) : route('sesi-management', [$j->kode_kelas, $j->kode_jadwal]) }}"
                                        wire:navigate>
                                        <flux:icon name="calendar-days" class="w-3.5 h-3.5" />
                                        <span>Lihat Kelas</span>
                                    </x-button-action>
                                @else
                                    @php
                                        $buttonClass =
                                            'inline-flex items-center justify-center gap-1.5 px-3 py-1 rounded-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/35 transition-all duration-200 text-sm font-medium shadow-sm cursor-pointer';
                                    @endphp
                                    @if (!empty($j->with_pw))
                                        <x-button-action color="blue"
                                            @click="
                                    $store.jadwal?.setEdit(0);
                                    $store.jadwal?.setColor('text-blue-700 dark:text-blue-400');
                                    $flux.modal('jadwal-join').show();
                                    $store.jadwal?.setValueJoinJadwal(
                                        '{{ $j->id ?? '' }}',
                                        '{{ $j->kode ?? '' }}',
                                        '{{ $j->kode_kelas ?? '' }}',
                                        '{{ $j->label_extra ?? '' }}',
                                    );
                                ">
                                            <flux:icon name="user-plus" class="w-3.5 h-3.5" />
                                            <span>Join</span>
                                        </x-button-action>
                                    @else
                                        <form x-on:submit.prevent="$wire.joinJadwal($store.jadwal)" id="jadwalForm">
                                            <x-button-action color="blue"
                                                @click="
                                        $store.jadwal?.setEdit(0);
                                        $store.jadwal?.setColor('text-blue-700 dark:text-blue-400');
                                        $store.jadwal?.setValueJoinJadwal(
                                            '{{ $j->id ?? '' }}',
                                        );
                                    ">
                                                <flux:icon name="user-plus" class="w-3.5 h-3.5" />
                                                <span>Join</span>
                                            </x-button-action>
                                        </form>
                                    @endif
                                @endif

                            </div>
                        </div>


                    </div>

                    {{-- FOOTER CARD (Kapasitas & Sinkronisasi Waktu Grid 3 Kolom) --}}
                    <div
                        class="grid grid-cols-5 gap-2 border-t table-border/40 bg-[var(--second-table-trans)] -mx-4 -mb-4 p-3 rounded-b-xl text-center text-xs">
                        <div class="col-span-2 border-r pl-2 table-border/60 space-y-0.5">
                            <span
                                class="text-left block text-[10px] uppercase font-semibold text-[var(--contrast-second-text)] tracking-wider">
                                Kapasitas</span>
                            <span class="text-left font-bold text-[var(--focus-color)] block truncate">
                                {{ $j->mahasiswas_count }} / {{ $j->kapasitas }}
                            </span>
                        </div>

                        <div class="col-span-3 truncate pl-0.5 pr-2 space-y-0.5">
                            <span
                                class="text-right block text-[10px] uppercase font-semibold text-[var(--contrast-second-text)] tracking-wider">Waktu
                                Pelaksanaan</span>
                            <span
                                class="text-right font-medium text-[var(--contrast-main-text)] block truncate">{{ $j->hari ?? '-' }},
                                {{ $j->jam_pelaksanaan ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </template>
        @endforeach

        {{-- EMPTY STATE ANCHOR --}}
        <div x-show="totalFilteredItems === 0"
            class="col-span-6 text-center p-12 rounded-xl border border-dashed table-border bg-[var(--main-table-trans)]">
            <p class="text-sm text-[var(--contrast-second-text)]">Tidak ada data Jadwal Pertemuan Kelas ditemukan!</p>
        </div>

        {{-- Slot Footer Pagination --}}
        <x-slot:footer>
            @include('livewire.global.table.pagination-alpine')
            @include('livewire.global.table.trash-delete')
        </x-slot:footer>

    </x-global.main-layout-card>
</div>
