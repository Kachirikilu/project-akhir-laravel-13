<div class="animate-pulse flex flex-col gap-5">
    
    {{-- Header Skeleton --}}
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[var(--main-table-color)]"></div>
        <div class="space-y-2">
            <div class="h-5 w-40 rounded bg-[var(--main-table-color)]"></div>
            <div class="h-3 w-32 rounded bg-[var(--sub-table-color)]"></div>
        </div>
    </div>

    {{-- Status Kehadiran Skeleton --}}
    <div class="space-y-3">
        <div class="h-4 w-32 rounded bg-[var(--main-table-color)]"></div>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            @foreach(range(1, 6) as $i)
                <div class="h-20 rounded-[12px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] flex flex-col items-center justify-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-[var(--sub-table-color)]"></div>
                    <div class="h-2 w-16 rounded bg-[var(--sub-table-color)]"></div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Divider --}}
    <div class="flex items-center gap-3 mt-2">
        <div class="h-px flex-1 bg-[var(--border-table-color)]"></div>
        <div class="h-3 w-32 rounded bg-[var(--sub-table-color)]"></div>
        <div class="h-px flex-1 bg-[var(--border-table-color)]"></div>
    </div>

    {{-- Keterangan Input Skeleton --}}
    <div class="h-14 w-full rounded-[12px] border border-[var(--border-table-color)] bg-[var(--sub-table-color)]"></div>

    {{-- Footer Actions Skeleton --}}
    <div class="flex items-center gap-2 pt-2">
        <div class="h-10 flex-1 rounded-lg bg-[var(--sub-table-color)]"></div>
        <div class="h-10 flex-1 rounded-lg bg-[var(--focus-color)] opacity-40"></div>
    </div>
</div>