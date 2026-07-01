<div class="px-2 py-1.5" x-data="{ copied: false }">
    <div
        class="flex justify-between items-center w-full group p-2 gap-4 rounded-lg bg-zinc-50 dark:bg-white/5 border border-zinc-200 dark:border-white/10">

        {{-- TEXT --}}
        <div class="flex flex-col items-start text-left">
            <span class="text-xs uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-bold leading-tight">
                {{ trim($typeXString) ?? '' }}
            </span>

            <span class="text-sm font-mono font-semibold text-zinc-800 dark:text-zinc-200 mt-0.5">
                {{ $xType ?? '-' }}
            </span>
        </div>

        <div
            @click.stop="
                navigator.clipboard.writeText('{{ $xType }}');
                copied = true;
                setTimeout(() => copied = false, 2000);
            "
            class="p-2 rounded-md cursor-pointer hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all"
            role="button"
            tabindex="0"
x
title="Salin Kode">

            <flux:icon 
                x-show="!copied" 
                name="clipboard"
                class="h-4 w-4 text-zinc-500 dark:text-zinc-400 group-hover:text-zinc-800 dark:group-hover:text-zinc-200" 
            />

            <flux:icon 
                x-show="copied" 
                name="check" 
                class="h-4 w-4 text-emerald-500" 
                x-cloak 
            />
        </div>
    </div>
</div>