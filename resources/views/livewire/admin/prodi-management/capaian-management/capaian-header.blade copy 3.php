    @php
        $colorClass = 'text-zinc-500';
        switch ($prodi->akreditas_pr) {
            case 'A':
                $colorClass = 'text-cyan-500 dark:text-cyan-400';
                break;
            case 'A-':
                $colorClass = 'text-green-500 dark:text-green-400';
                break;
            case 'B+':
                $colorClass = 'text-emerald-500 dark:text-emerald-400';
                break;
            case 'B':
                $colorClass = 'text-yellow-500 dark:text-yellow-400';
                break;
            case 'B-':
                $colorClass = 'text-amber-500 dark:text-amber-400';
                break;
            case 'C+':
            case 'C':
                $colorClass = 'text-orange-500 dark:text-orange-400';
                break;
            case 'D':
            case 'E':
                $colorClass = 'text-red-500 dark:text-red-400';
                break;
        }
    @endphp

<div
    class="md:px-6 lg:px-8 xl:px-12 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-9 bg-[var(--main-pop-up-color)] p-6 rounded-xl border table-border shadow-sm">

    {{-- 1. RATA-RATA NILAI CPL --}}
    <div class="flex flex-col gap-1">
        <span class="text-xs sm:text-sm uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
            Rata-rata CPL
        </span>
        <div class="flex items-baseline gap-1">
            <span class="mr-2 text-2xl font-black text-[var(--focus-color)] leading-none">
                {{ $prodi->rekap_pr ?? 0 }}
            </span>
            <span class="text-xs sm:text-sm font-bold uppercase tracking-wider text-[var(--contrast-main-text)] opacity-50">
                Skor
            </span>
        </div>
        <span class="text-xs sm:text-sm text-emerald-500 flex items-center gap-1.5 mt-0.5">
            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-emerald-500/10 text-emerald-500">
                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </span>
            Capaian Pembelajaran Lulusan
        </span>
    </div>

    {{-- 2. JUMLAH MATA KULIAH --}}
    <div class="flex flex-col gap-1">
        <span class="text-xs sm:text-sm uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
            Mata Kuliah / RPS Aktif
        </span>
        <span class="text-md sm:text-lg font-semibold text-[var(--contrast-second-text)]">
            {{ $prodi->count_mk ?? 0 }} <span class="ml-2 text-xs sm:text-sm font-normal opacity-70">MK</span>
            <span class="mx-1 text-sm sm:text-md font-normal opacity-70">/</span>
            {{ $prodi->count_rps_aktif ?? 0 }} <span class="ml-2 text-xs sm:text-sm font-normal opacity-70">RPS</span>
        </span>
        <span class="text-xs sm:text-sm text-[var(--contrast-main-text)] opacity-70">
            Dikelola Program Studi
        </span>
    </div>

    {{-- 3. INDEKS KINERJA PRODI --}}
    <div class="flex flex-col gap-1">
        <span class="text-xs sm:text-sm uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
            Indeks Kinerja (IK)
        </span>
        <span class="text-md sm:text-lg font-semibold text-[var(--contrast-second-text)]">
            {{ $prodi->index_pr }}<span class="text-xs sm:text-sm font-normal opacity-70"> / 4.00</span>
        </span>
        <span class="text-xs sm:text-sm text-[var(--contrast-main-text)] opacity-70">
            Rata-rata Indeks Prestasi Prodi
        </span>
    </div>

    {{-- 4. AKREDITASI --}}
    <div class="flex flex-col gap-1">
        <span class="text-xs sm:text-sm uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
            Akreditasi Prodi
        </span>
        <span class="my-1 text-md sm:text-lg font-black leading-none {{ $colorClass }}">
            {{ $prodi->akreditas_pr ?? 'E' }}
        </span>
        <span class="text-xs sm:text-sm text-[var(--contrast-main-text)] opacity-70">
            Status Mutu Akademik
        </span>
    </div>

</div>