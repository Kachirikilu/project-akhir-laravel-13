@if (Auth::user()->admin || Auth::user()->dosen)
    @if (!($onlyAdmin ?? false))
        <div class="flex flex-col w-full">
            @include('livewire.global.table.partial.switch-table', [
                'xString' => 'showDeleted',
                'icon' => 'trash',
                'textTrue' => 'Trash Mode',
                'textFalse' => 'Active Data',
                'colorSwitchTrue' => 'bg-red-500',
                'colorSwitchFalse' => 'bg-[var(--focus-color)]',
                'colorMainTrue' => 'text-red-500',
                'colorMainFalse' => 'text-gray-400',
                'colorCheckTrue' => 'text-gray-400',
                'colorCheckFalse' => 'text-[var(--focus-color)]',
            ])
    @endif
@endif
