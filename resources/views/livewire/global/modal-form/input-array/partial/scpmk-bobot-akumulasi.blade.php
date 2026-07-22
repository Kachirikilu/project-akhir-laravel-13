<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-3">
    {{-- Header text --}}
    <span class="text-xs sm:text-sm font-bold uppercase tracking-widest text-gray-400">
        Daftar Terpilih:
    </span>

    {{-- Container konten: tambahkan flex-wrap dan justify-end agar rapi di mobile --}}
    <div class="flex flex-wrap items-center justify-end gap-2 w-full sm:w-auto">

        @include('livewire.global.modal-form.partial.reset-all-buttons')

        {{-- Badge Group --}}
        <div x-cloak>
            <template x-if="grandTotalBobot <= {{ $nilai1 }}">
                <flux:badge color="red" size="sm" variant="pill" class="text-[9px] sm:text-xs">
                    Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                </flux:badge>
            </template>
            <template x-if="grandTotalBobot > {{ $nilai1 }} && grandTotalBobot < {{ $nilai2 }}">
                <flux:badge color="orange" size="sm" variant="pill" class="text-[9px] sm:text-xs">
                    Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                </flux:badge>
            </template>
            <template x-if="grandTotalBobot >= {{ $nilai2 }} && grandTotalBobot <= {{ $nilai3 }}">
                <flux:badge color="green" size="sm" variant="pill" class="text-[9px] sm:text-xs">
                    Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                </flux:badge>
            </template>
            <template x-if="grandTotalBobot > {{ $nilai3 }}">
                <flux:badge color="blue" size="sm" variant="pill" class="text-[9px] sm:text-xs">
                    Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
                </flux:badge>
            </template>
        </div>

        <span x-show="items.length > 0" 
              class="text-xs px-3 py-1 bg-[var(--focus-color)] text-white rounded-full whitespace-nowrap"
              x-text="items.length + ' Terpilih'">
        </span>
    </div>
</div>