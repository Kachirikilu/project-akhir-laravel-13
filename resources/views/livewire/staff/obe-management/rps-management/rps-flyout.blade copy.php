{{-- @if (
    ($this->showCPMKModal && $this->isEditingCPMK) ||
        ($this->showSCPMKModal && $this->isEditingSCPMK) ||
        ($this->showCPLModal && $this->isEditingCPL) ||
        ($this->showRefModal && $this->isEditingRef))
    <template x-if="$store.rps?.isFlyout == 1">
        <flux:modal name="rps-modal" wire:model="showRPSModal" x-data @refresh-data-rps.window="$store.rps.reset()" flyout
            class="md:w-[90vw] max-w-4xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
            @include('livewire.staff.obe-management.rps-management.rps-modal-form')
        </flux:modal>
    </template>
@endif

<template x-if="$store.rps?.isFlyout == 0"> --}}
    <flux:modal name="rps-modal" wire:model="showRPSModal" x-data @refresh-data-rps.window="$store.rps.reset()"
        class="md:w-[90vw] max-w-5xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
        @include('livewire.staff.obe-management.rps-management.rps-modal-form')
    </flux:modal>
{{-- </template> --}}

{{-- <template x-teleport="body">
    <div x-data="{
        open: @entangle('showRPSModal'),
        flyout: false,
    
        init() {
            this.$watch('$wire.showCPMKModal', () => this.updateFlyout())
            this.$watch('$wire.showSCPMKModal', () => this.updateFlyout())
            this.$watch('$wire.showCPLModal', () => this.updateFlyout())
            this.$watch('$wire.showRefModal', () => this.updateFlyout())
    
            this.$watch('$wire.isEditingCPMK', () => this.updateFlyout())
            this.$watch('$wire.isEditingSCPMK', () => this.updateFlyout())
            this.$watch('$wire.isEditingCPL', () => this.updateFlyout())
            this.$watch('$wire.isEditingRef', () => this.updateFlyout())
    
            this.updateFlyout()
        },
    
        updateFlyout() {
            this.flyout =
                ($wire.showCPMKModal && $wire.isEditingCPMK) ||
                ($wire.showSCPMKModal && $wire.isEditingSCPMK) ||
                ($wire.showCPLModal && $wire.isEditingCPL) ||
                ($wire.showRefModal && $wire.isEditingRef)
        }
    }" x-show="open" x-cloak class="fixed inset-0 z-[10000] flex items-center justify-center">

        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open = false" x-transition.opacity></div>
        <div x-bind:class="flyout
            ?
            'ml-auto h-full w-[500px] max-w-full' :
            'mx-auto w-[90vw] max-w-5xl h-[98vh]'"
            class="relative bg-[var(--second-pop-up-color)] border table-border rounded-xl shadow-xl overflow-hidden transition-all duration-300 flex flex-col"
            x-transition>
            <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-200 dark:border-white/10">
                <h2 class="text-lg font-semibold text-[var(--contrast-main-text)]">
                    RPS Modal
                </h2>

                <button @click="open = false" class="p-2 rounded-md hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                    ✕
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4">
                @include('livewire.staff.obe-management.rps-management.rps-modal-form')
            </div>

        </div>
    </div>
</template> --}}


:size="($isSmall ?? false) ? 'sm' : null"