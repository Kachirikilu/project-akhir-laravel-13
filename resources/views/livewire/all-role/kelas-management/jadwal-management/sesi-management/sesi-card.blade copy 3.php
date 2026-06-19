<div x-data="{
    get currentPage() { return this.$store.sesi.currentPage; },
    set currentPage(val) { this.$store.sesi.currentPage = val; },

    get perPage() { return this.$store.sesi.perPage || 8; },

    get totalFilteredItems() {
        if (!window.Alpine) return 0;
        return Array.from(document.querySelectorAll('.card-sesi-item')).filter(el => {
            return el.style.display !== 'none';
        }).length;
    },

    sortCards() {
        let field = this.$store.sesi.sortField;
        let direction = this.$store.sesi.sortDirection === 'desc' ? -1 : 1;

        if (!field) return;

        let container = document.querySelector('.card-sesi-item')?.parentElement;
        if (!container) return;

        let cards = Array.from(container.querySelectorAll('.card-sesi-item'));

        cards.sort((a, b) => {
            let valA = a.getAttribute(`data-${field}`) || '';
            let valB = b.getAttribute(`data-${field}`) || '';

            if (!isNaN(valA) && !isNaN(valB) && valA !== '' && valB !== '') {
                return (Number(valA) - Number(valB)) * direction;
            }
            return valA.localeCompare(valB, undefined, { numeric: true, sensitivity: 'base' }) * direction;
        });

        cards.forEach(card => container.appendChild(card));
    },

    updatePagination() {
        this.$nextTick(() => {
            let computedPages = Math.ceil(this.totalFilteredItems / this.perPage) || 1;
            this.$store.sesi.totalPages = computedPages;
        });
    },

    init() {
        this.$watch('$store.sesi.sortField', () => {
            this.sortCards();
            this.updatePagination();
        });
        this.$watch('$store.sesi.sortDirection', () => {
            this.sortCards();
            this.updatePagination();
        });

        this.$watch('$store.sesi.search', () => {
            this.currentPage = 1;
            this.updatePagination();
        });

        this.$watch('$store.sesi.perPage', () => {
            this.currentPage = 1;
            this.updatePagination();
        });

        setTimeout(() => {
            this.sortCards();
            this.updatePagination();
        }, 50);
    }
}" class="w-full">
    <x-global.main-layout-card :paginator="$sesis">

        {{-- Isi bagian Sortir --}}
        <x-slot:sortir>
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'pertemuan_ke',
                'isAlpineSortir' => 1,
                'alpine' => 'sesi',
                'headString' => 'Pertemuan',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'total_absensi',
                'isAlpineSortir' => 1,
                'alpine' => 'sesi',
                'headString' => 'Absensi',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'tanggal_pelaksanaan',
                'isAlpineSortir' => 1,
                'alpine' => 'sesi',
                'headString' => 'Tanggal',
            ])
            @include('livewire.global.table.head-sortir', [
                'sortFieldString' => 'metode',
                'isAlpineSortir' => 1,
                'alpine' => 'sesi',
            ])
        </x-slot:sortir>

        {{-- Isi bagian Search --}}
        <x-slot:search>
            <div class="w-full md:w-96 xl:w-108">
                <div class="col-start-1 row-start-1 w-full">
                    <div class="relative w-full max-w-md">
                        <input x-model="$store.sesi.search" type="search"
                            placeholder="Cari pertemuan, metode, tugas, atau kode Sub-CPMK..."
                            class="w-full px-3 py-2 rounded-lg border bg-transparent" />
                    </div>
                </div>
            </div>
        </x-slot:search>

        {{-- 2. GRID UTAMA KARTU SESI --}}
        @forelse($sesis as $index => $s)
            @php
                $daftarUjian = array_merge(config('app.uts_fields'), config('app.uas_fields'));

                $isUjian = in_array(strtoupper($s->metode), $daftarUjian);

                $kehadiran_mhs = null;

                if (Auth::user()->mahasiswa) {
                    $kehadiran_mhs = $s->kehadirans->where('mahasiswa_id', Auth::user()->mahasiswa->id)->first();
                }

                // ==========================
                // RAW STRING
                // ==========================
                $stringMetode = $s->metode ?? '';
                $stringTugas = $s->tugas ?? 'Tidak ada deskripsi tugas spesifik untuk sesi ini.';
                $stringKodeScpmk = $s->kode_scpmk ?? '';
                $stringPertemuan = implode(' ', [
                    $s->pertemuan_ke,
                    'p-' . $s->pertemuan_ke,
                    'p' . $s->pertemuan_ke,
                    'pertemuan ' . $s->pertemuan_ke,
                    'per ' . $s->pertemuan_ke,
                ]);

                // ==========================
                // PRECOMPUTED SEARCH
                // ==========================
                $searchMetode = strtolower($stringMetode);
                $searchTugas = strtolower($stringTugas);
                $searchPertemuan = strtolower($stringPertemuan);
                $searchKodeSCPMK = preg_replace('/[^A-Za-z0-9]/', '', strtolower($stringKodeScpmk));
            @endphp

            {{-- CARD ITEM --}}
            <div wire:key="kelas-{{ $s->id }}" x-data="{
                expanded: {{ $isUjian ? 'true' : 'false' }},
                dbIndex: {{ $index }},
                searchMetode: '{{ addslashes($searchMetode) }}',
                searchTugas: '{{ addslashes($searchTugas) }}',
                searchPertemuan: '{{ addslashes($searchPertemuan) }}',
                searchCode: '{{ addslashes($searchKodeSCPMK) }}',
            
                get matchSearchPassed() {
                    let query = ($store.sesi?.search || '').toLowerCase().trim();
            
                    if (!query) return true;
            
                    let cleanQuery = query.replace(/[^a-z0-9]/g, '');
            
                    return (
                        this.searchMetode.includes(query) ||
                        this.searchTugas.includes(query) ||
                        this.searchPertemuan.includes(query) ||
                        (cleanQuery && this.searchCode.includes(cleanQuery))
                    );
                },
            
                get localVisibleIndex() {
                    if (!this.matchSearchPassed) return -1;
                    let query = ($store.sesi?.search || '').trim();
                    if (!query) return this.dbIndex;
                    let count = 0;
                    let sibling = this.$el.previousElementSibling;
            
                    while (sibling) {
                        if (sibling.classList.contains('card-sesi-item')) {
                            if (sibling.__x_dataStack || sibling._x_dataStack) {
                                try {
                                    if (Alpine.evaluate(sibling, 'matchSearchPassed')) count++;
                                } catch (e) {}
                            } else {
                                count++;
                            }
                        }
                        sibling = sibling.previousElementSibling;
                    }
                    return count;
                }
            }"
                x-show="matchSearchPassed && (localVisibleIndex >= ($store.sesi.currentPage - 1) * $store.sesi.perPage && localVisibleIndex < $store.sesi.currentPage * $store.sesi.perPage)"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" @click="expanded = !expanded"
                data-pertemuan_ke="{{ $s->pertemuan_ke }}" data-total_absensi="{{ $s->total_absensi ?? 0 }}"
                data-tanggal_pelaksanaan="{{ $s->tanggal_pelaksanaan }}"
                data-metode="{{ $s->metode }}"
                class="card-sesi-item {{ $isUjian ? 'lg:col-span-2 ring-1 ring-amber-500/30 bg-gradient-to-r from-[var(--main-table-trans)] to-amber-500/5' : 'cursor-pointer select-none' }} relative flex flex-col self-start p-3 rounded-xl border table-border bg-[var(--main-table-trans)] shadow-sm hover:shadow-md transition-all duration-300">

                {{-- CARD HEADER --}}
                <div class="flex items-start justify-between gap-4 pb-3 border-b table-border"
                    @click.stop>
                    <div class="flex items-center gap-3">
                        <x-label-card type="sm">P-{{ $s->pertemuan_ke }}</x-label-card>
                        <div class="flex items-center gap-3">
                            <div>
                                <flux:dropdown>
                                    <button class="cursor-pointer focus:outline-none">
                                        @include('livewire.global.table.badge.metode-badge', [
                                            'xValue' => $s->metode,
                                        ])
                                    </button>
                                    @include(
                                        'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                                        [
                                            'x' => $s,
                                            'editString' => 'editSesi',
                                            'nameXString' => 'Sesi',
                                            'confirmDeleteString' => 'deleteSesi',
                                            'copyName' => 'Kode Sub-CPMK',
                                            'copyText' => $s->kode_scpmk ?? '',
                                        ]
                                    )
                                </flux:dropdown>
                            </div>
                            <div class="flex items-center gap-3">
                                @if ($isUjian)
                                    <span
                                        class="text-xs font-semibold uppercase tracking-wider text-amber-500 animate-pulse">Sesi
                                        Evaluasi Utama</span>
                                @endif
                                <span class="text-xs text-[var(--contrast-second-text)] font-mono">ID:
                                    {{ $s->id }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <flux:dropdown>
                            <flux:button class="cursor-pointer" variant="ghost" size="sm"
                                icon="ellipsis-horizontal" inset="top bottom" />
                            @include(
                                'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                                [
                                    'x' => $s,
                                    'editString' => 'editSesi',
                                    'nameXString' => 'Sesi',
                                    'confirmDeleteString' => 'deleteSesi',
                                    'copyName' => 'Kode Sub-CPMK',
                                    'copyText' => $s->kode_scpmk ?? '',
                                ]
                            )
                        </flux:dropdown>
                    </div>
                </div>

                {{-- CARD BODY --}}
                <div class="flex flex-col flex-1 justify-between mt-3 text-sm">
                    <div
                        class="p-3 rounded-lg bg-[var(--second-table-trans)] border table-border/30 space-y-3">
                        <span
                            class="text-xs font-bold uppercase tracking-wide text-[var(--focus-color)] block">Informasi
                            Sesi</span>
                        <div class="flex flex-col gap-2 text-xs text-[var(--contrast-main-text)]">
                            <div
                                class="flex items-center justify-between rounded-md bg-[var(--main-table-color)]/40 px-3 py-2">
                                <span class="text-[var(--contrast-second-text)]">Waktu</span>
                                <span class="font-medium text-right">{{ $s->hari }},
                                    {{ $s->jam_pelaksanaan }}</span>
                            </div>
                            <div
                                class="flex items-center justify-between rounded-md bg-[var(--main-table-color)]/40 px-3 py-2">
                                <span class="text-[var(--contrast-second-text)]">Tanggal</span>
                                <span class="font-medium text-right">{{ $s->tanggal_pelaksanaan }}</span>
                            </div>
                            <div
                                class="flex items-center justify-between rounded-md bg-[var(--main-table-color)]/40 px-3 py-2">
                                <span class="text-[var(--contrast-second-text)]">Absensi</span>
                                <span
                                    class="font-medium text-[var(--focus-color)] text-right">{{ $s->mhs_absensi ?? 0 }}
                                    / {{ $s->count_mahasiswa }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- INTERACTIVE DROPDOWN AREA --}}
                    <div x-show="expanded" x-collapse class="mt-2 transition-all duration-300">
                        <div class="grid grid-cols-1 {{ $isUjian ? 'sm:grid-cols-3' : '' }} gap-2">
                            <div @click.stop
                                class="{{ $isUjian ? 'sm:col-span-2' : 'sm:col-span-1' }} p-3 rounded-xl bg-[var(--sub-table-trans)] border table-border/30 space-y-3">
                                <div class="flex items-center justify-between gap-2">
                                    <span
                                        class="text-xs font-bold uppercase tracking-wide text-[var(--focus-color)]">Sub-CPMK
                                        & Bobot</span>
                                    <span
                                        class="text-xs px-2 py-1 rounded-lg font-semibold bg-[var(--main-table-color)] border table-border">Bobot:
                                        {{ $s->bobot_normalisasi ? $s->bobot_normalisasi . '%' : '-' }}</span>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between gap-2 flex-wrap pb-1">
                                        <flux:dropdown>
                                            <button class="cursor-pointer focus:outline-none group">
                                                <flux:badge icon="academic-cap" color="fuchsia" size="sm"
                                                    class="group-hover:opacity-80 transition">
                                                    {{ $s->kode_scpmk ?? '---' }}</flux:badge>
                                            </button>
                                            @include(
                                                'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                                                [
                                                    'x' => $s,
                                                    'editString' => 'editSesi',
                                                    'nameXString' => 'Sesi',
                                                    'confirmDeleteString' => 'deleteSesi',
                                                    'copyName' => 'Kode Sub-CPMK',
                                                    'copyText' => $s->kode_scpmk ?? '',
                                                ]
                                            )
                                        </flux:dropdown>

                                        @if (Auth::user()->mahasiswa)
                                            <div x-data="{
                                                sekarang: '',
                                                mulai: '{{ $s->waktu_pelaksanaan }}',
                                                dispensasi: '{{ $s->waktu_dispensasi }}',
                                                getWaktuLokal() {
                                                    let d = new Date();
                                                    return new Date(d.getTime() - (d.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
                                                },
                                                init() {
                                                    this.sekarang = this.getWaktuLokal();
                                                    setInterval(() => { this.sekarang = this.getWaktuLokal(); }, 10000);
                                                }
                                            }">
                                                <template x-if="sekarang >= mulai && sekarang <= dispensasi">
                                                    <x-button-action color="blue" size="sm"
                                                        @click="$store.sesi?.setValueAbsenSesi('{{ $s->id }}','{{ $s->pertemuan_ke }}','{{ $s->waktu_pelaksanaan }}','{{ $s->waktu_berakhir }}','{{ $s->waktu_telat }}','{{ $s->waktu_dispensasi }}'); $flux.modal('sesi-absen').show();">
                                                        <flux:icon name="user-plus" class="w-3.5 h-3.5" />
                                                        <span>Absensi</span>
                                                    </x-button-action>
                                                </template>
                                            </div>
                                        @endif
                                    </div>

                                    @if (Auth::user()->mahasiswa)
                                        <div
                                            class="pt-3 border-t border-zinc-200 dark:border-zinc-700 flex flex-col gap-2 text-sm">
                                            <span class="text-zinc-500 dark:text-zinc-400 font-medium">Status Absen
                                                Anda:</span>
                                            <div class="w-full">
                                                @if ($kehadiran_mhs)
                                                    <div class="flex flex-col items-start gap-1.5">
                                                        <div
                                                            class="flex items-center justify-between gap-2 flex-wrap pb-1 w-full">
                                                            <flux:badge color="green" size="sm">
                                                                {{ $kehadiran_mhs->status }}</flux:badge>
                                                            <span class="text-xs text-zinc-400 dark:text-zinc-500">
                                                                <flux:icon name="clock" class="w-3.5 h-3.5 inline" />
                                                                {{ $kehadiran_mhs->waktu_presensi?->format('H:i') }}
                                                                WIB
                                                            </span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <flux:badge color="zinc" size="sm" class="opacity-70">Belum
                                                        Presensi</flux:badge>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div
                                class="sm:col-span-1 p-3 rounded-xl bg-[var(--second-table-trans)] border table-border/30">
                                <span
                                    class="text-xs font-bold uppercase tracking-wide text-[var(--contrast-second-text)] block mb-2">Deskripsi
                                    Tugas</span>
                                <p class="text-xs leading-relaxed text-[var(--contrast-main-text)]">
                                    {{ $s->tugas ?? 'Tidak ada deskripsi.' }}</p>
                            </div>
                        </div>
                    </div>

                    @if (!$isUjian)
                        <div
                            class="flex justify-center mt-2 pt-1.5 border-t border-dashed table-border/20">
                            <div
                                class="flex items-center gap-1 text-[10px] font-medium text-[var(--contrast-second-text)]">
                                <span x-text="expanded ? 'Klik untuk merapatkan' : 'Klik kartu untuk detail'"></span>
                                <flux:icon name="chevron-down" class="w-3 h-3" ::class="{ 'rotate-180': expanded }" />
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div
                class="col-span-6 text-center p-12 rounded-xl border border-dashed table-border bg-[var(--main-table-trans)]">
                <p class="text-xs sm:text-sm text-[var(--contrast-second-text)]">Tidak ada data Sesi Pertemuan Kelas ditemukan!</p>
            </div>
        @endforelse

        <x-slot:footer>
            <div x-show="totalFilteredItems > perPage"
                class="flex items-center justify-between pt-4 border-t table-border mt-4">
                <div class="text-xs text-[var(--contrast-second-text)]">
                    Menampilkan <span class="font-semibold text-[var(--contrast-main-text)]"
                        x-text="Math.min((currentPage - 1) * perPage + 1, totalFilteredItems)"></span>
                    sampai <span class="font-semibold text-[var(--contrast-main-text)]"
                        x-text="Math.min($store.sesi.currentPage * ($store.sesi.perPage || 8), Array.from(document.querySelectorAll('.card-sesi-item')).filter(el => !el.style.display || el.style.display !== 'none').length)"></span>
                    dari <span class="font-semibold text-[var(--contrast-main-text)]"
                        x-text="totalFilteredItems"></span> data sesi
                </div>

                <div class="flex items-center gap-1">
                    <button type="button"
                        @click="if($store.sesi.currentPage > 1) { $store.sesi.currentPage--; window.scrollTo({top: 0, behavior: 'smooth'}); }"
                        :disabled="$store.sesi.currentPage === 1"
                        class="px-3 py-1 text-xs font-medium rounded-lg border table-border bg-[var(--main-table-trans)] disabled:opacity-40 cursor-pointer disabled:cursor-not-allowed">
                        Sebelumnya
                    </button>

                    <div class="flex items-center gap-1 mx-2 text-xs text-[var(--contrast-second-text)]">
                        Halaman
                        <span class="font-semibold text-[var(--contrast-main-text)]"
                            x-text="$store.sesi.currentPage"></span>
                        dari
                        <span class="font-semibold text-[var(--contrast-main-text)]"
                            x-text="$store.sesi.totalPages"></span>
                    </div>

                    <button type="button"
                        @click="if($store.sesi.currentPage < $store.sesi.totalPages) { $store.sesi.currentPage++; window.scrollTo({top: 0, behavior: 'smooth'}); }"
                        :disabled="$store.sesi.currentPage === $store.sesi.totalPages"
                        class="px-3 py-1 text-xs font-medium rounded-lg border table-border bg-[var(--main-table-trans)] disabled:opacity-40 cursor-pointer disabled:cursor-not-allowed">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </x-slot:footer>

    </x-global.main-layout-card>
</div>
