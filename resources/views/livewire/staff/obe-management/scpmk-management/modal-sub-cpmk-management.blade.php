<div>
    @if (!empty($parent))
        <flux:modal name="scpmk-modal" wire:model.live="showSCPMKModal" x-data flyout wire:key="scpmk-modal-flyout" 
            @refresh-data-scpmk.window="$store.scpmk.reset()"
            class="w-full md:w-[90vw] max-w-5xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
            @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form')
        </flux:modal>
    @else
        <flux:modal name="scpmk-modal" wire:model.live="showSCPMKModal" x-data wire:key="scpmk-modal" 
            @refresh-data-scpmk.window="$store.scpmk.reset()"
            class="w-full md:w-[90vw] max-w-5xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
            @include('livewire.staff.obe-management.scpmk-management.scpmk-modal-form')
        </flux:modal>
    @endif
</div>
