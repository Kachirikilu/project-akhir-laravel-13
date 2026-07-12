<div class="flex flex-col h-full animate-pulse p-6 space-y-6">

    {{-- Header --}}
    <div class="border-b border-[var(--border-table-color)] pb-4">
        <div class="h-6 w-48 rounded-lg bg-[var(--main-table-color)]"></div>
    </div>

    {{-- Universal Inputs --}}
    <div class="flex-1 space-y-6 scrollbar-medium">

        <div class="space-y-2">
            {{-- Label --}}
            <div class="h-3 w-24 rounded bg-[var(--main-table-color)]"></div>

            {{-- Field --}}
            <div
                class="h-10 w-full rounded-xl border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-4 flex items-center">
                <div class="h-3 w-1/3 rounded bg-[var(--sub-table-color)]"></div>
            </div>
        </div>

    </div>
    {{-- Footer Actions --}}
    <div class="pt-4 border-t border-[var(--border-table-color)] flex justify-end gap-3">
        <div class="h-9 w-24 rounded-lg bg-[var(--focus-color)] opacity-40"></div>
    </div>

</div>
