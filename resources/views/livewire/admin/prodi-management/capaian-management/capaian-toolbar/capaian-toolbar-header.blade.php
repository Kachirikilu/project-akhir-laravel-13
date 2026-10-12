<div class="gap-2 mb-5">
    {{-- Container Pembungkus Terluar: Mengikuti rule Kelas (pecah di md, pb-6 aktif di md ke atas) --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 w-full min-w-0">

        <div class="flex items-center gap-2 sm:gap-4 min-w-0 w-full">
            @if (!$noBackUrl)
                <a href="{{ $backUrl ?? route('nilai-capaian') }}" wire:navigate
                    class="mx-1 px-2 py-2 sm:p-3 rounded-full hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors shrink-0 flex items-center justify-center">
                    <flux:icon name="arrow-left" class="h-5 w-5 sm:h-6 sm:w-6 text-[var(--contrast-second-text)]" />
                </a>
            @endif

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

    </div>

    @include('livewire.admin.prodi-management.capaian-management.capaian-view.capaian-header')


</div>
<div class="flex flex-wrap items-center justify-between gap-4 mb-4">
    <div class="flex items-center gap-3 shrink-0">
        <div class="shrink-0">
            @include('livewire.global.table.export-button', [
                'xString' => 'exportRekapExcel()',
                'autoSmall' => 'sm',
                'isNoPb' => 1,
            ])
        </div>

        @php
            $runningAllKey = 'rekap_capaian_running_all';
            $runningProdiKey = 'rekap_capaian_running_prodi_ids';

            $isProcessAll = Cache::has($runningAllKey);

            $runningProdiIds = Cache::get($runningProdiKey, []);

            $currentProdiId = (int) $pr_id_url;
            $isCurrentProdiRunning = in_array($currentProdiId, array_map('intval', $runningProdiIds));

            $isProcessProdi = $isProcessAll || $isCurrentProdiRunning;
            $isAnyJobRunning = $isProcessAll || !empty($runningProdiIds);
        @endphp

        <div class="flex items-center gap-2" @if ($isAnyJobRunning) wire:poll.12s @endif>
            <div class="shrink-0">
                @include('livewire.global.table.export-button', [
                    'nameXString' => $isProcessProdi
                        ? "Process Capaian $kode_pr_url"
                        : "Rekap Capaian $kode_pr_url",
                    'isLoading' => $isProcessProdi,
                    'xString' => "rekapCapaian($pr_id_url, 15)",
                    'color' => 'blue',
                    'icon' => 'academic-cap',
                    'isNoPb' => 1,
                ])
            </div>
            @if (Auth::user()->admin || Auth::user()->dosen->tingkat_user == 1)
                <div class="shrink-0">
                    @include('livewire.global.table.export-button', [
                        'nameXString' => $isProcessAll
                            ? 'Process Capaian ' . (env('UNIV') ?? 'UNSRI')
                            : 'Rekap Capaian ' . (env('UNIV') ?? 'UNSRI'),
                        'isLoading' => $isProcessAll,
                        'xString' => 'rekapCapaianAll()',
                        'color' => 'yellow',
                        'icon' => 'academic-cap',
                        'autoSmall' => 'md',
                        'isNoPb' => 1,
                    ])
                </div>
            @endif
        </div>
    </div>

    <div class="flex items-center shrink-0 ml-auto">
        @include(
            'livewire.staff.obe-management.obe-toolbar.obe-toolbar-header.obe-toolbar-header-partial',
            ['typeXString' => $typeXString]
        )
    </div>
</div>
