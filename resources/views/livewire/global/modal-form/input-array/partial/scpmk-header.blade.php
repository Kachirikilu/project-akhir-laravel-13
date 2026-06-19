<div
    class="flex items-start justify-between px-4 py-3 bg-white/40 dark:bg-black/10 transition-colors hover:bg-white/60 dark:hover:bg-black/20 active:bg-white/70 dark:active:bg-black/30">

    <div class="flex items-start gap-3 flex-1">
        {{-- NOMOR URUT --}}
        <span class="text-xs font-black text-[var(--hover-focus-color)] w-4 mt-0.5" x-text="index + 1"></span>

        <div class="flex flex-col gap-1 flex-1 cursor-pointer"
            x-on:click="expanded.includes(index) ? expanded = expanded.filter(i => i !== index) : expanded.push(index)">

            {{-- KODE SEBAGAI BADGE DI ATAS --}}
            <div class="text-xs text-sm flex items-center gap-2">
                <div class="flex items-center gap-1.5 mb-1.5">
                    <flux:icon icon="chevron-right" variant="mini" class="transition-transform duration-200"
                        x-bind:class="expanded.includes(index) ? 'rotate-90 text-[var(--hover-focus-color)]' :
                            'text-gray-400'" />
                    <span class="text-xs font-bold px-1.5 py-0.5 rounded bg-[var(--focus-color)] text-white uppercase"
                        x-text="itemsAll[index]?.kode"></span>
                </div>
                <div class="h-px flex-1 mb-1.5 bg-gray-200 dark:bg-neutral-150 opacity-40"></div>
            </div>

            {{-- NAMA UTAMA --}}
            <span class="text-xs text-sm mb-1 font-semibold text-[var(--contrast-main-text)] leading-tight"
                x-text="itemsAll[index]?.slot1"></span>

            {{-- DETAIL ID DAN TOTAL BOBOT DI BAWAH --}}
            <div class="text-xs text-sm flex items-center flex-wrap text-xs text-[var(--contrast-second-text)] gap-y-1">
                <span class="font-bold text-[var(--hover-focus-color)]" x-text="'ID: ' + id"></span>
                <span class="mx-1.5 opacity-50">|</span>
                <span class="flex items-center gap-1">
                    <span x-text="itemsAll[index]?.slot2"></span>
                    @if ($typeX2String == 'count_scpmk')
                         Pertemuan
                    @endif
                </span>
                <span class="mx-1.5 opacity-50">|</span>
                <span class="flex items-center gap-1">
                    @if ($typeX3String == 'total_bobot')
                        Total Bobot:
                    @else
                        Bobot:
                    @endif
                    <span class="font-black text-[var(--hover-focus-color)]"
                        x-text="(subItems[index]?.scpmk || []).reduce((t, s) => t + Number(s.bobot || 0), 0) + '%'">
                    </span>
                </span>
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS --}}
    @include('livewire.global.modal-form.partial.action-buttons')
</div>
