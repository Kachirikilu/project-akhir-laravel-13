<div class="flex justify-end md:order-2 pb-2" x-data="{
    confirmExport: false,
    timeout: null,

    handleClick() {
        if (this.confirmExport) {
            $wire.{{ $xString }}();
            this.confirmExport = false;
            clearTimeout(this.timeout);
            return;
        }

        this.confirmExport = true;

        this.timeout = setTimeout(() => {
            this.confirmExport = false;
        }, 3000);
    }
}">
    <flux:button @click="handleClick" size="sm"
        class="cursor-pointer h-8 !text-xs border border-emerald-200 transition-colors !text-emerald-600 dark:!text-emerald-400 transition-all duration-200 ease-in-out"
        x-bind:class="confirmExport
            ?
            '!bg-emerald-100 dark:!bg-emerald-900/30' :
            'hover:!bg-emerald-100 dark:hover:!bg-emerald-900/30'">

        <div class="flex items-center">
            <flux:icon name="printer" class="mr-1 h-3.5 w-3.5" />
            <span x-bind:class="confirmExport ? 'font-bold' : 'font-medium'">Export Excel</span>
        </div>

        <flux:icon wire:loading wire:target="{{ $xString }}" name="arrow-path"
            class="animate-spin h-3.5 w-3.5 ml-2 dark:!text-emerald-600" />
    </flux:button>
</div>
