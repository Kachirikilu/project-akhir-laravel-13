<div class="flex items-center justify-between mb-4">
    <span class="text-sm font-bold uppercase tracking-widest text-gray-400">Daftar Terpilih:</span>
    <div class="flex items-center gap-2">

        @include('livewire.global.modal-form.partial.reset-all-buttons')

        <template x-if="grandTotalBobot <= {{ $nilai1 }}">
            <flux:badge color="red" size="sm" variant="pill">
                Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
            </flux:badge>
        </template>
        <template x-if="grandTotalBobot > {{ $nilai1 }} && grandTotalBobot < {{ $nilai2 }}">
            <flux:badge color="orange" size="sm" variant="pill">
                Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
            </flux:badge>
        </template>
        <template x-if="grandTotalBobot >= {{ $nilai2 }} && grandTotalBobot <= {{ $nilai3 }}">
            <flux:badge color="green" size="sm" variant="pill">
                Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
            </flux:badge>
        </template>
        <template x-if="grandTotalBobot > {{ $nilai3 }}">
            <flux:badge color="blue" size="sm" variant="pill">
                Akumulasi Bobot: <span class="ml-2" x-text="grandTotalBobot"></span>%
            </flux:badge>
        </template>
        <span x-show="items.length > 0" class="text-xs px-3 py-1 bg-[var(--focus-color)] text-white rounded-full"
            x-text="items.length + ' Terpilih'"></span>
    </div>
</div>
