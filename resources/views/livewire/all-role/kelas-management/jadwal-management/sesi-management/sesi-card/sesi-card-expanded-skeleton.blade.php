<div class="flex flex-col gap-2 animate-pulse">

    {{-- Sub CPMK --}}
    <div
        class="rounded-md border {{ $bgBorder }}
               {{ $secondTable }} px-4 py-3 flex flex-col gap-3">

        <div class="flex items-center justify-between">
            <div class="h-3 w-20 rounded {{ $mainTable }}"></div>

            <div
                class="h-6 w-20 rounded-lg {{ $focusColor }} opacity-30">
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="h-3 w-24 rounded {{ $subTable }}"></div>
            <div class="h-3 w-14 rounded {{ $mainTable }}"></div>
        </div>

        <div class="flex items-center justify-between">
            <div class="h-3 w-20 rounded {{ $subTable }}"></div>
            <div class="h-3 w-14 rounded {{ $mainTable }}"></div>
        </div>
    </div>

    {{-- Deskripsi --}}
    <div
        class="rounded-md border {{ $bgBorder }}
               {{ $secondTable }} px-4 py-3 space-y-3">

        <div class="h-3 w-36 rounded {{ $mainTable }}"></div>

        <div class="space-y-2">
            <div class="h-3 w-full rounded {{ $subTable }}"></div>
            <div class="h-3 w-11/12 rounded {{ $subTable }}"></div>
            <div class="h-3 w-8/12 rounded {{ $subTable }}"></div>
        </div>
    </div>

    {{-- Referensi --}}
    <div
        class="rounded-md border {{ $bgBorder }}
               {{ $secondTable }} px-4 py-3 space-y-3">

        <div class="h-3 w-20 rounded {{ $mainTable }}"></div>

        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full {{ $mainTable }}"></div>
                <div class="h-3 flex-1 rounded {{ $subTable }}"></div>
            </div>

            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full {{ $mainTable }}"></div>
                <div class="h-3 w-10/12 rounded {{ $subTable }}"></div>
            </div>
        </div>
    </div>

    {{-- Dosen --}}
    <div
        class="rounded-md border {{ $bgBorder }}
               {{ $secondTable }} px-4 py-3 space-y-3">

        <div class="h-3 w-28 rounded {{ $mainTable }}"></div>

        <div class="space-y-3">

            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full {{ $mainTable }}"></div>

                    <div class="h-3 w-40 rounded {{ $subTable }}"></div>

                    <div
                        class="h-4 w-14 rounded-full {{ $subTable }}">
                    </div>
                </div>

                <div class="h-3 w-32 ml-5 rounded-lg {{ $subTable }}"></div>
            </div>

            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full {{ $mainTable }}"></div>

                    <div class="h-3 w-36 rounded {{ $subTable }}"></div>
                </div>

                <div class="h-3 w-28 ml-5 rounded {{ $subTable }}"></div>
            </div>

        </div>
    </div>

    {{-- KHUSUS MAHASISWA --}}
    @auth
        @if (Auth::user()->mahasiswa)

            {{-- Tombol absensi --}}
            <div
                class="rounded-xl h-9 w-full
                       {{ $focusColor }} opacity-25">
            </div>

            {{-- Status absensi --}}
            <div
                class="rounded-md border {{ $bgBorder }}
                       {{ $secondTable }} px-3 py-3 flex items-center justify-between">

                <div
                    class="h-6 w-24 rounded-full
                           {{ $focusColor }} opacity-30">
                </div>

                <div class="h-3 w-20 rounded {{ $subTable }}"></div>
            </div>

            {{-- Keterangan --}}
            <div
                class="rounded-md border {{ $bgBorder }}
                       {{ $secondTable }} px-3 py-3 space-y-2">

                <div class="h-3 w-20 rounded {{ $mainTable }}"></div>

                <div class="space-y-2">
                    <div class="h-3 w-full rounded {{ $subTable }}"></div>
                    <div class="h-3 w-9/12 rounded {{ $subTable }}"></div>
                </div>
            </div>

        @endif
    @endauth

</div>