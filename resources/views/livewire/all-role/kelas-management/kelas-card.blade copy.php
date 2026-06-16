<x-global.main-layout-card :paginator="$kelas">

    {{-- 1. Isi bagian Sortir --}}
    <x-slot:sortir>
        <div class="flex flex-col md:flex-row md:flex-wrap lg:items-center lg:justify-between gap-y-4 gap-x-6 w-full">

            <div x-data="{ activeTab: @entangle('filterKelasgg') }"
                class="scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto shrink-0">
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
                    'tabString' => 'mk-ganjil',
                    'tabNameString' => 'Ganjil',
                    'icon' => 'calendar-days',
                ])

                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'filterByKelasgg',
                    'xFilter' => 'filterKelasgg',
                    'tabFilter' => $totalGenap,
                    'tabString' => 'mk-genap',
                    'tabNameString' => 'Genap',
                    'icon' => 'calendar-days',
                ])
            </div>
            <div x-data="{ activeTab: @entangle('switchTable2') }" class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto lg:justify-end">
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

        </div>
    </x-slot:sortir>


    {{-- 2. Isi Utama (Looping Card) masuk ke Default Slot --}}
    @forelse($kelas as $k)
        <div wire:key="kelas-{{ $k->id }}" data-kelas-id="{{ $k->id }}"
            class="relative flex flex-col justify-between p-4 rounded-xl border table-border bg-[var(--main-table-trans)] shadow-sm hover:shadow-md transition-all duration-200">

            {{-- HEADER CARD (Kode Kelas, Kode RPS, & Tombol Aksi) --}}
            <div class="flex items-start justify-between gap-2 pb-3 border-b table-border/60">
                <div class="flex flex-wrap items-center gap-2">
                    {{-- 1. KODE KELAS --}}
                    <flux:dropdown>
                        <button class="cursor-pointer focus:outline-none">
                            @include('livewire.global.table.badge.level-mk-badge', [
                                'xValue' => $k->kode,
                                'sortir' => $k->rps_rel?->mk_rel?->level_mk,
                            ])
                        </button>
                        @include('livewire.all-role.kelas-management.kelas-toolbar-table', [
                            'x' => $k,
                            'editString' => 'editKelas',
                            'nameXString' => 'Kelas',
                            'confirmDeleteString' => 'deleteKelas',
                        ])
                    </flux:dropdown>

                    {{-- 2. KODE RPS --}}
                    <flux:dropdown>
                        <button class="cursor-pointer focus:outline-none">
                            @include('livewire.global.table.badge.semester-badge', [
                                'xValue' => $k->kode_rps,
                                'textString' => 'RPS:',
                                'sortir' => $k->semester,
                            ])
                        </button>
                        @include('livewire.all-role.kelas-management.kelas-toolbar-table', [
                            'x' => $k,
                            'editString' => 'editKelas',
                            'nameXString' => 'Kelas',
                            'confirmDeleteString' => 'deleteKelas',
                            'copyName' => 'Kode RPS',
                            'copyText' => $k->kode_rps ?? '',
                        ])
                    </flux:dropdown>
                </div>

                {{-- TOMBOL AKSI ELLIPSIS --}}
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom" />
                    @include('livewire.all-role.kelas-management.kelas-toolbar-table', [
                        'x' => $k,
                        'editString' => 'editKelas',
                        'nameXString' => 'Kelas',
                        'confirmDeleteString' => 'deleteKelas',
                    ])
                </flux:dropdown>
            </div>

            {{-- BODY CARD (Nama Mata Kuliah & Nama Kelas) --}}
            <div class="flex-1 pt-1 pb-3.5 flex flex-col">
                <div class="space-y-1">
                    <h3 class="font-semibold text-sm text-[var(--contrast-main-text)] leading-snug tracking-tight">
                        {{ $k->mk ?? '-' }}
                    </h3>

                    <p class="text-xs font-medium text-[var(--focus-color)] flex items-center gap-1.5">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-[var(--focus-color)]"></span>
                        {{ $k->kelas ?? '-' }}
                    </p>
                    <p class="text-xs font-medium text-[var(--focus-color)] flex items-center gap-1.5">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-[var(--focus-color)]"></span>
                        {{ $k->kode_mk ?? '-' }} - {{ $k->sks_text }}
                    </p>
                </div>

                <div class="mt-auto pt-3 flex justify-end">
                    <x-button-action color="emerald" href="{{ route('jadwal-management', $k->kode) }}" wire:navigate>
                        <flux:icon name="rectangle-group" class="w-3.5 h-3.5" />
                        <span>Lihat Kelas</span>
                    </x-button-action>
                </div>
            </div>
            {{-- FOOTER CARD (Semester, SKS, Program Studi) --}}
            <div
                class="grid grid-cols-3 gap-2 pt-3 border-t table-border/40 bg-[var(--second-table-trans)] -mx-4 -mb-4 p-3 rounded-b-xl text-center text-xs">
                <div class="border-r table-border/60 space-y-0.5">
                    <span
                        class="block text-[10px] uppercase font-semibold text-[var(--contrast-second-text)] tracking-wider">Semester</span>
                    <span class="font-bold text-[var(--contrast-main-text)]">{{ $k->semester ?? '-' }}</span>
                </div>

                <div class="border-r table-border/60 space-y-0.5">
                    <span
                        class="block text-[10px] uppercase font-semibold text-[var(--contrast-second-text)] tracking-wider">Bobot</span>
                    <span class="font-bold text-[var(--contrast-main-text)]">{{ $k->sks ?? '-' }} SKS</span>
                </div>

                <div class="truncate px-1 space-y-0.5">
                    <span
                        class="block text-[10px] uppercase font-semibold text-[var(--contrast-second-text)] tracking-wider">{{ $k->pr_rel->strata ?? '----' }}</span>
                    <span class="font-bold text-[var(--contrast-main-text)] truncate block">
                        {{ $k->kode_pr ?? '---' }}
                    </span>
                </div>
            </div>

        </div>
    @empty
        {{-- KEADAAN KOSONG --}}
        <div
            class="col-span-6 text-center p-12 rounded-xl border border-dashed table-border bg-[var(--main-table-trans)]">
            <flux:icon name="information-circle" class="mx-auto h-8 w-8 text-[var(--contrast-second-text)] mb-2" />
            <p class="text-sm text-[var(--contrast-second-text)]">Tidak ada data Kelas ditemukan!</p>
        </div>
    @endforelse

</x-global.main-layout-card>
