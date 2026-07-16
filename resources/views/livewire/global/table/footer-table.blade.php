<div>
    @if ($typeXString->hasPages())
        <div class="{{ $mx ?? 'mx-3' }} my-3" id="pagination-links-container" wire:target="{{ $typeXString->getPageName() }}">
            {{ $typeXString->links('vendor.pagination.tailwind') }}
        </div>
    @endif

    @include('livewire.global.table.trash-delete', ['mx' => $mx ?? 'mx-3'])
</div>
