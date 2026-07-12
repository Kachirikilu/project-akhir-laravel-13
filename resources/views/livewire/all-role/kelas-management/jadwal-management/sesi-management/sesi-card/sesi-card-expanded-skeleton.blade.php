<div class="flex flex-col gap-2 animate-pulse">

    {{-- Sub CPMK --}}
    <div
        class="rounded-[10px] border border-[var(--border-table-color)]
               bg-[var(--second-table-color)] px-4 py-3 flex flex-col gap-3">

        <div class="flex items-center justify-between">
            <div class="h-3 w-20 rounded bg-[var(--main-table-color)]"></div>

            <div
                class="h-6 w-20 rounded-full bg-[var(--focus-color)] opacity-30">
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="h-3 w-24 rounded bg-[var(--sub-table-color)]"></div>
            <div class="h-3 w-14 rounded bg-[var(--main-table-color)]"></div>
        </div>

        <div class="flex items-center justify-between">
            <div class="h-3 w-20 rounded bg-[var(--sub-table-color)]"></div>
            <div class="h-3 w-14 rounded bg-[var(--main-table-color)]"></div>
        </div>
    </div>

    {{-- Deskripsi --}}
    <div
        class="rounded-[10px] border border-[var(--border-table-color)]
               bg-[var(--second-table-color)] px-4 py-3 space-y-3">

        <div class="h-3 w-36 rounded bg-[var(--main-table-color)]"></div>

        <div class="space-y-2">
            <div class="h-3 w-full rounded bg-[var(--sub-table-color)]"></div>
            <div class="h-3 w-11/12 rounded bg-[var(--sub-table-color)]"></div>
            <div class="h-3 w-8/12 rounded bg-[var(--sub-table-color)]"></div>
        </div>
    </div>

    {{-- Referensi --}}
    <div
        class="rounded-[10px] border border-[var(--border-table-color)]
               bg-[var(--second-table-color)] px-4 py-3 space-y-3">

        <div class="h-3 w-20 rounded bg-[var(--main-table-color)]"></div>

        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-[var(--main-table-color)]"></div>
                <div class="h-3 flex-1 rounded bg-[var(--sub-table-color)]"></div>
                <div class="w-4 h-4 rounded bg-[var(--focus-color)] opacity-30"></div>
            </div>

            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-[var(--main-table-color)]"></div>
                <div class="h-3 w-10/12 rounded bg-[var(--sub-table-color)]"></div>
            </div>
        </div>
    </div>

    {{-- Dosen --}}
    <div
        class="rounded-[10px] border border-[var(--border-table-color)]
               bg-[var(--second-table-color)] px-4 py-3 space-y-3">

        <div class="h-3 w-28 rounded bg-[var(--main-table-color)]"></div>

        <div class="space-y-3">

            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-[var(--main-table-color)]"></div>

                    <div class="h-3 w-40 rounded bg-[var(--sub-table-color)]"></div>

                    <div
                        class="h-4 w-14 rounded-full bg-blue-400/30">
                    </div>
                </div>

                <div class="h-3 w-32 ml-5 rounded bg-[var(--sub-table-color)]"></div>
            </div>

            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-[var(--main-table-color)]"></div>

                    <div class="h-3 w-36 rounded bg-[var(--sub-table-color)]"></div>
                </div>

                <div class="h-3 w-28 ml-5 rounded bg-[var(--sub-table-color)]"></div>
            </div>

        </div>
    </div>

    {{-- KHUSUS MAHASISWA --}}
    @auth
        @if (Auth::user()->mahasiswa)

            {{-- Tombol absensi --}}
            <div
                class="rounded-xl h-9 w-full
                       bg-[var(--focus-color)] opacity-25">
            </div>

            {{-- Status absensi --}}
            <div
                class="rounded-[10px] border border-[var(--border-table-color)]
                       bg-[var(--second-table-color)] px-3 py-3 flex items-center justify-between">

                <div
                    class="h-6 w-24 rounded-full
                           bg-[var(--focus-color)] opacity-30">
                </div>

                <div class="h-3 w-20 rounded bg-[var(--sub-table-color)]"></div>
            </div>

            {{-- Keterangan --}}
            <div
                class="rounded-[10px] border border-[var(--border-table-color)]
                       bg-[var(--second-table-color)] px-3 py-3 space-y-2">

                <div class="h-3 w-20 rounded bg-[var(--main-table-color)]"></div>

                <div class="space-y-2">
                    <div class="h-3 w-full rounded bg-[var(--sub-table-color)]"></div>
                    <div class="h-3 w-9/12 rounded bg-[var(--sub-table-color)]"></div>
                </div>
            </div>

        @endif
    @endauth

</div>