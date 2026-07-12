<div x-show="expanded.includes(index)" x-collapse>
    <div class="px-4 pb-4 bg-white/20 dark:bg-black/5">
        <div class="border-t table-border pt-3 overflow-x-auto scrollbar-medium">

            <table class="w-full text-xs text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-tighter border-b table-border">
                        <th class="pb-3 px-4 text-center">ID DSN</th>
                        <th class="pb-3 px-4 font-bold">NIP</th>
                        <th class="pb-3 px-4 min-w-32">Nama</th>
                        <th class="pb-3 px-4">Peran</th>
                        <th class="pb-3 px-4 text-center">Ketua</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-table-color)]">
                    <template x-for="sub in subItems[index]?.dosen" :key="sub.id">
                        <tr
                            class="hover:bg-black/5 dark:hover:bg-white/5 active:bg-black/10 dark:active:bg-white/10 transition-colors">
                            <td class="text-xs sm:text-sm py-2.5 px-2 leading-relaxed text-center"
                                x-text="sub.id || '-'">
                            </td>
                            <td class="text-xs sm:text-sm py-2.5 px-2">
                                <flux:badge color="fuchsia" size="sm"
                                    class="py-0 px-1.5 text-xs font-bold uppercase">
                                    <span x-text="sub.kode || '-'"></span>
                                </flux:badge>
                            </td>
                            <td class="text-xs sm:text-sm py-2.5 px-2 leading-relaxed" x-text="sub.name || '-'">
                            </td>
                            <td class="text-xs sm:text-sm py-2.5 px-2 leading-relaxed" x-text="sub.peran || '-'"></td>
                            
                            <td class="text-xs sm:text-sm py-2.5 px-2 leading-relaxed">
                                <div class="flex justify-center">
                                    <template x-if="sub.is_ketua == 1">
                                        <flux:badge color="blue" size="sm" class="text-xs font-bold uppercase">Ketua Tim</flux:badge>
                                    </template>
                                    <template x-if="sub.is_ketua == 0"><span>-</span></template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

        </div>
    </div>
</div>
