@if ($heightContainer ?? false)
    <div wire:loading wire:target="{{ $wireLoading ?? null }}" class="{{ $heightContainer }}">
@endif
<div wire:loading wire:target="{{ $wireLoading ?? null }}"
    class="absolute inset-0 z-50 flex items-center justify-center bg-[var(--second-table-color)]/60 backdrop-blur-[2px] rounded-xl h-full w-full">

    <div class="h-full flex flex-col items-center justify-center">
        <flux:icon name="arrow-path" class="animate-spin h-10 w-10 text-[var(--focus-color)]" />
        <p wire:stream="{{ $stream ?? '' }}" class="mt-4 text-sm font-medium text-[var(--contrast-second-text)] italic">
            {{ $textString ?? 'Menyinkronkan...' }}</p>
    </div>
</div>
@if ($heightContainer ?? false)
    </div>
@endif