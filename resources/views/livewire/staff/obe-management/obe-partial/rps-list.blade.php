<div
    class="px-4 py-6 mt-4 
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        RPS yang Terhubung di {{ $nameXString }} ini</h4>

    <div class="relative">

        <div class="space-y-4">

            @include('livewire.global.modal-form.loading-animation', [
                'wireLoading' => ($wireLoading ?? 'edit' . $nameXString) . ', loadingRPSList',
            ])

            {{-- HEADER --}}
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-600 rounded-lg shadow-sm shadow-emerald-200">
                    <flux:icon.clipboard-document-list variant="solid" class="size-4 text-emerald-300" />
                </div>
                <div>
                    <h3 class="font-bold text-zinc-900 dark:text-white leading-none text-sm">RPS Terhubung</h3>
                    <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">
                        Daftar RPS yang menggunakan {{ $nameXString }} ini
                    </p>
                </div>
            </div>

            {{-- LIST AREA --}}
            <div
                class="border-2 border-dashed border-[var(--border-table-color)] rounded-xl p-3 bg-gray-50/30 dark:bg-neutral-800/30">

                {{-- SUBHEADER --}}
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Daftar RPS:</span>
                    {{-- @if (count($rps_items_list) > 0)
                    <span
                        class="text-xs px-3 py-1 bg-[var(--focus-color)] text-white rounded-full font-bold uppercase tracking-tighter">
                        {{ count($rps_items_list) }} RPS
                    </span>
                @endif --}}
                </div>

                {{-- LIST --}}
                <div class="space-y-2 max-h-[450px] overflow-y-auto pr-1 scrollbar-thin">

                    @php $rps_items_list = is_iterable($rps_items_list) ? $rps_items_list : []; @endphp

                    @if (count($rps_items_list) === 0)
                        <div
                            class="flex flex-col items-center justify-center py-8 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl opacity-50">
                            <flux:icon.document-text class="size-8 mb-2" />
                            <template x-if="$store.{{ $alpine ?? 'config' }}?.isEdit == 0">
                                <p class="text-xs italic">Buat terlebih dahulu {{ $nameXString }}!</p>
                            </template>
                            <template x-if="$store.{{ $alpine ?? 'config' }}?.isEdit == 1">
                                <p class="text-xs italic">Belum ada RPS yang terhubung!</p>
                            </template>
                        </div>
                    @else
                        @foreach ($rps_items_list as $r)
                            <div wire:key="rps-row-{{ $r['id'] }}-{{ $nameXString }}" x-data="{ expanded: false }"
                                wire:loading.class="opacity-50 pointer-events-none"
                                wire:target="{{ $wireLoading ?? 'edit' . $nameXString }}, loadingRPSList"
                                class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm hover:border-emerald-300 dark:hover:border-emerald-800 transition-all">

                                {{-- HEADER ITEM (clickable) --}}
                                <div x-on:click="expanded = !expanded"
                                    class="p-3 cursor-pointer flex items-center gap-3 border-l-4 border-l-emerald-600 hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10">

                                    <span
                                        class="text-xs font-bold px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 flex-shrink-0">
                                        ID{{ $r['id'] }}
                                    </span>

                                    <div class="flex-grow min-w-0">
                                        <div class="flex items-center gap-3">
                                            <p class="text-sm font-bold truncate text-zinc-800 dark:text-zinc-200">
                                                {{ $r['mk'] }}</p>
                                            <span
                                                class="shrink-0 text-xs font-mono px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                                {{ $r['kode'] }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-zinc-500 font-medium italic">{{ $r['akademik'] }}</p>
                                    </div>

                                    <div class="flex items-center gap-2 flex-shrink-0">
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

                                {{-- DETAIL EXPANDABLE --}}
                                <div x-show="expanded" x-collapse>
                                    <div class="px-3 pb-3 pt-0 ml-1">
                                        <div
                                            class="p-3 bg-zinc-50 dark:bg-zinc-800/40 rounded-lg border border-zinc-100 dark:border-zinc-700 space-y-3">

                                            <div>
                                                <span
                                                    class="text-xs font-bold uppercase text-emerald-600 dark:text-emerald-400">Deskripsi
                                                    RPS:</span>
                                                <p
                                                    class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed mt-1">
                                                    {{ $r['deskripsi'] ?? '-' }}
                                                </p>
                                            </div>

                                            <div
                                                class="grid grid-cols-2 gap-x-6 gap-y-3 pt-3 border-t border-zinc-200/60 dark:border-zinc-700/50">
                                                <div class="flex items-center gap-2">
                                                    <flux:icon.credit-card variant="micro"
                                                        class="opacity-50 text-emerald-600 dark:text-emerald-400" />
                                                    <span class="text-xs text-zinc-500">Bobot: <b
                                                            class="text-zinc-700 dark:text-zinc-200">{{ $r['sks_text'] }}</b></span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <flux:icon.shield-check variant="micro"
                                                        class="opacity-50 text-emerald-600 dark:text-emerald-400" />
                                                    <span class="text-xs text-zinc-500">Status: <b
                                                            class="text-zinc-700 dark:text-zinc-200">{{ $r['wajib_text'] }}</b></span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <flux:icon.academic-cap variant="micro"
                                                        class="opacity-50 text-emerald-600 dark:text-emerald-400" />
                                                    <span class="text-xs text-zinc-500">CPMK: <b
                                                            class="text-zinc-700 dark:text-zinc-200">{{ $r['count_cpmk'] }}
                                                            Pokok</b></span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <flux:icon.list-bullet variant="micro"
                                                        class="opacity-50 text-emerald-600 dark:text-emerald-400" />
                                                    <span class="text-xs text-zinc-500">Sub-CPMK: <b
                                                            class="text-zinc-700 dark:text-zinc-200">{{ $r['count_scpmk'] }}
                                                            Items</b></span>
                                                </div>
                                                <div class="col-span-2 flex items-center gap-2 pt-1">
                                                    <div
                                                        class="flex items-center gap-2 px-2.5 py-1 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800/50 shadow-sm">
                                                        <flux:icon.chart-pie variant="micro"
                                                            class="text-emerald-600 dark:text-emerald-400" />
                                                        <span class="text-xs text-zinc-500">Total Akumulasi: <b
                                                                class="text-zinc-800 dark:text-zinc-100">{{ $r['total_bobot'] }}%</b></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="col-span-2 flex items-center gap-6 pt-3 mt-1 border-t border-zinc-200/50 dark:border-zinc-700/50">


                                                <button type="button"
                                                    class="cursor-pointer group flex items-center gap-1.5 text-xs font-medium text-zinc-500 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors duration-200"
                                                    @click="
                                                    $store.rps?.resetShow();
                                                    $store.rps?.setColor('text-emerald-700 dark:text-emerald-400');

                                                        $store.rps?.setShowRPS(
                                                            '{{ $r['id'] }}',
                                                            '{{ $r['rps'] }}',
                                                        );

                                                    $flux.modal('rps-detail-modal').show();
                                                "
                                                    wire:click="showRPS({{ $r['id'] }})">
                                                    <div
                                                        class="p-1 rounded-md bg-zinc-100 dark:bg-zinc-800 group-hover:bg-cyan-50 dark:group-hover:bg-cyan-900/30 transition-colors">
                                                        <flux:icon name="eye" variant="micro" class="w-3.5 h-3.5" />
                                                    </div>
                                                    <span>Show RPS</span>
                                                    <flux:icon wire:loading wire:target="showRPS({{ $r['id'] }})"
                                                        name="arrow-path" class="animate-spin h-4 w-4 ml-1" />
                                                </button>

                                                <button type="button"
                                                    class="cursor-pointer group flex items-center gap-1.5 text-xs font-medium text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 transition-colors duration-200"
                                                    wire:click="printPDFRPS({{ $r['id'] }})">
                                                    <div
                                                        class="p-1 rounded-md bg-zinc-100 dark:bg-zinc-800 group-hover:bg-rose-50 dark:group-hover:bg-rose-900/30 transition-colors">
                                                        <flux:icon name="printer" variant="micro" class="w-3.5 h-3.5" />
                                                    </div>
                                                    <span>Print PDF RPS</span>
                                                    <flux:icon wire:loading
                                                        wire:target="printPDFRPS({{ $r['id'] }})"
                                                        name="arrow-path" class="animate-spin h-4 w-4 ml-1" />
                                                </button>

                                                @unless (
                                                    $this->showRPSModal &&
                                                        !(
                                                            ($this->isEditingCPMK && !$this->isFlyoutCPMK) ||
                                                            ($this->isEditingSCPMK && !$this->isFlyoutSCPMK) ||
                                                            ($this->isEditingCPL && !$this->isFlyoutCPL) ||
                                                            ($this->isEditingRef && !$this->isFlyoutRef)
                                                        ))
                                                    {{-- Action Link: Edit --}}
                                                    <button type="button"
                                                        class="cursor-pointer group flex items-center gap-1.5 text-xs font-medium text-zinc-500 hover:text-yellow-600 dark:hover:text-yellow-400 transition-colors duration-200"
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
                                                            '{{ $r['total_bobot'] }}',
                                                            '{{ $r['mk_rel']->kode_semester ?? '' }}',
                                                        );
                                                        $flux.modal('rps-modal').show();
                                                    "
                                                        wire:click="editRPS('{{ $r['id'] }}')">
                                                        <div
                                                            class="p-1 rounded-md bg-zinc-100 dark:bg-zinc-800 group-hover:bg-yelow-50 dark:group-hover:bg-yelow-900/30 transition-colors">
                                                            <flux:icon name="pencil-square" variant="micro"
                                                                class="w-3.5 h-3.5" />
                                                        </div>
                                                        <span>Edit RPS</span>
                                                        <flux:icon wire:loading wire:target="editRPS({{ $r['id'] }})"
                                                            name="arrow-path" class="animate-spin h-4 w-4 ml-1" />
                                                    </button>
                                                @endunless

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if (!empty($rps_modal_paginator) && $rps_modal_paginator->hasPages())
                            <div class="py-4" id="pagination-links-container">
                                {{-- <div
                                wire:target="gotoPage, previousPage, nextPage, {{ $rps_modal_paginator->getPageName() }}"> --}}
                                {{ $rps_modal_paginator->links('vendor.pagination.tailwind', [
                                    'typeXLoading' => 'loadingRPSList',
                                ]) }}
                                {{-- </div> --}}
                            </div>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
