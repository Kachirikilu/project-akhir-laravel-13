<div x-show="expanded.includes(index)" x-collapse>
    <div class="px-4 pb-4 bg-white/20 dark:bg-black/5">
        <div class="border-t table-border pt-3 overflow-x-auto scrollbar-medium">

            <table class="w-full text-xs text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-tighter border-b table-border">
                        <th class="pb-3 px-4 text-center">Kode</th>
                        <th class="pb-3 px-4 text-center font-bold min-w-16">Kode</th>
                        <th class="pb-3 px-4 min-w-32">Deskripsi</th>
                        <th class="pb-3 px-4 min-w-32">Materi</th>
                        <th class="pb-3 px-4 min-w-32">Metodologi</th>
                        <th class="pb-3 px-4 min-w-32">Indikator</th>
                        <th class="pb-3 px-4 text-center">Metode</th>
                        <th class="pb-3 px-4 text-center">Bobot</th>
                        <th class="pb-3 px-4 min-w-32">Deskripsi Tugas</th>
                        <th class="pb-3 px-4 text-center">Waktu Tugas</th>
                        <th class="pb-3 px-4 text-center">Waktu Mandiri</th>
                        @unless ($parent == 'cpmk')
                            <th class="pb-3 px-4 text-center">Edit</th>
                        @endunless
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-table-color)]">
                    <template x-for="sub in subItems[index]?.scpmk" :key="sub.id">
                        <tr class="hover:bg-black/5 dark:hover:bg-white/5 active:bg-black/10 dark:active:bg-white/10 transition-colors">
                            <td class="text-xs sm:text-sm py-2.5 px-2 leading-relaxed text-center" x-text="sub.id || '-'">
                            </td>
                            <td class="text-xs sm:text-sm py-2.5 px-2">
                                <flux:badge color="fuchsia" size="sm"
                                    class="py-0 px-1.5 text-xs font-bold uppercase">
                                    <span x-text="sub.kode || '-'"></span>
                                </flux:badge>
                            </td>
                            <td class="text-xs sm:text-sm py-2.5 px-2 leading-relaxed" x-text="sub.materi || '-'">
                            </td>
                            <td class="text-xs sm:text-sm py-2.5 px-2 leading-relaxed" x-text="sub.metodologi || '-'"></td>
                            <td class="text-xs sm:text-sm py-2.5 px-2 leading-relaxed" x-text="sub.indikator || '-'">
                            </td>
                            <td class="text-xs sm:text-sm py-2.5 px-2 leading-relaxed" x-text="sub.deskripsi || '-'">
                            </td>
                            <td class="text-xs sm:text-sm py-2.5 px-2 text-center leading-relaxed">
                                <div class="flex justify-center">
                                    <template x-if="sub.metode === 'UTS' || sub.metode === 'UAS'">
                                        <flux:badge color="amber" size="sm" class="text-xs font-bold uppercase"
                                            x-text="sub.metode"></flux:badge>
                                    </template>
                                    <template x-if="sub.metode === 'Teori'">
                                        <flux:badge color="emerald" size="sm" class="text-xs font-bold">Teori
                                        </flux:badge>
                                    </template>
                                    <template x-if="['Praktik', 'Tugas', 'Hasil Proyek'].includes(sub.metode)">
                                        <flux:badge color="cyan" size="sm" class="text-xs font-bold"
                                            x-text="sub.metode">
                                        </flux:badge>
                                    </template>
                                    <template
                                        x-if="!['UTS', 'UAS', 'Teori', 'Praktik', 'Tugas', 'Hasil Proyek'].includes(sub.metode)">
                                        <flux:badge color="zinc" size="sm" class="text-xs font-bold"
                                            x-text="sub.metode || '-'">
                                        </flux:badge>
                                    </template>
                                </div>
                            </td>
                            <td class="text-xs sm:text-sm py-2.5 px-2 text-center leading-relaxed font-black text-[var(--hover-focus-color)]"
                                x-text="sub.bobot + '%'"></td>
                            <td class="text-xs sm:text-sm py-2.5 px-2 leading-relaxed text-[var(--contrast-main-text)]"
                                x-text="sub.tugas || '-'"></td>
                            <td class="text-xs sm:text-sm py-2.5 px-2 text-center leading-relaxed text-[var(--contrast-main-text)]"
                                x-text="sub.w_tugas || '-'"></td>
                            <td class="text-xs sm:text-sm py-2.5 px-2 text-center leading-relaxed text-[var(--contrast-main-text)]"
                                x-text="sub.w_mandiri || '-'"></td>

                            @unless ($parent == 'cpmk')
                                <td class="text-xs sm:text-sm py-2.5 px-2 text-center leading-relaxed text-[var(--contrast-main-text)]">

                                    {{-- 3. Tombol Aksi (Minimalist Style) --}}
                                    <div
                                        class="col-span-2 flex items-center justify-center border-zinc-200/50 dark:border-zinc-700/50">

                                        {{-- Action Link: Edit --}}
                                        <button type="button"
                                            class="cursor-pointer group flex items-center gap-1.5 text-xs font-medium text-zinc-500 hover:text-yellow-600 dark:hover:text-yellow-400 transition-colors duration-200"
                                            @click="
                                                $store.scpmk?.reset();
                                                $store.scpmk?.setEdit(1);
                                                $store.scpmk?.setFlyout(true);
                                                $store.scpmk?.setColor('text-fuchsia-700 dark:text-fuchsia-400');
                                                $store.scpmk?.setValueSCPMK(
                                                    sub.kode,
                                                    sub.deskripsi,
                                                    sub.materi,
                                                    sub.metodologi,
                                                    sub.indikator,
                                                    sub.metode,
                                                    sub.tugas,
                                                    sub.w_tugas,
                                                    sub.w_mandiri,
                                                    sub.bobot,
                                                );
                                                $flux.modal('scpmk-modal').show();
                                                $dispatch('open-edit-scpmk-modal', { id: sub.id, parent: '{{ $withParent ?? '' }}' });
                                            "
                                                {{-- wire:click="editSCPMK(sub.id)" --}}
                                            >

                                            <div
                                                class="p-1 rounded-md bg-zinc-100 dark:bg-zinc-800 group-hover:bg-yellow-50 dark:group-hover:bg-yellow-900/30 transition-colors">
                                                <flux:icon
                                                    wire:loading.class="opacity-50 pointer-events-none transition-opacity"
                                                    wire:target="editSCPMK()" name="pencil-square" variant="micro"
                                                    class="w-3.5 h-3.5" />
                                            </div>
                                        </button>

                                    </div>
                                </td>
                            @endunless
                        </tr>
                    </template>
                </tbody>
            </table>

        </div>
    </div>
</div>
