{{-- Header Section --}}
<div class="mb-4 xl:mb-8">
    {{-- Container Utama --}}
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between mb-2 lg:mb-6 gap-4 min-w-0 w-full">

        {{-- Sisi Kiri: Profil Mahasiswa --}}
        <div class="mb-2 xl:mb-0 flex items-center gap-2 sm:gap-4 min-w-0 w-full">
            <a href="{{ route('nilai-management', ['switchTable' => 'rps']) }}" wire:navigate
                class="mx-1 px-2 py-2 sm:p-3 rounded-full hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors shrink-0 flex items-center justify-center">
                <flux:icon name="arrow-left" class="h-5 w-5 sm:h-6 sm:w-6 text-[var(--contrast-second-text)]" />
            </a>

            <div class="min-w-0 flex-1">
                <h2
                    class="mb-1 sm:mb-2 text-xl sm:text-2xl font-bold text-[var(--contrast-second-text)] flex flex-wrap items-center gap-4 min-w-0">
                    <span class="break-words"><span class="mr-2">Kode RPS: </span> {{ $rps->kode }}</span>
                    <span
                        class="text-[10px] sm:text-xs font-semibold px-2 sm:px-4 py-0.5 sm:py-1 rounded-md bg-[var(--focus-color)]/10 text-[var(--focus-color)] border border-[var(--focus-color)]/20 whitespace-nowrap">
                        {{ $rps->draf_text }}
                    </span>
                </h2>
                <p
                    class="text-[var(--contrast-main-text)] opacity-70 text-xs sm:text-sm flex items-center gap-x-1 gap-y-1 flex-wrap mt-0.5 min-w-0">
                    <span>{{ $rps->mk }}</span>
                    <strong class="opacity-40 mx-1">|</strong>
                    <span>Semester {{ $rps->semester }}</span>
                    <strong class="opacity-40 mx-1">|</strong>
                    <span>{{ $rps->sks }} SKS</span>
                    <strong class="opacity-40 mx-1">|</strong>
                    <span>{{ $rps->sks_text }}</span>
                </p>
            </div>
        </div>

        {{-- Ganti bagian div tombol Anda menjadi seperti ini --}}
        <div class="flex flex-wrap justify-end items-center gap-2.5 w-full">
            <flux:button
                @click="
            $store.rps?.resetShow();
            $store.rps?.setShowRPS(
                '{{ $rps->id ?? '' }}',
                '{{ $rps->kode ?? '' }}',
            );
            $store.rps?.setColor('text-green-700 dark:text-green-400');
            $flux.modal('rps-detail-modal').show();
            $dispatch('open-show-rps-modal', { id: {{ $rps->id }} });
        "
                icon="eye" size="sm"
                class="text-xs !cursor-pointer px-4 !text-cyan-600 dark:!text-cyan-400 !bg-cyan-50 hover:!bg-cyan-100 active:!bg-cyan-200 dark:!bg-cyan-950/20 dark:hover:!bg-cyan-900/30 dark:active:!bg-cyan-900 !border-cyan-200/60 dark:!border-cyan-800/40 transition-all duration-200 h-[34px] flex items-center justify-center">
                <span>Show RPS</span>
            </flux:button>

            <div class="shrink-0">
                @include('livewire.global.table.export-button', [
                    'nameXString' => 'Export RPS',
                    'xString' => "printPDFRPS($rps->id)",
                    'icon' => 'arrow-down-tray',
                    'isFull' => 1,
                    'valuePx' => 'px-4',
                    'valuePy' => 'py-4',
                    'isTextMd' => 0,
                    'isNoPb' => 1,
                    'color' => 'rose',
                ])
            </div>
        </div>

    </div>

</div>
