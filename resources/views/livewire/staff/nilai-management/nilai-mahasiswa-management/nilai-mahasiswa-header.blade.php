@php
    // $totalSks = $mahasiswa->total_sks;
    // $totalMk = $mahasiswa->count_rps;
    // $calculatedIndex = $mahasiswa->ipk_mhs ?? '0.00';
    // $mutuMhs = $mahasiswa->mutu_mhs ?? 'E';

    if ($alpine === 'nilai') {
        $safeNilais = collect($nilais ?? []);

        $nilaiUnik = $safeNilais->groupBy('rps_id')->map(function ($group) {
            return collect($group)->sortByDesc('nilai')->first();
        });

        $totalSks = $nilaiUnik->sum('sks');
        // $totalMk = $nilaiUnik->where('nilai', '>=', 50)->count();
        $totalMk = $nilaiUnik->count();

        $totalBobotSks = $nilaiUnik->sum(function ($n) {
            return ($n->sks ?? 0) * (float) ($n->nilai_index ?? 0);
        });

        $calculatedIndex = $totalSks > 0 ? $totalBobotSks / $totalSks : 0;
    } else {
        $safePeriodes = collect($periodes ?? []);
        $totalSks = $safePeriodes->sum('total_sks');
        $totalMk = $safePeriodes->sum('total_mk');;

        $calculatedIndex = (float) ($safePeriodes->avg('ip_semester') ?? 0);

        $totalBobotSks = $safePeriodes->sum(function ($n) {
            return ($n->total_sks ?? 0) * (float) ($n->ip_semester ?? 0);
        });
        $calculatedIndex = $totalSks > 0 ? $totalBobotSks / $totalSks : 0;
    }

    $mutuMhs = match (true) {
        $calculatedIndex >= 3.75 => 'A', 
        $calculatedIndex >= 3.5 => 'A-',
        $calculatedIndex >= 3.0 => 'B+',
        $calculatedIndex >= 2.75 => 'B',
        $calculatedIndex >= 2.0 => 'C',
        $calculatedIndex >= 1.0 => 'D',
        default => 'E',
    };
    // dump(collect($nilais ?? $periodes));

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
    <div
        class="flex flex-col lg:flex-col xl:flex-row xl:items-center xl:justify-between mb-2 lg:mb-6 gap-4 min-w-0 w-full">

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
                    class="mb-1 sm:mb-2 text-xl sm:text-2xl font-bold text-[var(--contrast-second-text)] flex flex-wrap items-center gap-4 min-w-0">
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
    {{--
    ============================================================
    GRID INFORMASI UTAMA MAHASISWA — Versi Donut Mini
    Kolom 1 & 3 pakai donut ring (ada rasio persentase).
    Kolom 2 & 4 pakai donut tanpa ring (hanya angka di tengah).
    ============================================================
--}}

    @php
        $isIps = ($alpine ?? '') === 'nilai';

        // SKS
        $targetSks = $mahasiswa->pr_rel->target_sks ?? 144;
        $sksValue = (int) ($totalSks ?? 0);
        $sksDisplay = $sksValue . ' / ' . $targetSks;
        $sksSub = $isIps ? 'Kredit terdaftar KHS' : 'Terakumulasi di transkrip';

        // MK
        $mkValue = (int) ($totalMk ?? 0);
        $mkSub = $isIps ? 'Registrasi KRS Semester ini' : 'Rencana Pembelajaran Semester';

        // IPK / IPS
        $ipValue = is_numeric($calculatedIndex) ? (float) $calculatedIndex : 0;
        $ipDisplay = is_numeric($calculatedIndex)
            ? number_format($calculatedIndex, 2) . ' / 4.00'
            : $calculatedIndex . ' / 4.00';
        $ipLabel = $isIps ? 'IP Semester (IPS)' : 'IPK Akumulatif';
        $ipSub = $isIps ? 'Indeks Prestasi Semester Aktif' : 'Skala Penilaian Kurikulum OBE';

        // Predikat Mutu — tidak ada rasio, tampilkan huruf di tengah
        $mutuDisplay = $mutuMhs ?? 'E';
        $mutuLabel = $isIps ? 'Mutu Semester' : 'Predikat Mutu';
        $mutuSub = 'Bobot Standar Akademik';
        // Warna accent predikat
        $mutuAccent = match (strtoupper($mutuDisplay)) {
            'A', 'A+' => '#10b981', // 4.0
            'A-', 'B+', 'B' => '#1d6fb8', // 3.0 - 3.7
            'B-', 'C+', 'C' => '#f59e0b', // 2.0 - 2.7
            'D' => '#ef4444', // 1.0 (D)
            default => '#991b1b', // E (Merah Gelap)
        };

        $mutuSoftBg =
            'rgba(' .
            implode(
                ',',
                match (strtoupper($mutuDisplay)) {
                    'A', 'A+' => [16, 185, 129],
                    'A-', 'B+', 'B' => [29, 111, 184],
                    'B-', 'C+', 'C' => [245, 158, 11],
                    'D' => [239, 68, 68],
                    default => [153, 27, 27],
                },
            ) .
            ',0.12)';
    @endphp

    <div
        class="md:px-6 lg:px-8 xl:px-12 grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-4 gap-y-9 bg-[var(--main-pop-up-color)]/90 p-6 rounded-xl border table-border shadow-sm">

        {{-- 1. SKS — ada rasio, donut penuh --}}
        @include('livewire.global.statistik.donut-mini-stats', [
            'icon' => 'scale',
            'title' => $isIps ? 'SKS Semester' : 'SKS Ditempuh',
            'sub' => $sksSub,
            'value' => $sksValue,
            'max' => $targetSks,
            'display' => $sksDisplay,
            'accent' => '#10b981',
            'softBg' => 'rgba(16,185,129,0.12)',
            'textColor' => '#10b981',
            'size' => 64,
            'pctSize' => 'text-sm',
        ])

        {{-- 2. MATA KULIAH — tidak ada rasio, angka saja di tengah --}}
        @include('livewire.global.statistik.donut-mini-stats', [
            'icon' => 'rectangle-stack',
            'title' => $isIps ? 'Mata Kuliah Diambil' : 'Mata Kuliah',
            'sub' => $mkSub,
            'mainValue' => $mkValue,
            'display' => 'Mata Kuliah',
            'accent' => 'var(--focus-color)',
            'softBg' => 'color-mix(in srgb, var(--focus-color) 14%, transparent)',
            'textColor' => 'var(--focus-color)',
            'size' => 64,
            'pctSize' => 'text-sm',
        ])

        {{-- 3. IPK / IPS — ada rasio /4.00 --}}
        @include('livewire.global.statistik.donut-mini-stats', [
            'icon' => 'trophy',
            'title' => $ipLabel,
            'sub' => $ipSub,
            'value' => $ipValue,
            'max' => 4,
            'display' => $ipDisplay,
            'accent' => 'var(--focus-color)',
            'softBg' => 'color-mix(in srgb, var(--focus-color) 14%, transparent)',
            'textColor' => 'var(--focus-color)',
            'size' => 64,
            'pctSize' => 'text-sm',
        ])

        {{-- 4. PREDIKAT MUTU — tidak ada rasio, huruf di tengah --}}
        @include('livewire.global.statistik.donut-mini-stats', [
            'icon' => 'academic-cap',
            'title' => $mutuLabel,
            'sub' => $mutuSub,
            'value' => $ipValue,
            'max' => 4,
            'display' => $mutuDisplay,
            'accent' => $mutuAccent,
            'softBg' => $mutuSoftBg,
            'textColor' => $mutuAccent,
            'size' => 64,
            'pctSize' => 'text-sm',
        ])

    </div>
</div>
