<div class="flex flex-wrap items-center gap-2 mb-4">
    {{-- Container Pembungkus Terluar: Mengikuti rule Kelas (pecah di md, pb-6 aktif di md ke atas) --}}
    {{-- <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:mb-6 w-full min-w-0">

        @if (!($withCapaian ?? null))
            @if ($typeXString == 'all')
                <h2 class="text-xl sm:text-2xl font-bold text-[var(--contrast-second-text)] min-w-0 break-words">
                    Manajemen Rencana Pembelajaran Semester
                </h2>
            @endif
        @else
            <div class="flex items-center gap-2 sm:gap-4 min-w-0 w-full">
                <a href="{{ $backUrl ?? route('nilai-management') }}" wire:navigate
                    class="mx-1 px-2 py-2 sm:p-3 rounded-full hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors shrink-0 flex items-center justify-center">
                    <flux:icon name="arrow-left" class="h-5 w-5 sm:h-6 sm:w-6 text-[var(--contrast-second-text)]" />
                </a>

                <div class="min-w-0 flex-1">
                    <h2
                        class="mb-1 sm:mb-2 text-xl sm:text-2xl font-bold text-[var(--contrast-second-text)] flex items-center gap-2 min-w-0">
                        <span class="break-words">{{ $textString }}</span>
                    </h2>
                    <p
                        class="text-[var(--contrast-main-text)] opacity-70 text-xs sm:text-sm flex items-center gap-x-1 gap-y-1 flex-wrap mt-0.5 min-w-0">
                        <span>{{ $textString2 }}</span>
                        <strong class="opacity-40 mx-1">|</strong>
                        <span>{{ $textString3 }}</span>
                    </p>
                </div>
            </div>

            <div
                class="flex flex-row-reverse items-center justify-start gap-3 w-full md:w-auto overflow-x-auto scrollbar-tiny flex-nowrap shrink-0 pb-1 pr-2 pl-2 sm:pl-0">
                <div class="shrink-0 mt-1">
                    @include('livewire.global.table.export-button', [
                        'xString' => 'exportRekapExcel()',
                        'autoSmall' => 'sm',
                    ])
                </div>

                <div class="shrink-0 mt-1">
                    @include('livewire.global.table.export-button', [
                        'nameXString' => "Rekap Capaian $kode_pr_url",
                        'xString' => "generateRekapCapaian($pr_id_url, 15)",
                        'color' => 'blue',
                        'icon' => 'academic-cap',
                    ])
                </div>

            </div>
        @endif

    </div> --}}
    @if ($typeXString == 'all')
        <h2 class="text-xl sm:text-2xl mr-4 font-bold mb-4 text-[var(--contrast-second-text)]">Manajemen Outcome-Based Education</h2>
    @endif
    @include('livewire.staff.obe-management.obe-toolbar-partial', ['typeXString' => $typeXString])
</div>
