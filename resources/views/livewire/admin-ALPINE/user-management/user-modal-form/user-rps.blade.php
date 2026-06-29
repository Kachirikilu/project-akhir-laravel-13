<div
    class="px-4 py-6 mt-4 bg-[var(--main-table-color)] table-border shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <div
        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[var(--contrast-second-text)] pb-4">
        <div class="flex items-center gap-3">
            <div :class="[$store.user?.colorIcon, $store.user?.colorIconBg]" class="p-2.5 rounded-lg">
                <flux:icon icon="user" variant="mini" class="w-5 h-5" />
            </div>
            <div>
                <h3 class="text-base font-bold text-[var(--contrast-main-text)] tracking-wide"
                    x-text="$store.user?.name ?? 'Wildan Athif Muttaqien'"></h3>
                <template x-if="$store.user?.typeModal == 'dosen'" x-cloak>
                    <p class="text-xs text-[var(--contrast-second-text)] font-mono mt-0.5"
                        x-text="'NIP: ' + ($store.user?.nip ?? '03041282227063')"></p>
                </template>
                <template x-if="$store.user?.typeModal == 'mahasiswa'" x-cloak>
                    <p class="text-xs text-[var(--contrast-second-text)] font-mono mt-0.5"
                        x-text="'NIM: ' + ($store.user?.nim ?? '03041282227063')"></p>
                </template>

            </div>
        </div>
        {{-- <div>
            @include('livewire.global.table.badge.kode-wilayah-badge', [
                'xValue' => $jadwal->kode,
                'sortir' => $jadwal->kode_wilayah,
            ])
        </div> --}}
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--contrast-second-text)] mb-3">
            Akumulasi Rekap Hasil RPS
        </label>

        <div class="space-y-3">

            <template x-if="$store.user?.typeModal == 'mahasiswa'" x-cloak>

                <div class="grid grid-cols-3 gap-3">
                    <div
                        class="p-3 rounded-lg border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/50 dark:bg-emerald-950/20 text-center">
                        <span class="block text-xs font-medium text-emerald-700 dark:text-emerald-400">Nilai
                            Akhir</span>
                        <span class="block text-xl font-bold text-emerald-800 dark:text-emerald-300 mt-0.5">
                            <span x-text="$store.user?.rekap_mhs"></span>
                        </span>
                    </div>

                    <div
                        class="p-3 rounded-lg border border-blue-200 dark:border-blue-900/60 bg-blue-50/50 dark:bg-blue-950/20 text-center">
                        <span class="block text-xs font-medium text-blue-700 dark:text-blue-400">Index (0-4)</span>
                        <span class="block text-xl font-bold text-blue-800 dark:text-blue-300 mt-0.5">
                            <span x-text="$store.user?.index_mhs"></span>
                        </span>
                    </div>

                    <div
                        class="p-3 rounded-lg border border-amber-200 dark:border-amber-900/60 bg-amber-50/50 dark:bg-amber-950/20 text-center">
                        <span class="block text-xs font-medium text-amber-700 dark:text-amber-400">Mutu</span>
                        <span class="block text-xl font-bold text-amber-800 dark:text-amber-300 mt-0.5">
                            <span x-text="$store.user?.mutu_mhs"></span>
                        </span>
                    </div>

                </div>
            </template>

            <div class="grid grid-cols-2 gap-3">

                <div
                    class="p-3 rounded-lg border border-lime-200 dark:border-lime-900/60 bg-lime-50/50 dark:bg-lime-950/20 text-center">
                    <span class="block text-xs font-medium text-lime-700 dark:text-lime-400">Jumlah RPS</span>
                    <span class="block text-xl font-bold text-lime-800 dark:text-lime-300 mt-0.5">
                        <span x-text="$store.user?.count_rps"></span>
                    </span>
                </div>

                <div
                    class="p-3 rounded-lg border border-purple-200 dark:border-purple-900/60 bg-purple-50/50 dark:bg-purple-950/20 text-center">
                    <span class="block text-xs font-medium text-purple-700 dark:text-purple-400">Total SKS</span>
                    <span class="block text-xl font-bold text-purple-800 dark:text-purple-300 mt-0.5">
                        <span x-text="$store.user?.total_sks"></span>
                    </span>
                </div>

            </div>

        </div>
    </div>
</div>
