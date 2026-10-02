<div class="flex flex-col">
    @include('livewire.global.table.partial.switch-table', [
        'xString' => 'showMore',
        'icon' => 'squares-2x2',
        'textTrue' => $textTrue ?? 'Detailed View',
        'textFalse' => $textFalse ?? 'Simple View',
        'colorTrue' => 'text-[var(--focus-color)]',
        'colorFalse' => 'text-gray-400',
        'autoSmall' => $autoSmall ?? false,
    ])
</div>
