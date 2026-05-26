<div
    class="px-4 py-6 mt-4 
    {{-- bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700  --}}
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        RPS yang Terhubung di {{ $nameXString }} ini</h4>

    <div class="relative">

        <template x-if="$store.{{ $alpine ?? 'config' }}?.isEdit == 1">
            @include('livewire.global.modal-form.loading-animation', [
                'wireLoading' => 'add' . ($wireLoading ?? $nameXString) . ', edit' . ($wireLoading ?? $nameXString),
            ])
        </template>

        <div class="space-y-4">
            @php
                // Tema Hijau (Emerald) sesuai permintaan
                $theme = [
                    'bg' => 'bg-emerald-600',
                    'shadow' => 'shadow-emerald-200',
                    'icon' => 'text-emerald-300',
                    'hover-border' => 'hover:border-emerald-300 dark:hover:border-emerald-800',
                    'hover-bg' => 'hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10',
                    'border-l' => 'border-l-emerald-600',
                    'badge-bg' => 'bg-emerald-100 dark:bg-emerald-900/30',
                    'badge-text' => 'text-emerald-700 dark:text-emerald-400',
                    'link' => 'text-emerald-600 dark:text-emerald-400',
                    'flux-badge' => 'emerald',
                ];
            @endphp

            <div
                class="px-4 py-6 mt-4 bg-[var(--main-table-color)] border-[var(--border-table-color)] shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
                <div class="flex items-center gap-3 mb-6 border-b border-[var(--border-table-color)] pb-4">
                    <div class="p-2 {{ $theme['bg'] }} rounded-lg shadow-sm {{ $theme['shadow'] }}">
                        <flux:icon.clipboard-document-list variant="solid" class="size-4 {{ $theme['icon'] }}" />
                    </div>
                    <div>
                        <h3 class="font-bold text-zinc-900 dark:text-white leading-none text-sm">RPS Terhubung</h3>
                        <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">Daftar Mata Kuliah yang
                            menggunakan {{ $nameXString }} ini</p>
                    </div>
                </div>

                {{-- WADAH LIST DENGAN SCROLL --}}
                <div class="relative space-y-3 max-h-[450px] overflow-y-auto pr-2 scrollbar-thin bg-gray-50/30 dark:bg-neutral-800/30">

                    @php
                        $rps_items_list = is_iterable($rps_items_list) ? $rps_items_list : [];
                    @endphp

                    @if (count($rps_items_list) === 0)
                        <div
                            class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl opacity-50">
                            <flux:icon.document-text class="size-8 mb-2" />
                            <template x-if="$store.{{ $alpine ?? 'config' }}?.isEdit == 0">
                                <p class="text-xs italic">Buat terlebih dahulu {{ $nameXString }}!</p>
                            </template>
                            <template x-if="$store.{{ $alpine ?? 'config' }}?.isEdit == 1">
                                <p class="text-xs italic">Belum ada RPS yang terhubung!</p>
                            </template>
                        </div>
                    @else
                        @foreach ($rps_items_list as $index => $r)
                            <div wire:key="rps-row-{{ $r['id'] }}-{{ $nameXString }}" x-data="{ expanded: false }"
                                wire:target="loadingRPSList"
                                wire:loading.class="opacity-50 pointer-events-none transition-opacity"
                                class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden transition-all shadow-sm {{ $theme['hover-border'] }}">

                                {{-- Header Ringkas (Clickable) --}}
                                <div x-on:click="expanded = !expanded"
                                    class="p-3 cursor-pointer flex items-center gap-3 border-l-4 {{ $theme['hover-bg'] }} {{ $theme['border-l'] }}">
                                    {{-- 
                                    <div
                                        class="size-8 flex-none flex items-center justify-center rounded-md font-bold text-xs {{ $theme['badge-bg'] }} {{ $theme['badge-text'] }}">
                                        ID{{ $r['id'] }}
                                    </div> --}}

                                    <span
                                        class="text-xs font-bold px-1.5 py-0.5 mb-0.5 rounded {{ $theme['badge-bg'] }} {{ $theme['badge-text'] }}">ID{{ $r['id'] }}</span>

                                    <div class="flex-grow min-w-0">
                                        <div class="flex items-center gap-4">
                                            <p class="text-sm font-bold truncate text-zinc-800 dark:text-zinc-200">
                                                {{ $r['mk'] }}</p>
                                            <span
                                                class="shrink-0 mt-0.5 text-xs font-mono px-2 py-0.5 rounded border border-transparent {{ $theme['badge-bg'] }} {{ $theme['badge-text'] }}">
                                                {{ $r['kode'] }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-zinc-500 font-medium italic">{{ $r['akademik'] }}</p>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        @if ($r['draf'])
                                            <flux:badge size="xs" color="yellow">{{ $r['draf_text'] }}
                                            </flux:badge>
                                        @else
                                            <flux:badge size="xs" color="green">{{ $r['draf_text'] }}
                                            </flux:badge>
                                        @endif
                                        <flux:icon.chevron-down variant="micro"
                                            class="text-zinc-400 transition-transform duration-200"
                                            x-bind:class="expanded ? 'rotate-180' : ''" />
                                    </div>
                                </div>

                                {{-- Detail Expandable --}}
                                <div x-show="expanded" x-collapse>
                                    <div class="px-3 pb-3 pt-0 ml-1">
                                        <div
                                            class="p-3 bg-zinc-50 dark:bg-zinc-800/40 rounded-lg border border-zinc-100 dark:border-zinc-700 space-y-3">
                                            {{-- 1. Deskripsi --}}
                                            <div>
                                                <span
                                                    class="text-xs font-bold uppercase {{ $theme['link'] }}">Deskripsi
                                                    RPS:</span>
                                                <p
                                                    class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed mt-1">
                                                    {{ $r['deskripsi'] ?? '-' }}
                                                </p>
                                            </div>

                                            {{-- 2. Detail Informasi (Grid) --}}
                                            <div
                                                class="grid grid-cols-2 gap-x-6 gap-y-4 pt-4 border-t border-zinc-200/60 dark:border-zinc-700/50">

                                                {{-- SKS & Status --}}
                                                <div class="flex items-center gap-2.5">
                                                    <flux:icon.credit-card variant="micro"
                                                        class="opacity-50 {{ $theme['link'] }}" />
                                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Bobot: <b
                                                            class="text-zinc-700 dark:text-zinc-200 font-semibold">{{ $r['sks_text'] }}</b></span>
                                                </div>

                                                <div class="flex items-center gap-2.5">
                                                    <flux:icon.shield-check variant="micro"
                                                        class="opacity-50 {{ $theme['link'] }}" />
                                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Status: <b
                                                            class="text-zinc-700 dark:text-zinc-200 font-semibold">{{ $r['wajib_text'] }}</b></span>
                                                </div>

                                                {{-- CPMK & Sub-CPMK --}}
                                                <div class="flex items-center gap-2.5">
                                                    <flux:icon.academic-cap variant="micro"
                                                        class="opacity-50 {{ $theme['link'] }}" />
                                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">CPMK: <b
                                                            class="text-zinc-700 dark:text-zinc-200 font-semibold">{{ $r['count_cpmk'] }}
                                                            Pokok</b></span>
                                                </div>

                                                <div class="flex items-center gap-2.5">
                                                    <flux:icon.list-bullet variant="micro"
                                                        class="opacity-50 {{ $theme['link'] }}" />
                                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Sub-CPMK: <b
                                                            class="text-zinc-700 dark:text-zinc-200 font-semibold">{{ $r['count_scpmk'] }}
                                                            Items</b></span>
                                                </div>

                                                {{-- Total Bobot Nilai (Versi Nyaru) --}}
                                                <div class="col-span-2 flex items-center gap-2.5 pt-1">
                                                    <div
                                                        class="flex items-center gap-2 px-2.5 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 shadow-sm">
                                                        <flux:icon.chart-pie variant="micro"
                                                            class="{{ $theme['link'] }}" />
                                                        <span class="text-xs text-zinc-500">Total Akumulasi: <b
                                                                class="text-zinc-800 dark:text-zinc-100">{{ $r['total_bobot'] }}%</b></span>
                                                    </div>
                                                </div>

                                                {{-- 3. Tombol Aksi (Minimalist Style) --}}
                                                <div
                                                    class="col-span-2 flex items-center gap-4 pt-3 mt-1 border-t border-zinc-200/50 dark:border-zinc-700/50">

                                                    {{-- Action Link: Edit --}}
                                                    <button type="button"
                                                        class="cursor-pointer group flex items-center gap-1.5 text-xs font-medium text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200"
                                                        @click="
                                                                $store.rps?.reset();
                                                                $store.rps?.setEdit(1);
                                                                $store.rps?.setFlyout(true);
                                                                $store.rps?.setColor('text-emerald-700 dark:text-emerald-400');
                                                                $store.rps?.setValueRPS(
                                                                    '{{ $r['kode_blok'] ?? '' }}',
                                                                    '{{ $r['deskripsi'] ?? '' }}',
                                                                    '{{ $r['mk_id'] ?? '' }}',
                                                                    '{{ $r['kode_mk'] ?? '' }}',
                                                                    '{{ $r['mk'] ?? '' }}',
                                                                    '{{ $r['akademik'] ?? '' }}',
                                                                    '{{ $r['draf'] ?? '' }}',
                                                                    '{{ $r['count_scpmk'] }}',
                                                                    '{{ $r['bobot_uts'] }}',
                                                                    '{{ $r['bobot_uas'] }}',
                                                                    '{{ $r['total_bobot'] }}'
                                                                );
                                                                $flux.modal('rps-modal').show();
                                                            "
                                                        wire:click="editRPS('{{ $r['id'] }}')">

                                                        <div
                                                            class="p-1 rounded-md bg-zinc-100 dark:bg-zinc-800 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/30 transition-colors">
                                                            <flux:icon name="pencil-square" variant="micro"
                                                                class="w-3.5 h-3.5" />
                                                        </div>
                                                        <span>Edit RPS</span>
                                                        <flux:icon wire:loading wire:target="editRPS()"
                                                            name="arrow-path" class="animate-spin h-4 w-4 ml-2" />
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if (!empty($rps_modal_paginator) && $rps_modal_paginator->hasPages())
                            <div class="py-4" id="pagination-links-container">
                                <div
                                    wire:target="gotoPage, previousPage, nextPage, {{ $rps_modal_paginator->getPageName() }}">
                                    {{ $rps_modal_paginator->links('vendor.pagination.tailwind', [
                                        'typeXLoading' => 'loadingRPSList',
                                    ]) }}
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
