<div>
    @if (!empty($parent))
        <flux:modal :flyout="$isFlyoutUser" name="user-modal" wire:model.live="showUserModal" flyout wire:key="user-modal-flyout" 
            @refresh-data-user.window="if (!$wire.showUserModal) $store.user.reset()"
            class="w-full md:w-3xl max-w-4xl h-[98vh] !p-4 sm:!px-6 md:!px-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
            @include('livewire.admin.user-management.user-modal-form')
        </flux:modal>
    @else
        <flux:modal :flyout="$isFlyoutUser" name="user-modal" wire:model.live="showUserModal" wire:key="user-modal" 
            @refresh-data-user.window="if (!$wire.showUserModal) $store.user.reset()"
            class="w-full md:w-3xl max-w-4xl h-[98vh] !p-4 sm:!px-6 md:!px-8 !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">
            @include('livewire.admin.user-management.user-modal-form')
        </flux:modal>
    @endif
</div>
