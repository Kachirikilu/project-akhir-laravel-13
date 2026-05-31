<div
    class="px-4 py-6 mt-4 bg-[var(--main-table-color)] border-[var(--border-table-color)] shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <div
        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4  border-b border-[var(--contrast-second-text)] pb-4">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-lg">
                <flux:icon icon="user" variant="mini" class="w-5 h-5" />
            </div>
            <div>
                <h3 class="text-base font-bold text-[var(--contrast-main-text)] tracking-wide" x-text="$store.sesi?.nama_mahasiswa"></h3>
                <p class="text-xs text-[var(--contrast-second-text)] font-mono mt-0.5" x-text="'NIM: ' + $store.sesi?.nim_mahasiswa"></p>
            </div>
        </div>

        <div>
            @include('livewire.global.table.badge.kode-wilayah-badge', [
                'xValue' => $jadwal->kode,
                'sortir' => $jadwal->kode_wilayah,
            ])
        </div>
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--contrast-second-text)] mb-3">
            Akumulasi Rekap Absensi
        </label>

        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2">

                        <div
                class="p-2.5 rounded-lg border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/50 dark:bg-emerald-950/20 text-center">
                <span class="block text-xs font-medium text-emerald-700 dark:text-emerald-400">Poin</span>
                <span class="block text-lg font-bold text-emerald-800 dark:text-emerald-300 mt-0.5"
                    x-text="$store.sesi?.mhs_poin_absensi +  '%'"></span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/50 dark:bg-emerald-950/20 text-center">
                <span class="block text-xs font-medium text-emerald-700 dark:text-emerald-400">Hadir</span>
                <span class="block text-lg font-bold text-emerald-800 dark:text-emerald-300 mt-0.5"
                    x-text="$store.sesi?.mhs_masuk +  ' / ' + '{{ $totalSesiKelas }}'"></span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-purple-200 dark:border-purple-900/60 bg-purple-50/50 dark:bg-purple-950/20 text-center">
                <span class="block text-xs font-medium text-purple-700 dark:text-purple-400">Dispensasi</span>
                <span class="block text-lg font-bold text-purple-800 dark:text-purple-300 mt-0.5"
                    x-text="$store.sesi?.mhs_dispensasi +  ' / ' + '{{ $totalSesiKelas }}'"></span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-amber-200 dark:border-amber-900/60 bg-amber-50/50 dark:bg-amber-950/20 text-center">
                <span class="block text-xs font-medium text-amber-700 dark:text-amber-400">Terlambat</span>
                <span class="block text-lg font-bold text-amber-800 dark:text-amber-300 mt-0.5"
                    x-text="$store.sesi?.mhs_terlambat +  ' / ' + '{{ $totalSesiKelas }}'"></span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-blue-200 dark:border-blue-900/60 bg-blue-50/50 dark:bg-blue-950/20 text-center">
                <span class="block text-xs font-medium text-blue-700 dark:text-blue-400">Izin</span>
                <span class="block text-lg font-bold text-blue-800 dark:text-blue-300 mt-0.5"
                    x-text="$store.sesi?.mhs_izin +  ' / ' + '{{ $totalSesiKelas }}'"></span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-sky-200 dark:border-sky-900/60 bg-sky-50/50 dark:bg-sky-950/20 text-center">
                <span class="block text-xs font-medium text-sky-700 dark:text-sky-400">Sakit</span>
                <span class="block text-lg font-bold text-sky-800 dark:text-sky-300 mt-0.5"
                    x-text="$store.sesi?.mhs_sakit +  ' / ' + '{{ $totalSesiKelas }}'"></span>
            </div>

            <div
                class="p-2.5 rounded-lg border border-rose-200 dark:border-rose-900/60 bg-rose-50/50 dark:bg-rose-950/20 text-center">
                <span class="block text-xs font-medium text-rose-700 dark:text-rose-400">Tidak Hadir</span>
                <span class="block text-lg font-bold text-rose-800 dark:text-rose-300 mt-0.5"
                    x-text="$store.sesi?.mhs_tidak_masuk +  ' / ' + '{{ $totalSesiKelas }}'"></span>
            </div>
        </div>
    </div>

</div>
