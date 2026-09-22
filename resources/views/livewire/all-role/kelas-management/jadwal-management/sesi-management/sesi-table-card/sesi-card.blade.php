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

            // 1. Ekstraksi Sufiks & Variabel Statis (Berdasarkan $isUjian saja)
            $suf = $isUjian ? '-special' : '';

            $borderTable = "border-[var(--border-table-color{$suf})]";
            $mainText = "text-[var(--contrast-main-text{$suf})]";
            $secondText = "text-[var(--contrast-second-text{$suf})]";
            $thirdText = "text-[var(--contrast-third-text{$suf})]";

            $focusColor = "bg-[var(--focus-color{$suf})]";
            $mainTable = "bg-[var(--main-table-color{$suf})]";
            $secondTable = "bg-[var(--second-table-color{$suf})]";
            $subTable = "bg-[var(--sub-table-color{$suf})]";

            // 2. Base String untuk Penggabungan $focusButton
            $btnBase = 'transition-all duration-200 hover:z-10 active:z-10';

            if ($isUjian) {
                if ($isPastDate) {
                    $focusDiv =
                        'dark:border-[var(--border-table-color-special)]/42 border-[var(--border-table-color-special)]';
                    $focusButton =
                        $btnBase .
                        ' hover:bg-[var(--focus-color-special)]/80 hover:text-[var(--main-text-special)] active:bg-[var(--focus-color-special)]/80 active:text-[var(--main-text-special)] dark:hover:bg-[var(--focus-color-special)]/24 dark:active:bg-[var(--focus-color-special)]/24 text-[var(--focus-color-special)] ring-[var(--focus-color-special)]/64';
                    $mainColor = 'dark:bg-[var(--main-color-special)]/24 bg-[var(--main-color-special)]/72';
                } else {
                    $focusDiv =
                        'ring-1 ring-[var(--focus-color-special)] border-[var(--border-table-color-special)] bg-[var(--main-table-trans-spceial)]/64';
                    $focusButton =
                        $btnBase .
                        ' hover:bg-[var(--focus-color-special)] hover:text-[var(--main-text-special)] active:bg-[var(--focus-color-special)] active:text-[var(--main-text-special)] text-[var(--focus-color-special)] ring-[var(--focus-color-special)]';
                    $mainColor = 'bg-[var(--main-color-special)]';
                }
            } else {
                if ($isPastDate) {
                    $focusDiv = 'dark:border-[var(--border-table-color)]/42 border-[var(--border-table-color)]';
                    $focusButton =
                        $btnBase .
                        ' hover:bg-[var(--focus-color)]/84 hover:text-[var(--main-text)] active:bg-[var(--focus-color)]/84 active:text-[var(--main-text)] dark:hover:bg-[var(--focus-color)]/24 dark:active:bg-[var(--focus-color)]/24 text-[var(--focus-color)] ring-[var(--focus-color)]/64';
                    $mainColor = 'dark:bg-[var(--main-color)]/24 bg-[var(--main-color)]/72';
                } else {
                    $focusDiv = 'border-[var(--border-table-color)] bg-[var(--main-table-trans)]/64';
                    $focusButton =
                        $btnBase .
                        ' hover:bg-[var(--focus-color)] hover:text-[var(--main-text)] active:bg-[var(--focus-color)] active:text-[var(--main-text)] text-[var(--focus-color)] ring-[var(--focus-color)]';
                    $mainColor = 'bg-[var(--main-color)]';
                }
            }
        @endphp


        <div x-show="filteredAndSortedIds.slice((currentPage - 1) * perPage, currentPage * perPage).some(item => Number(item.id) === Number({{ $s->id }}))"
            class="{{ $isUjian ? 'lg:col-span-2' : '' }} contents">

            <div :style="'order: ' + filteredAndSortedIds.findIndex(entry => Number(entry.id) === Number(
                {{ $s->id }}))"
                wire:key="kelas-sesi-card-{{ $s->id }}" x-data="{ expanded: false, hasLoaded: false }"
                @click="expanded = !expanded; hasLoaded = true"
                class="{{ $focusDiv }} {{ $isUjian ? 'lg:col-span-2' : '' }} flex flex-col h-auto flex-shrink-0 rounded-[20px] overflow-hidden border transition-all duration-200 hover:shadow-lg active:shadow-lg cursor-pointer">



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

</x-global.main-layout-card>
