<div>
    @if (!empty($parent))
        <flux:modal name="cpmk-modal" wire:model.live="showCPMKModal" x-data flyout wire:key="cpmk-modal-flyout" 
            @refresh-data-cpmk.window="$store.cpmk.reset()"
            class="w-full md:w-[90vw] max-w-5xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
            @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form')
        </flux:modal>
    @else
        <flux:modal name="cpmk-modal" wire:model.live="showCPMKModal" x-data wire:key="cpmk-modal" 
            @refresh-data-cpmk.window="$store.cpmk.reset()"
            class="w-full md:w-[90vw] max-w-5xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
            @include('livewire.staff.obe-management.cpmk-management.cpmk-modal-form')
        </flux:modal>
    @endif
</div>