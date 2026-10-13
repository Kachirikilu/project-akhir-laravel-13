<div x-show="expanded.includes(index)" x-collapse>
    <div class="px-4 pb-4 bg-white/20 dark:bg-black/5">
        <div class="border-t table-border pt-3 overflow-x-auto scrollbar-medium">

            <table class="w-full text-xs text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-tighter border-b table-border">
                        <th class="pb-3 px-4 text-center">ID</th>
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
                            <td class="text-xs sm:text-sm py-2.5 px-2 leading-relaxed" x-text="sub.materi || '-'">
                            </td>
                            <td class="text-xs sm:text-sm py-2.5 px-2 leading-relaxed" x-text="sub.metodologi || '-'">
                            </td>
                            <td class="text-xs sm:text-sm py-2.5 px-2 leading-relaxed" x-text="sub.indikator || '-'">
                            </td>
                            <td class="text-xs sm:text-sm py-2.5 px-2 leading-relaxed" x-text="sub.deskripsi || '-'">
                            </td>
                            @php
                                $utsFields = array_map(
                                    'strtoupper',
                                    config('rps.uts_fields', ['UTS', 'EVALUASI AWAL']),
                                );
                                $uasFields = array_map(
                                    'strtoupper',
                                    config('rps.uas_fields', [
                                        'UAS',
                                        'EVALUASI AKHIR',
                                        'LAPORAN AKHIR',
                                        'HASIL PROYEK',
                                        'HASIL PROJEK',
                                    ]),
                                );
                                $proyekFields = ['HASIL PROYEK', 'HASIL PROJEK', 'PORTOFOLIO'];
                                // Keluarkan item proyek dari pengecekan warna UAS agar tetap mendapat badge indigo (light-bulb)
                                $pureUasFields = array_values(array_diff($uasFields, $proyekFields));
                            @endphp

                            <td class="text-xs sm:text-sm py-2.5 px-2 text-center leading-relaxed">
                                <div class="flex justify-center" x-data="{
                                    utsList: @js($utsFields),
                                    uasList: @js($pureUasFields),
                                    proyekList: @js($proyekFields),
                                    norm(val) { return (val || '').toString().trim().toUpperCase(); }
                                }">

                                    {{-- Teori --}}
                                    <template x-if="norm(sub.metode) === 'TEORI'">
                                        <flux:badge icon="book-open" color="emerald" size="sm"
                                            variant="{{ $variant ?? 'pill' }}">
                                            <span x-text="sub.metode"></span>
                                        </flux:badge>
                                    </template>

                                    {{-- Praktik & Responsi --}}
                                    <template x-if="['PRAKTIK', 'RESPONSI'].includes(norm(sub.metode))">
                                        <flux:badge icon="beaker" color="cyan" size="sm"
                                            variant="{{ $variant ?? 'pill' }}">
                                            <span x-text="sub.metode"></span>
                                        </flux:badge>
                                    </template>

                                    {{-- Tugas, Logbook & Kuis --}}
                                    <template x-if="['TUGAS', 'LOGBOOK', 'KUIS'].includes(norm(sub.metode))">
                                        <flux:badge icon="pencil-square" color="blue" size="sm"
                                            variant="{{ $variant ?? 'pill' }}">
                                            <span x-text="sub.metode"></span>
                                        </flux:badge>
                                    </template>

                                    {{-- UTS & Evaluasi Awal (Dinamis dari config rps.uts_fields) --}}
                                    <template x-if="utsList.includes(norm(sub.metode))">
                                        <flux:badge icon="clipboard-document-check" color="amber" size="sm"
                                            variant="{{ $variant ?? 'pill' }}">
                                            <span x-text="sub.metode"></span>
                                        </flux:badge>
                                    </template>

                                    {{-- UAS, Evaluasi Akhir & Laporan Akhir (Dinamis dari config rps.uas_fields) --}}
                                    <template x-if="uasList.includes(norm(sub.metode))">
                                        <flux:badge icon="document-check" color="orange" size="sm"
                                            variant="{{ $variant ?? 'pill' }}">
                                            <span x-text="sub.metode"></span>
                                        </flux:badge>
                                    </template>

                                    {{-- Hasil Proyek, Hasil Projek & Portofolio --}}
                                    <template x-if="proyekList.includes(norm(sub.metode))">
                                        <flux:badge icon="light-bulb" color="indigo" size="sm"
                                            variant="{{ $variant ?? 'pill' }}">
                                            <span x-text="sub.metode"></span>
                                        </flux:badge>
                                    </template>

                                    {{-- Kerja Praktek --}}
                                    <template x-if="norm(sub.metode) === 'KERJA PRAKTEK'">
                                        <flux:badge icon="briefcase" color="violet" size="sm"
                                            variant="{{ $variant ?? 'pill' }}">
                                            <span x-text="sub.metode"></span>
                                        </flux:badge>
                                    </template>

                                    {{-- Skripsi --}}
                                    <template x-if="norm(sub.metode) === 'SKRIPSI'">
                                        <flux:badge icon="academic-cap" color="fuchsia" size="sm"
                                            variant="{{ $variant ?? 'pill' }}">
                                            <span x-text="sub.metode"></span>
                                        </flux:badge>
                                    </template>

                                    {{-- Aktivitas Partisipasif --}}
                                    <template x-if="norm(sub.metode) === 'AKTIVITAS PARTISIPASIF'">
                                        <flux:badge icon="user-group" color="rose" size="sm"
                                            variant="{{ $variant ?? 'pill' }}">
                                            <span x-text="sub.metode"></span>
                                        </flux:badge>
                                    </template>

                                    {{-- Mandiri --}}
                                    <template x-if="norm(sub.metode) === 'MANDIRI'">
                                        <flux:badge icon="user" color="slate" size="sm"
                                            variant="{{ $variant ?? 'pill' }}">
                                            <span x-text="sub.metode"></span>
                                        </flux:badge>
                                    </template>

                                    {{-- Default / Lainnya --}}
                                    <template
                                        x-if="![
            'TEORI', 'PRAKTIK', 'RESPONSI', 'TUGAS', 'LOGBOOK', 'KUIS',
            'KERJA PRAKTEK', 'SKRIPSI', 'AKTIVITAS PARTISIPASIF', 'MANDIRI',
            ...utsList, ...uasList, ...proyekList
        ].includes(norm(sub.metode))">
                                        <flux:badge icon="information-circle" color="zinc" size="sm"
                                            variant="{{ $variant ?? 'pill' }}">
                                            <span x-text="sub.metode || '-'"></span>
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
                                <td
                                    class="text-xs sm:text-sm py-2.5 px-2 text-center leading-relaxed text-[var(--contrast-main-text)]">

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
                                            {{-- wire:click="editSCPMK(sub.id)" --}}>

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
