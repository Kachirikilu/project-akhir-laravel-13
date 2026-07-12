<div class="flex flex-col h-full animate-pulse">
    {{-- Header --}}
    <div class="px-6 py-6 border-b border-[var(--border-table-color)]">
        <div class="h-8 w-56 rounded-lg bg-[var(--main-table-color)]"></div>
    </div>

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto p-6 space-y-8 scrollbar-large">
        {{-- Judul Section --}}
        <div class="space-y-3">
            <div class="h-6 w-72 rounded bg-[var(--main-table-color)]"></div>
            <div class="h-px w-full bg-[var(--border-table-color)]"></div>
        </div>

        {{-- Textarea --}}
        <div class="space-y-2">
            <div class="h-4 w-40 rounded bg-[var(--main-table-color)]"></div>
            <div class="rounded-xl border border-[var(--border-table-color)] bg-[var(--second-table-color)] p-4 space-y-3">
                @foreach(['w-full', 'w-11/12', 'w-10/12', 'w-8/12'] as $width)
                    <div class="h-3 {{ $width }} rounded bg-[var(--sub-table-color)]"></div>
                @endforeach
                <div class="h-20"></div>
            </div>
        </div>

        {{-- Repeater Input Group --}}
        @foreach(range(1, 3) as $i)
            <div class="space-y-2">
                <div class="h-4 w-32 rounded bg-[var(--main-table-color)]"></div>
                <div class="flex items-center gap-3 rounded-xl border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-4 py-3">
                    <div class="w-5 h-5 rounded-full bg-[var(--main-table-color)]"></div>
                    <div class="flex-1 h-4 rounded bg-[var(--sub-table-color)]"></div>
                </div>
            </div>
        @endforeach

        {{-- Area pesan --}}
        <div class="space-y-2 pt-6">
            <div class="h-3 w-2/3 rounded bg-[var(--sub-table-color)]"></div>
            <div class="h-3 w-1/2 rounded bg-[var(--sub-table-color)]"></div>
            <div class="h-3 w-1/4 rounded bg-[var(--sub-table-color)]"></div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="border-t border-[var(--border-table-color)] p-6 flex justify-end gap-3">
        <div class="h-10 w-28 rounded-lg bg-[var(--main-table-color)]"></div>
        <div class="h-10 w-36 rounded-lg bg-[var(--focus-color)] opacity-40"></div>
    </div>
</div>