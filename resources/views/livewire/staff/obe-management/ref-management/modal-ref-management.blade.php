<div>
    @if (!empty($parent))
        <flux:modal name="ref-modal" wire:model.live="showRefModal" x-data @refresh-data-ref.window="$store.ref.reset()"
            flyout wire:key="ref-modal-flyout" 
            class="w-full md:w-[90vw] max-w-3xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
            @include('livewire.staff.obe-management.ref-management.ref-modal-form')
        </flux:modal>
    @else
        <flux:modal name="ref-modal" wire:model.live="showRefModal" x-data @refresh-data-ref.window="$store.ref.reset()" wire:key="ref-modal" 
            class="w-full md:w-[90vw] max-w-3xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
            @include('livewire.staff.obe-management.ref-management.ref-modal-form')
        </flux:modal>
    @endif
</div>
