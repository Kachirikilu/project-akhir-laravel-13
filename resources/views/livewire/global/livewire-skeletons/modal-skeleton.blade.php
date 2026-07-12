<div class="flex flex-col h-full animate-pulse p-6 space-y-6">

    {{-- Header --}}
    <div class="border-b border-[var(--border-table-color)] pb-4">
        <div class="h-6 w-48 rounded-lg bg-[var(--main-table-color)]"></div>
    </div>

    {{-- Universal Inputs --}}
    <div class="flex-1 space-y-6 scrollbar-medium">

        <div class="space-y-3">
            <div class="h-6 w-72 rounded bg-[var(--main-table-color)]"></div>
            <div class="h-px w-full bg-[var(--border-table-color)]"></div>
        </div>
        @foreach (range(1, 4) as $i)
            <div class="space-y-2">
                {{-- Label --}}
                <div class="h-3 w-24 rounded bg-[var(--main-table-color)]"></div>

                {{-- Field --}}
                <div
                    class="h-10 w-full rounded-xl border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-4 flex items-center">
                    <div class="h-3 w-1/3 rounded bg-[var(--sub-table-color)]"></div>
                </div>
            </div>
        @endforeach

        <div class="space-y-2 pt-6">
            <div class="h-3 w-2/3 rounded bg-[var(--sub-table-color)]"></div>
            <div class="h-3 w-1/2 rounded bg-[var(--sub-table-color)]"></div>
            <div class="h-3 w-1/4 rounded bg-[var(--sub-table-color)]"></div>
        </div>
    </div>

    {{-- Footer Actions --}}
    <div class="pt-4 border-t border-[var(--border-table-color)] flex justify-end gap-3">
        <div class="h-9 w-24 rounded-lg bg-[var(--main-table-color)]"></div>
        <div class="h-9 w-24 rounded-lg bg-[var(--focus-color)] opacity-40"></div>
    </div>

</div>
