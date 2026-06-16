<div
    class="px-4 py-6 mt-4 bg-[var(--main-table-color)] table-border shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <div
        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--contrast-second-text)] pb-4">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-lg">
                <flux:icon icon="academic-cap" variant="mini" class="w-5 h-5" />
            </div>
            <div>
                <h3 class="text-base font-bold text-[var(--contrast-main-text)] tracking-wide"
                    x-text="$store.cpl?.kode ?? 'S1-TKE-CPL1211'"></h3>
            </div>
        </div>
    </div>
    @if ($withCapaian ?? null)
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--contrast-second-text)] mb-3">
                Akumulasi Rekap Hasil Capaian Lulusan Semester
            </label>

            <div class="space-y-3">

                <div class="grid grid-cols-3 gap-3">
                    <div
                        class="p-3 rounded-lg border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/50 dark:bg-emerald-950/20 text-center">
                        <span class="block text-xs font-medium text-emerald-700 dark:text-emerald-400">Nilai
                            Akhir</span>
                        <span class="block text-xl font-bold text-emerald-800 dark:text-emerald-300 mt-0.5">
                            <span x-text="$store.cpl?.rekap_cpl_pr"></span>
                        </span>
                    </div>

                    <div
                        class="p-3 rounded-lg border border-blue-200 dark:border-blue-900/60 bg-blue-50/50 dark:bg-blue-950/20 text-center">
                        <span class="block text-xs font-medium text-blue-700 dark:text-blue-400">Index (0-4)</span>
                        <span class="block text-xl font-bold text-blue-800 dark:text-blue-300 mt-0.5">
                            <span x-text="$store.cpl?.index_cpl_pr"></span>
                        </span>
                    </div>

                    <div
                        class="p-3 rounded-lg border border-amber-200 dark:border-amber-900/60 bg-amber-50/50 dark:bg-amber-950/20 text-center">
                        <span class="block text-xs font-medium text-amber-700 dark:text-amber-400">Mutu</span>
                        <span class="block text-xl font-bold text-amber-800 dark:text-amber-300 mt-0.5">
                            <span x-text="$store.cpl?.mutu_cpl_pr"></span>
                        </span>
                    </div>

                </div>

            </div>
        </div>
    @endif
</div>
