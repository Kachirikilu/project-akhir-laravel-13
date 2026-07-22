{{-- @if (!$this->showCPLModal && !$this->showRefModal)
    <template x-if="$store.scpmk?.isFlyout == 1">
        <flux:modal name="scpmk-modal" wire:model.live="showSCPMKModal" x-data @refresh-data-scpmk.window="$store.scpmk.reset()"
            flyout
            class="md:w-[90vw] max-w-3xl max-h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-large">
            @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form')
        </flux:modal>
    </template>
@endif

<template x-if="$store.scpmk?.isFlyout == 0"> --}}
    <flux:modal name="scpmk-modal" wire:model.live="showSCPMKModal" x-data @refresh-data-scpmk.window="$store.scpmk.reset()"
   class="md:w-[90vw] max-w-5xl max-h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-large">
        @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form')
    </flux:modal>
{{-- </template> --}}