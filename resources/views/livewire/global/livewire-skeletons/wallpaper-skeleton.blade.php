<div class="my-4 p-4 bg-[var(--sub-table-color)] rounded-2xl border border-[var(--border-table-color)] w-full animate-pulse">
    <div class="w-full flex justify-between items-center mb-4">
        <div class="h-4 w-32 bg-[var(--border-table-color)] rounded"></div>
    </div>

    <div class="flex gap-3 pb-2 mb-2 overflow-hidden">
        @for ($i = 0; $i < 4; $i++)
            <div class="flex-shrink-0 w-[90px] h-[210px] rounded-xl bg-[var(--border-table-color)]"></div>
        @endfor
    </div>

    <div class="pt-4 border-t border-[var(--border-table-color)] space-y-4">
        <div class="h-5 w-full bg-[var(--border-table-color)] rounded-lg"></div>
        <div class="h-5 w-full bg-[var(--border-table-color)] rounded-lg"></div>
    </div>
</div>