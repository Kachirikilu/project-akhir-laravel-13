<div>
    @if (!empty($parent))
        <flux:modal name="cpl-modal" wire:model.live="showCPLModal" x-data @refresh-data-cpl.window="$store.cpl.reset()"
            flyout wire:key="cpl-modal-flyout" 
            class="w-full md:w-[90vw] max-w-3xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
            @include('livewire.staff.obe-management.cpl-management.cpl-modal-form')
        </flux:modal>
    @else
        <flux:modal name="cpl-modal" wire:model.live="showCPLModal" x-data @refresh-data-cpl.window="$store.cpl.reset()" wire:key="cpl-modal"
            class="w-full md:w-[90vw] max-w-3xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
            @include('livewire.staff.obe-management.cpl-management.cpl-modal-form')
        </flux:modal>
    @endif
</div>
