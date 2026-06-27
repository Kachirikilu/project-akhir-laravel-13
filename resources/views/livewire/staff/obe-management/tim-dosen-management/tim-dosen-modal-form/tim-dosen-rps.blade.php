<div
    class="px-4 py-6 mt-4 bg-[var(--main-table-color)] table-border shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <div
        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--contrast-second-text)] pb-4">
        <div class="flex items-start gap-3">
            <div :class="[$store.tim_dosen?.colorIcon, $store.tim_dosen?.colorIconBg]" class="p-2.5 rounded-lg">
                <flux:icon icon="user-group" variant="mini" class="w-5 h-5" />
            </div>
            <div>
                <h3 class="text-base font-bold text-[var(--contrast-main-text)] tracking-wide"
                    x-text="$store.tim_dosen?.nama_tim ?? 'Tim Wildan Athif Muttaqien'"></h3>
                <p class="text-xs text-[var(--contrast-second-text)] font-mono mt-0.5"
                    x-text="'Ketua: ' + ($store.tim_dosen?.ketua ?? 'Wildan Athif Muttaqien')"></p>
                <p class="text-xs text-[var(--contrast-second-text)] font-mono mt-0.5"
                    x-text="'NIP Ketua: ' + ($store.tim_dosen?.nip ?? '03041282227063')"></p>
            </div>
        </div>

    </div>

    <div>
        <label  x-text="'Program Studi ' + ($store.tim_dosen?.prodi ?? 'S1 Teknik Elektro')" class="block text-xs font-semibold uppercase tracking-wider text-[var(--contrast-second-text)] mb-3">
        </label>

        <div class="space-y-3">

            <div class="grid grid-cols-3 gap-3">
                <div
                    class="p-3 rounded-lg border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/50 dark:bg-emerald-950/20 text-center">
                    <span class="block text-xs font-medium text-emerald-700 dark:text-emerald-400">Koordinator</span>
                    <span class="block text-xl font-bold text-emerald-800 dark:text-emerald-300 mt-0.5">
                        <span x-text="$store.tim_dosen?.count_koordinator"></span>
                    </span>
                </div>

                <div
                    class="p-3 rounded-lg border border-blue-200 dark:border-blue-900/60 bg-blue-50/50 dark:bg-blue-950/20 text-center">
                    <span class="block text-xs font-medium text-blue-700 dark:text-blue-400">Pengajar</span>
                    <span class="block text-xl font-bold text-blue-800 dark:text-blue-300 mt-0.5">
                        <span x-text="$store.tim_dosen?.count_pengajar"></span>
                    </span>
                </div>

                <div
                    class="p-3 rounded-lg border border-amber-200 dark:border-amber-900/60 bg-amber-50/50 dark:bg-amber-950/20 text-center">
                    <span class="block text-xs font-medium text-amber-700 dark:text-amber-400">Asisten</span>
                    <span class="block text-xl font-bold text-amber-800 dark:text-amber-300 mt-0.5">
                        <span x-text="$store.tim_dosen?.count_asisten"></span>
                    </span>
                </div>

            </div>

            <div class="grid grid-cols-2 gap-3">

                <div
                    class="p-3 rounded-lg border border-lime-200 dark:border-lime-900/60 bg-lime-50/50 dark:bg-lime-950/20 text-center">
                    <span class="block text-xs font-medium text-lime-700 dark:text-lime-400">Jumlah RPS</span>
                    <span class="block text-xl font-bold text-lime-800 dark:text-lime-300 mt-0.5">
                        <span x-text="$store.tim_dosen?.count_rps"></span>
                    </span>
                </div>

                <div
                    class="p-3 rounded-lg border border-purple-200 dark:border-purple-900/60 bg-purple-50/50 dark:bg-purple-950/20 text-center">
                    <span class="block text-xs font-medium text-purple-700 dark:text-purple-400">Total SKS</span>
                    <span class="block text-xl font-bold text-purple-800 dark:text-purple-300 mt-0.5">
                        <span x-text="$store.tim_dosen?.total_sks"></span>
                    </span>
                </div>

            </div>

        </div>
    </div>
</div>
