<div class="flex flex-col gap-5 animate-pulse">

    {{-- Header --}}
    <div class="flex items-center gap-2.5">
        <div class="h-10 w-10 rounded-xl bg-[var(--main-table-color)]"></div>

        <div class="flex-1 space-y-2">
            <div class="h-5 w-52 rounded bg-[var(--main-table-color)]"></div>
            <div class="h-3 w-72 rounded bg-[var(--sub-table-color)]"></div>
        </div>
    </div>

    {{-- Nomor WhatsApp --}}
    <div class="flex flex-col gap-2">
        <div class="h-3 w-40 rounded bg-[var(--main-table-color)]"></div>
        <div class="h-3 w-24 rounded bg-[var(--sub-table-color)]"></div>
        <div class="grid grid-cols-12 gap-2">
            <div class="col-span-3">
                <div class="h-11 rounded-xl bg-[var(--main-table-color)]"></div>
            </div>
            <div class="col-span-9">
                <div class="h-11 rounded-xl bg-[var(--main-table-color)]"></div>
            </div>
        </div>
        <div class="h-3 w-56 rounded bg-[var(--sub-table-color)]"></div>
    </div>

    {{-- Divider --}}
    <div class="flex items-center gap-3">
        <div class="flex-1 h-px bg-[var(--border-table-color)]"></div>
        <div class="h-3 w-40 rounded bg-[var(--main-table-color)]"></div>
        <div class="flex-1 h-px bg-[var(--border-table-color)]"></div>
    </div>

    {{-- Kontak --}}
    <div class="rounded-xl border border-[var(--border-table-color)] bg-[var(--sub-table-color)] p-3">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-[var(--main-table-color)]"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 w-36 rounded bg-[var(--main-table-color)]"></div>
                <div class="h-3 w-28 rounded bg-[var(--sub-table-color)]"></div>
            </div>
            <div class="h-6 w-20 rounded-full bg-[var(--main-table-color)]"></div>
        </div>
    </div>

    {{-- Pesan --}}
    <div class="flex flex-col gap-2">
        <div class="h-3 w-44 rounded bg-[var(--main-table-color)]"></div>
        <div
            class="flex items-center gap-3 rounded-xl border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-3 py-3">
            <div class="h-4 w-4 rounded-full bg-[var(--main-table-color)]"></div>
            <div class="h-4 w-48 rounded bg-[var(--main-table-color)]"></div>
        </div>
        <div class="h-3 w-64 rounded bg-[var(--sub-table-color)]"></div>
    </div>

    {{-- Footer --}}
    <div class="flex gap-2 pt-1">
        <div class="h-10 flex-1 rounded-xl bg-[var(--main-table-color)]"></div>
        <div class="h-10 flex-1 rounded-xl bg-[var(--focus-color)]/25"></div>
    </div>

</div>