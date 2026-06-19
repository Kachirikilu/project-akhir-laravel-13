@php
    // --- 1. DEKLARASI VARIABEL UTAMA DENGAN FALLBACK DEFAULT (MODE PERIODE) ---
    $totalSks = $mahasiswa->total_sks ?? ($user->total_sks ?? 0);
    $matakuliahLulusCount = $mahasiswa->count_rps ?? ($user->count_rps ?? 0);
    $calculatedIndex = $mahasiswa->ipk_mhs ?? '0.00';
    $mutuMhs = $mahasiswa->mutu_mhs ?? 'E';

    // --- 2. OVERWRITE JIKA MODE 'NILAI' (HITUNG DARI $nilais) ---
    if ($alpine === 'nilai') {
        $safeNilais = collect($nilais ?? []);

        // Menyaring rps_id unik nilai tertinggi untuk cakupan semester ini
        $nilaiUnik = $safeNilais->groupBy('rps_id')->map(function ($group) {
            return collect($group)->sortByDesc('nilai')->first();
        });

        $totalSks = $nilaiUnik->sum('sks');
        $matakuliahLulusCount = $nilaiUnik->where('nilai', '>=', 50)->count();

        // Hitung IPS (Indeks Prestasi Semester)
        $totalBobotSks = $nilaiUnik->sum(function ($n) {
            return ($n->sks ?? 0) * (float) ($n->nilai_index ?? 0);
        });
        $calculatedIndex = $totalSks > 0 ? $totalBobotSks / $totalSks : 0;

        // Tentukan Predikat Mutu Semester
        // Tentukan Predikat Mutu berdasarkan hasil kalkulasi index mhs (IPS / IPK)
        $mutuMhs = match (true) {
            $calculatedIndex >= 4.0 => 'A',
            $calculatedIndex >= 3.7 => 'A-',
            $calculatedIndex >= 3.3 => 'B+',
            $calculatedIndex >= 3.0 => 'B',
            $calculatedIndex >= 2.7 => 'B-',
            $calculatedIndex >= 2.3 => 'C+',
            $calculatedIndex >= 2.0 => 'C',
            $calculatedIndex >= 1.0 => 'D',
            default => 'E',
        };
    }

    // --- 3. LOGIKA WARNA BERDASARKAN MUTU AKTIF ---
    $colorClass = 'text-zinc-500';
    switch ($mutuMhs) {
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

    $ganjil_genap = $ganjil_genap_url ?? null;
    $akademik = $akademik_fix_url ?? null;
@endphp

{{-- Header Section --}}
<div class="mb-8">
    {{-- Container Utama --}}
    <div class="flex flex-col lg:flex-col xl:flex-row xl:items-center xl:justify-between mb-2 lg:mb-6 gap-4 min-w-0 w-full">

        {{-- Sisi Kiri: Profil Mahasiswa --}}
        <div class="flex items-center gap-2 sm:gap-4 min-w-0 w-full">
            @if (!($noBackUrl ?? false))
                <a href="{{ $backUrl ?? route('nilai-management') }}" wire:navigate
                    class="mx-1 px-2 py-2 sm:p-3 rounded-full hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors shrink-0 flex items-center justify-center">
                    <flux:icon name="arrow-left" class="h-5 w-5 sm:h-6 sm:w-6 text-[var(--contrast-second-text)]" />
                </a>
            @endif

            <div class="min-w-0 flex-1">
                <h2
                    class="mb-1 sm:mb-2 text-xl sm:text-2xl font-bold text-[var(--contrast-second-text)] flex flex-wrap items-center gap-2 min-w-0">
                    <span class="break-words">{{ $mahasiswa->name ?? 'Wildan Athif Muttaqien' }}</span>
                    @if ($ganjil_genap || $akademik)
                        <span
                            class="text-[10px] sm:text-xs font-semibold px-2 sm:px-4 py-0.5 sm:py-1 rounded-md bg-[var(--focus-color)]/10 text-[var(--focus-color)] border border-[var(--focus-color)]/20 whitespace-nowrap">
                            {{ ucfirst($ganjil_genap) }} {{ $akademik }}
                        </span>
                    @endif
                </h2>
                <p
                    class="text-[var(--contrast-main-text)] opacity-70 text-xs sm:text-sm flex items-center gap-x-1 gap-y-1 flex-wrap mt-0.5 min-w-0">
                    <span>NIM: {{ $nim_url ?? '03041282227063' }}</span>
                    <strong class="opacity-40 mx-1">|</strong>
                    <span>{{ $mahasiswa->pr_rel->prodi ?? '---' }} ({{ $mahasiswa->pr_rel->kode_dp ?? '---' }})</span>
                    <strong class="opacity-40 mx-1">|</strong>
                    <span>{{ $mahasiswa->pr_rel->fakultas_fk ?? '---' }}
                        ({{ $mahasiswa->pr_rel->kode_fk ?? '---' }})</span>
                </p>
            </div>
        </div>

        {{-- Sisi Kanan / Bawah: Grup Tombol dengan Trik Full CSS --}}
        {{-- Kombinasi flex-row-reverse, justify-start, dan xl:ml-auto menjamin posisi start dari kanan dan scrollbar aktif --}}
        <div
            class="flex flex-row-reverse items-center justify-start xl:ml-auto gap-3 w-full xl:w-auto overflow-x-auto scrollbar-tiny flex-nowrap shrink-0 pb-2 pr-2 pl-2 sm:pl-0">

            {{-- TOMBOL UTAMA (Excel) ditaruh paling atas karena urutan flex-row-reverse akan merendernya di posisi paling kanan layar --}}
            @if ($alpine == 'nilai')
                <div class="shrink-0 mt-1">
                    @include('livewire.global.table.export-button', [
                        'xString' => "exportNilaiMahasiswaExcel($mahasiswa->id, '$ganjil_genap', '$akademik')",
                        'nameXString' => "Rekap Nilai Mahasiswa $ganjil_genap $akademik",
                        'autoSmall' => 'sm',
                    ])
                </div>
            @else
                <div class="shrink-0 mt-1">
                    @include('livewire.global.table.export-button', [
                        'xString' => "exportNilaiMahasiswaExcel($mahasiswa->id)",
                        'nameXString' => 'Rekap Nilai Mahasiswa',
                        'autoSmall' => 'sm',
                    ])
                </div>
            @endif


            {{-- TOMBOL KEDUA (Capaian) ditaruh di bawahnya, otomatis akan berjejer di sebelah kirinya tombol Excel --}}
            @if (Auth::user()->admin || Auth::user()->dosen)
                <div class="shrink-0 mt-1">
                    @include('livewire.global.table.export-button', [
                        'nameXString' => 'Rekap Capaian ' . Auth::user()->kode_pr,
                        'xString' => 'generateRekapCapaian(' . Auth::user()->pr_id . ', 15)',
                        'color' => 'blue',
                        'icon' => 'academic-cap',
                    ])
                </div>
            @endif

        </div>
    </div>
    {{-- Grid Informasi Utama Mahasiswa --}}
    <div
        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-9 bg-[var(--second-pop-up-color)] p-6 rounded-xl border table-border shadow-sm">

        {{-- 1. SKS GRID --}}
        <div class="flex flex-col gap-1">
            <span
                class="text-xs sm:text-sm uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
                {{ $alpine === 'nilai' ? 'SKS Semester' : 'SKS Ditempuh' }}
            </span>
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-black text-[var(--focus-color)] leading-none">
                    {{ $totalSks }}
                </span>
                <span
                    class="text-xs sm:text-sm font-bold uppercase tracking-wider text-[var(--contrast-main-text)] opacity-50">
                    Kredit
                </span>
            </div>
            <span class="text-xs sm:text-sm text-emerald-500 flex items-center gap-1.5 mt-0.5">
                <span
                    class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-emerald-500/10 text-emerald-500">
                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
                {{ $alpine === 'nilai' ? 'Kredit Terdaftar KHS' : 'Terakumulasi di Transkrip' }}
            </span>
        </div>

        {{-- 2. MATA KULIAH LULUS --}}
        <div class="flex flex-col gap-1">
            <span
                class="text-xs sm:text-sm uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
                {{ $alpine === 'nilai' ? 'Mata Kuliah Diambil' : 'Mata Kuliah Lulus' }}
            </span>
            <span class="text-md sm:text-lg font-semibold text-[var(--contrast-second-text)]">
                {{ $matakuliahLulusCount }} <span class="text-xs sm:text-sm font-normal opacity-70">Mata Kuliah</span>
            </span>
            <span class="text-xs sm:text-sm text-[var(--contrast-main-text)] opacity-70">
                {{ $alpine === 'nilai' ? 'Registrasi KRS Semester Ini' : 'Rencana Pembelajaran Semester' }}
            </span>
        </div>

        {{-- 3. IPK / IP SEMESTER --}}
        <div class="flex flex-col gap-1">
            <span
                class="text-xs sm:text-sm uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
                {{ $alpine === 'nilai' ? 'IP Semester (IPS)' : 'IPK Akumulatif' }}
            </span>
            <span class="text-md sm:text-lg font-semibold text-[var(--contrast-second-text)]">
                {{ is_numeric($calculatedIndex) ? number_format($calculatedIndex, 2) : $calculatedIndex }}<span
                    class="text-xs sm:text-sm font-normal opacity-70">/ 4.00</span>
            </span>
            <span class="text-xs sm:text-sm text-[var(--contrast-main-text)] opacity-70">
                {{ $alpine === 'nilai' ? 'Indeks Prestasi Semester Aktif' : 'Skala Penilaian Kurikulum OBE' }}
            </span>
        </div>

        {{-- 4. PREDIKAT MUTU --}}
        <div class="flex flex-col gap-1">
            <span
                class="text-xs sm:text-sm uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">
                {{ $alpine === 'nilai' ? 'Mutu Semester' : 'Predikat Mutu' }}
            </span>
            <span class="text-md sm:text-lg font-black leading-none {{ $colorClass }}">
                {{ $mutuMhs }}
            </span>
            <span class="text-xs sm:text-sm text-[var(--contrast-main-text)] opacity-70">
                Bobot Standar Akademik
            </span>
        </div>

    </div>
</div>
