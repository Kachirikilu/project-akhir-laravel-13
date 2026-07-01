<div>
    @if (!empty($parent))
        <flux:modal name="tim-dosen-modal" wire:model.live="showTimDosenModal" x-data flyout wire:key="tim-dosen-modal-flyout" 
            @refresh-data-tim-dosen.window="$store.tim_dosen.reset()"
            class="w-full md:w-[90vw] max-w-3xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
            @include('livewire.staff.obe-management.tim-dosen-management.tim-dosen-modal-form')
        </flux:modal>
    @else
        <flux:modal name="tim-dosen-modal" wire:model.live="showTimDosenModal" x-data wire:key="tim-dosen-modal" 
            @refresh-data-tim-dosen.window="$store.tim_dosen.reset()"
            class="w-full md:w-[90vw] max-w-3xl h-[98vh] !p-4 sm:!p-6 md:!p-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
            @include('livewire.staff.obe-management.tim-dosen-management.tim-dosen-modal-form')
        </flux:modal>
    @endif
</div>
