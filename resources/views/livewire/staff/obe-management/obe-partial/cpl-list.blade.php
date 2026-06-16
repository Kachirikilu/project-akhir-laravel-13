<div class="space-y-4">
    {{-- HEADER --}}
    <div class="flex items-center gap-3">
        <div class="p-2 bg-emerald-600 rounded-lg shadow-sm shadow-emerald-200">
            <flux:icon.academic-cap variant="solid" class="size-4 text-emerald-300" />
        </div>
        <div>
            <h3 class="font-bold text-zinc-900 dark:text-white leading-none text-sm">
                Capaian Pembelajaran Lulusan dari CPMK
            </h3>
            <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">Mapping CPL</p>
        </div>
    </div>

    {{-- LIST AREA --}}
    <div class="border-2 border-dashed table-border rounded-xl p-3 bg-gray-50/30 dark:bg-neutral-800/30">

        {{-- SUBHEADER --}}
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Mapping CPL:</span>
            <template x-if="$store.rps.cpl_cpmk.length > 0">
                <span class="text-xs px-3 py-1 bg-[var(--focus-color)] text-white rounded-full font-bold uppercase tracking-tighter"
                    x-text="$store.rps.cpl_cpmk.length + ' CPL Terhubung'"></span>
            </template>
        </div>

        {{-- LIST --}}
        <div class="space-y-2 max-h-[400px] overflow-y-auto pr-1 scrollbar-tiny">

            <template x-if="$store.rps.cpl_cpmk.length === 0">
                <div class="flex flex-col items-center justify-center py-8 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl opacity-50">
                    <flux:icon icon="clipboard" variant="outline" class="size-8 mb-2" />
                    <p class="text-xs italic font-medium">Belum ada mapping CPL untuk CPMK ini</p>
                </div>
            </template>

            <template x-for="(cpl, index) in $store.rps.cpl_cpmk" :key="cpl.id">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm hover:border-emerald-300 dark:hover:border-emerald-800 transition-all">
                    <div class="p-3 flex items-start gap-3 border-l-4 border-l-emerald-600">
                        <span class="flex-none text-xs font-black text-emerald-600 dark:text-emerald-400 w-4 mt-0.5"
                            x-text="index + 1"></span>
                        <div class="flex flex-col gap-1 flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400"
                                    x-text="cpl.kode"></span>
                                <span class="text-xs font-bold px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400"
                                    x-text="'ID' + cpl.id"></span>
                                <div class="h-px flex-1 bg-gray-200 dark:bg-neutral-800 opacity-50"></div>
                            </div>
                            <p class="text-xs text-[var(--contrast-main-text)] leading-relaxed" x-text="cpl.deskripsi"></p>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>
</div>