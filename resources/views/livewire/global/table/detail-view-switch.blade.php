<div class="flex flex-col">
    @include('livewire.global.table.partial.switch-table', [
        'xString' => 'showMore',
        'icon' => 'squares-2x2',
        'textTrue' => 'Detailed View',
        'textFalse' => 'Simple View',
        'colorTrue' => 'text-[var(--focus-color)]',
        'colorFalse' => 'text-gray-400',
    ])
</div>
