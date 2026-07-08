<label
    class="bg-[var(--main-color)]/20 flex-shrink-0 w-[90px] h-[210px] rounded-xl border-2 border-dashed border-[var(--border-table-color)] flex items-center justify-center cursor-pointer hover:border-[var(--main-color)] transition-colors">
    <input type="file" class="hidden" accept="image/*" wire:model.live="wallpaper">

    <div wire:loading wire:target="wallpaper">
        <svg class="animate-spin w-6 h-6 text-[var(--main-color)]" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
            </path>
        </svg>
    </div>
    <div wire:loading.remove wire:target="wallpaper">
        <flux:icon name="plus" variant="mini" class="w-6 h-6 text-[var(--contrast-main-text)] opacity-50" />
    </div>
</label>
