<div class="flex items-center gap-1 ml-4">
    <div class="flex flex-col gap-0.5">
        <button x-on:click="move(index, -1)" type="button" :disabled="index === 0"
            class="cursor-pointer p-0.5 hover:bg-black/5 dark:hover:bg-white/10 active:bg-white/20 active:bg-black/10 dark:active:bg-white/10 rounded disabled:opacity-10">
            <flux:icon icon="chevron-up" variant="mini" class="size-4" />
        </button>
        <button x-on:click="move(index, 1)" type="button" :disabled="index === items.length - 1"
            class="cursor-pointer p-0.5 hover:bg-black/5 dark:hover:bg-white/10 active:bg-white/20 active:bg-black/10 dark:active:bg-white/10 rounded disabled:opacity-10">
            <flux:icon icon="chevron-down" variant="mini" class="size-4" />
        </button>
    </div>
    <button x-on:click="removeItem(index)" type="button"
        class="cursor-pointer p-1.5 hover:bg-red-50 dark:hover:bg-red-900/20 active:bg-red-100 dark:active:bg-red-900/10 text-red-500 rounded-md transition-colors ml-1">
        <flux:icon icon="trash" variant="mini" class="size-5" />
    </button>
</div>
