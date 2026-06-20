<div wire:key="input-form-{{ $modelString }}-{{ $alpine }}">
    @include('livewire.global.modal-form.partial.label')
    <div class="relative mt-1">

        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="variable" variant="mini" x-bind:class="$store.{{ $alpine ?? 'config' }}?.colorIcon" />
        </div>

        <input type="text" x-bind:value="$store.{{ $alpine ?? 'config' }}?.{{ $modelString }} || '--'" readonly
            placeholder="--"
            class="text-xs sm:text-sm bg-[var(--second-table-color)] table-border text-[var(--contrast-main-text)] w-full border rounded-lg pl-10 px-3 py-2 text-center font-bold">
    </div>
</div>
