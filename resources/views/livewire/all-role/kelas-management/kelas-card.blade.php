<x-global.main-layout-card :paginator="$kelas">

    {{-- 1. Isi bagian Sortir --}}
    <x-slot:sortir>
        <div x-data="{ activeTab: @entangle('filterKelasgg') }"
                class="w-full pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto shrink-0">
                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'filterByKelasgg',
                    'xFilter' => 'filterKelasgg',
                    'tabFilter' => $totalGanjil + $totalGenap,
                    'tabString' => '',
                    'tabNameString' => 'Semua',
                    'icon' => 'table-cells',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'filterByKelasgg',
                    'xFilter' => 'filterKelasgg',
                    'tabFilter' => $totalGanjil,
                    'tabString' => 'kelas-ganjil',
                    'tabNameString' => 'Ganjil',
                    'icon' => 'calendar-days',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'filterByKelasgg',
                    'xFilter' => 'filterKelasgg',
                    'tabFilter' => $totalGenap,
                    'tabString' => 'kelas-genap',
                    'tabNameString' => 'Genap',
                    'icon' => 'calendar-days',
                ])
        </div>
    </x-slot:sortir>

    <x-slot:search>
        <div x-data="{ activeTab: @entangle('switchTable2') }" class="w-full pb-1 flex flex-wrap items-center gap-2.5 w-full lg:w-auto lg:justify-end">
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'kode',
                    'headString' => 'Kode Kelas',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'kode_rps',
                    'headString' => 'Kode RPS',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'kelas',
                    'headString' => 'Nama Kelas',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'mk',
                    'headString' => 'Mata Kuliah',
                ])
                @include('livewire.global.table.head-sortir', [
                    'sortFieldString' => 'semester',
                ])
                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'switchingTable2',
                    'xFilter' => 'switchTable2',
                    'tabFilter' => $totalGanjil + $totalGenap,
                    'tabString' => 'kelas-table',
                    'tabNameString' => 'Tabel Kelas',
                    'icon' => 'table-cells',
                ])
        </div>
    </x-slot:search>



    {{-- 2. Isi Utama (Looping Card) masuk ke Default Slot --}}
    @forelse($kelas as $k)
        <div wire:key="kelas-{{ $k->id }}" data-kelas-id="{{ $k->id }}"
            class="flex flex-col rounded-[20px] overflow-hidden border border-[var(--border-table-color)] bg-[var(--main-table-trans)]/50 transition-all duration-200 hover:shadow-lg active:shadow-lg">

            {{-- ═══ HERO ═══ --}}
            <div class="flex flex-col gap-3 p-[18px] bg-[var(--main-color)]">

                {{-- Baris atas: kode kelas + tombol menu --}}
                <div class="flex items-start justify-between gap-2">

                    {{-- Kode Kelas --}}

                    <div class="flex items-center gap-2">
                        <flux:dropdown>
                            <button
                                class="inline-flex items-center gap-1.5 rounded-lg border border-white/20 bg-white/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-white/75 transition-colors hover:bg-white/20 active:bg-white/50 focus:outline-none cursor-pointer">
                                <flux:icon name="academic-cap" class="w-3 h-3" />
                                {{ $k->kode }}
                            </button>
                            @include('livewire.all-role.kelas-management.kelas-toolbar-table', [
                                'x' => $k,
                                'editString' => 'editKelas',
                                'nameXString' => 'Kelas',
                                'confirmDeleteString' => 'deleteKelas',
                            ])
                        </flux:dropdown>
                        @if (Auth::user()->admin || Auth::user()->dosen)
                            <span class="text-xs text-white/60 font-mono">ID:
                                {{ $k->id }}</span>
                        @endif
                    </div>

                    {{-- Tombol Menu --}}
                    <flux:dropdown>
                        <button
                            class="flex h-[30px] w-[30px] flex-shrink-0 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-white/80 transition-colors hover:bg-white/20 active:bg-white/50 focus:outline-none cursor-pointer">
                            <flux:icon name="ellipsis-vertical" class="w-4 h-4" />
                        </button>
                        @include('livewire.all-role.kelas-management.kelas-toolbar-table', [
                            'x' => $k,
                            'editString' => 'editKelas',
                            'nameXString' => 'Kelas',
                            'confirmDeleteString' => 'deleteKelas',
                        ])
                    </flux:dropdown>
                </div>

                {{-- Nama Mata Kuliah --}}
                <p class="text-[15px] font-bold leading-[1.35] tracking-[-0.02em] text-[var(--main-text)]">
                    {{ $k->mk ?? '-' }}
                </p>

                {{-- Sub info: nama kelas + prodi --}}
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-[var(--main-text)]/65">
                        <flux:icon name="users" class="w-3 h-3" />
                        {{ $k->kelas ?? '-' }}
                    </span>
                    <span class="h-[3px] w-[3px] flex-shrink-0 rounded-full bg-[var(--main-text)]/30"></span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-[var(--main-text)]/65">
                        <flux:icon name="academic-cap" class="w-3 h-3" />
                        {{ $k->kode_pr ?? '-' }}
                    </span>
                </div>
            </div>

            {{-- ═══ BODY ═══ --}}
            <div class="flex flex-1 flex-col gap-2.5 p-4">

                {{-- Baris RPS --}}
                <flux:dropdown>
                    <div
                        class="flex w-full items-center gap-1.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] pl-4 pr-2.5 py-2 text-left transition-colors focus:outline-none cursor-pointer">
                        <flux:icon name="document-text" class="w-3.5 h-3.5 text-[var(--contrast-third-text)]" />
                        <span
                            class="text-[10px] font-bold uppercase tracking-[0.06em] text-[var(--contrast-third-text)]">RPS</span>
                        <span class="ml-auto text-xs font-semibold text-[var(--contrast-main-text)]">
                            <button class="cursor-pointer focus:outline-none">
                                @include('livewire.global.table.badge.level-mk-badge', [
                                    'xValue' => $k->kode_rps,
                                    'sortir' => $k->rps_rel?->mk_rel?->level_mk,
                                ])
                            </button>
                        </span>
                    </div>
                    @include('livewire.all-role.kelas-management.kelas-toolbar-table', [
                        'x' => $k,
                        'editString' => 'editKelas',
                        'nameXString' => 'Kelas',
                        'confirmDeleteString' => 'deleteKelas',
                        'copyName' => 'Kode RPS',
                        'copyText' => $k->kode_rps ?? '',
                    ])
                </flux:dropdown>

                {{-- Stat boxes --}}
                <div class="grid grid-cols-3 gap-1.5">

                    {{-- Semester --}}
                    <div
                        class="flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                        <span
                            class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Semester</span>
                        <span
                            class="text-base font-bold leading-none text-[var(--contrast-main-text)]">{{ $k->semester ?? '-' }}</span>
                    </div>

                    {{-- SKS --}}
                    <div
                        class="flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                        <span
                            class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Bobot</span>
                        <span
                            class="text-base font-bold leading-none text-[var(--contrast-main-text)]">{{ $k->sks ?? '-' }}</span>
                        <span class="text-[9px] font-semibold text-[var(--contrast-second-text)]">SKS</span>
                    </div>

                    {{-- Strata + Prodi --}}
                    <div
                        class="flex flex-col items-center justify-center gap-1 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                        <span
                            class="bg-[var(--focus-color)] rounded px-1.5 py-[2px] text-[9px] font-extrabold uppercase tracking-[0.08em] text-[var(--main-text)]">
                            {{ $k->pr_rel->strata ?? '---' }}
                        </span>
                        <span
                            class="text-xs font-bold text-[var(--contrast-main-text)]">{{ $k->kode_pr ?? '---' }}</span>
                    </div>
                </div>
            </div>

            {{-- ═══ FOOTER ═══ --}}
            <div class="px-4 pb-4 flex items-center gap-1.5">
                <button
                    class="cursor-pointer flex w-full items-center justify-center gap-1.5 rounded-bl-[11px] rounded-r-[4px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] btn-card-focus-state transition-all active:scale-[0.99]"
                    @click="
                        $store.kelas?.resetShow();
                        $store.kelas?.setShowRPS(
                            '{{ $k->rps_id ?? '' }}',
                        );
                        $flux.modal('rps-detail-modal').show();
                    "
                    wire:click="showRPS({{ $k->rps_id }})">
                    <flux:icon wire:loading.remove wire:target="showRPS({{ $k->rps_id }})"
                        name="clipboard-document-list" class="w-3.5 h-3.5" />
                    <span wire:loading.remove wire:target="showRPS({{ $k->rps_id }})">Lihat RPS</span>
                    <flux:icon wire:loading wire:target="showRPS({{ $k->rps_id }})" name="arrow-path"
                        class="animate-spin h-4 w-4 ml-2" />
                </button>

                <button
                    class="flex w-full items-center justify-center gap-1.5 rounded-br-[11px] rounded-l-[4px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] transition-all
                        {{ $k->trashed()
                            ? 'cursor-not-allowed bg-gray-100 dark:bg-zinc-800/50 text-gray-400 dark:text-zinc-500 ring-1 ring-gray-200 dark:ring-zinc-800'
                            : 'cursor-pointer bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] btn-card-focus-state active:scale-[0.99]' }}"
                    {{ $k->trashed() ? 'disabled' : 'href=' . route('jadwal-management', $k->kode) . ' wire:navigate' }}
                    href="{{ route('jadwal-management', $k->kode) }}" wire:navigate>
                    <flux:icon name="rectangle-group" class="w-3.5 h-3.5" />
                    <span>Lihat Kelas</span>
                </button>
            </div>

        </div>
    @empty
        {{-- KEADAAN KOSONG --}}
        <div
            class="col-span-6 text-center p-12 rounded-xl border border-dashed table-border bg-[var(--main-table-trans)]">
            <flux:icon name="information-circle" class="mx-auto h-8 w-8 text-[var(--contrast-second-text)] mb-2" />
            <p class="text-xs sm:text-sm text-[var(--contrast-second-text)]">Tidak ada data Kelas ditemukan!</p>
        </div>
    @endforelse

</x-global.main-layout-card>
