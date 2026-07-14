<style>
    /* ===== Reset dasar (pengganti Tailwind Preflight) =====
       Tanpa Tailwind, browser memakai default UA stylesheet:
       - box-sizing default browser = content-box, sedangkan Tailwind pakai border-box.
         Ini FATAL untuk layout ini karena banyak elemen punya width tetap + padding/border
         (kolom sumbu-Y, kotak bar, kotak legenda) -> tanpa border-box, ukurannya
         membengkak dan bikin bar chart & legenda tabrakan/tidak presisi.
       - <h3>/<p> punya margin & font-size bawaan browser yang berbeda dari Tailwind
         (Tailwind menormalkannya ke margin:0, font-size:inherit).
       Reset ini menyamakan kondisi supaya nilai pixel yang sudah dihitung manual
       di bawah tetap akurat. */
    *, *::before, *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    h1, h2, h3, h4, h5, h6, p {
        font-size: inherit;
        font-weight: inherit;
        line-height: inherit;
    }
    img {
        display: block;
        max-width: 100%;
    }
    ul, ol {
        list-style: none;
    }

    @page {
        size: A4 landscape;
    }

    /* ===== Generic helpers ===== */
    .w-full { width: 100%; }
    .relative { position: relative; }
    .absolute { position: absolute; }
    .fixed { position: fixed; }
    .block { display: block; }
    .text-black { color: #000; }
    .bg-white { background-color: #fff; }
    .rounded { border-radius: 4px; }
    .rounded-sm { border-radius: 2px; }
    .rounded-lg { border-radius: 8px; }
    .shrink-0 { flex-shrink: 0; }
    .font-bold { font-weight: bold; }
    .font-semibold { font-weight: 600; }
    .font-medium { font-weight: 500; }
    .leading-none { line-height: 1; }
    .uppercase { text-transform: uppercase; }
    .whitespace-nowrap { white-space: nowrap; }
    .tracking-wide { letter-spacing: 0.025em; }
    .tracking-wider { letter-spacing: 0.05em; }
    .text-right { text-align: right; }
    .text-gray-400 { color: #9ca3af; }
    .text-gray-500 { color: #6b7280; }
    .text-gray-600 { color: #4b5563; }
    .text-gray-700 { color: #374151; }
    .text-gray-800 { color: #1f2937; }

    /* dark mode: mengikuti konvensi project (ancestor class ".dark") */
    .dark .dark-text-zinc-100 { color: #f4f4f5; }
    .dark .dark-text-zinc-200 { color: #e4e4e7; }
    .dark .dark-border-zinc-800 { border-color: #27272a; }
    .dark .dark-bg-zinc-950-50 { background-color: rgba(9, 9, 11, 0.5); }

    /* ===== Footer logo (fixed, terulang tiap halaman) ===== */
    .pdf-footer-logo {
        bottom: 0.5cm;
        right: 0.5cm;
        z-index: 999;
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .pdf-footer-logo-text {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        line-height: 1;
        gap: 4px;
    }
    .pdf-footer-prodi {
        font-size: 8px;
        font-weight: 600;
        color: #6b7280;
    }
    .pdf-footer-univ {
        font-size: 8px;
        font-weight: 600;
        color: #9ca3af;
    }
    .pdf-footer-logo img {
        height: 36px;
        object-fit: contain;
    }

    /* ===== Root wrapper ===== */
    .rps-pdf {
        background-color: #fff;
    }

    .page-container {
        width: 100%;
    }

    /* ===== Header baris judul + info kelas ===== */
    .chart-header-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        border-bottom: 1px solid #f3f4f6;
        padding: 16px 20px;
        position: relative;
    }
    .chart-header-title {
        font-size: 14px;
        line-height: 20px;
        font-weight: bold;
        color: #1f2937;
        letter-spacing: 0.025em;
    }
    .chart-header-subtitle {
        font-size: 11px;
        color: #4b5563;
        margin-top: 2px;
    }
    .chart-header-kelas {
        display: block;
        font-size: 11px;
        font-weight: bold;
        color: #1f2937;
    }
    .chart-header-meta {
        display: block;
        font-size: 10px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .chart-body { padding-top: 24px; }

    /* ===== Pembungkus grafik ===== */
    .chart-wrap {
        position: relative;
        height: 320px;
        padding-top: 68px;
    }

    .chart-margin-lg { margin-top: 80px; } /* 13-18 CPMK */
    .chart-margin-md { margin-top: 40px; } /* 7-12 CPMK */
    .chart-margin-none { margin-top: 0; } /* 1-6 CPMK */

    /* ===== Legenda CPMK ===== */
    .cpmk-legend-box {
        position: absolute;
        top: 0;
        right: 0;
        background-color: #f9fafb;
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid #f3f4f6;
        z-index: 30;
    }
    .cpmk-legend-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        column-gap: 24px;
        row-gap: 16px;
    }
    .cpmk-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 120px;
    }
    .cpmk-legend-swatch {
        width: 12px;
        height: 12px;
        border-radius: 2px;
        flex-shrink: 0;
    }
    .cpmk-legend-text {
        display: flex;
        flex-direction: column;
    }
    .cpmk-legend-kode {
        font-size: 9px;
        font-weight: bold;
        color: #374151;
        line-height: 1;
    }
    .cpmk-legend-detail {
        font-size: 7px;
        color: #6b7280;
        font-weight: 500;
        line-height: 1;
        margin-top: 2px;
    }

    /* ===== Baris chart (sumbu-Y + area bar) ===== */
    .chart-row {
        display: flex;
        height: 100%;
    }

    .chart-yaxis {
        position: relative;
        flex-shrink: 0;
        width: 20px;
        height: 100%;
    }
    .chart-yaxis-label {
        position: absolute;
        right: 4px;
        font-size: 7px;
        color: #9ca3af;
        line-height: 1;
    }

    .chart-area-outer {
        position: relative;
        flex: 1;
        height: 100%;
    }

    .chart-area-inner {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: flex-start;
        height: 100%;
        border-bottom: 1px solid #d1d5db;
        border-left: 1px solid #d1d5db;
        gap: 32px;
        padding-left: 12px;
        padding-right: 8px;
    }

    .chart-gridline {
        position: absolute;
        left: 0;
        width: 100%;
        border-top: 1px solid #f3f4f6;
        z-index: 0;
    }

    /* ===== Garis Batas Kelulusan 70% ===== */
    .chart-threshold-wrap {
        position: absolute;
        left: 0;
        width: 100%;
        z-index: 50;
    }
    .chart-threshold-line {
        width: 100%;
        border-top: 2px dashed #ef4444;
    }
    .chart-threshold-label {
        position: absolute;
        right: 0;
        top: -14px;
        font-size: 9px;
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 4px;
        background-color: #fff;
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    /* ===== Bar per mahasiswa ===== */
    .chart-bar-group {
        position: relative;
        display: flex;
        align-items: flex-end;
        gap: 2px;
        height: 100%;
        justify-content: center;
        flex-shrink: 0;
        z-index: 20;
    }
    .chart-bar-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        height: 100%;
    }
    .chart-bar-value {
        font-size: 7px;
        font-weight: bold;
        margin-bottom: 2px;
        line-height: 1;
        white-space: nowrap;
    }
    .chart-bar {
        border-top-left-radius: 2px;
        border-top-right-radius: 2px;
    }

    /* ===== Label NIM ===== */
    .chart-nim-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        gap: 32px;
        margin-top: 8px;
        padding-left: 44px;
    }
    .chart-nim-box {
        display: flex;
        justify-content: center;
        flex-shrink: 0;
    }
    .chart-nim-text {
        font-size: 8px;
        color: #000;
        display: block;
        transform: rotate(45deg);
        transform-origin: top left;
    }
</style>

@php
    $allMapping = collect($mapping_pertemuan ?? [])->values();
    $globalTotalBobotMentah = collect($groupsCpmk)->map(fn($p) => collect($p)->sum('bobot'))->sum() ?: 1;
    $daftarCpmk = collect($groupsCpmk)->keys()->toArray();

    $seriesData = [];
    foreach ($daftarCpmk as $kode) {
        $seriesData[$kode] = [
            'name' => $kode,
            'data' => [],
        ];
    }
    $daftarNim = [];

    foreach ($users as $user) {
        $daftarNim[] = $user->identity1;

        $arrayNilai = is_array($user->mhs_nilai_array)
            ? $user->mhs_nilai_array
            : json_decode($user->mhs_nilai_array ?? '[]', true);
        $bobotCpmkArray = is_array($user->mhs_bobot_array)
            ? $user->mhs_bobot_array
            : json_decode($user->mhs_bobot_array ?? '[]', true);

        foreach ($groupsCpmk as $kodeCpmk => $pertemuans) {
            $skorMurniCpmk = 0;

            $bobotMentahCpmkIni = collect($pertemuans)->sum('bobot');
            $bobotNormalisasiGlobalCpmk = ($bobotMentahCpmkIni / $globalTotalBobotMentah) * 100;

            foreach ($pertemuans as $pertemuan) {
                $originalIndex = $allMapping->search(
                    fn($item) => $item['kode_scpmk'] === $pertemuan['kode_scpmk'] &&
                        $item['kode_cpmk'] === $pertemuan['kode_cpmk'],
                );

                $nilaiPertemuan = $arrayNilai[$originalIndex] ?? 0;
                $rasioBobotDiCpmk = $bobotCpmkArray[$originalIndex] ?? 0;
                $skorMurniCpmk += $nilaiPertemuan * $rasioBobotDiCpmk;
            }
            $totalNilaiKontribusiCpmk = ($skorMurniCpmk / $bobotNormalisasiGlobalCpmk) * 100;
            $seriesData[$kodeCpmk]['data'][] = round($totalNilaiKontribusiCpmk, 1);
        }
    }

    $bobotCpmkLegend = [];
    $rataKeberhasilanCpmk = [];
    foreach ($groupsCpmk as $kodeCpmk => $pertemuans) {
        $bobotMentah = collect($pertemuans)->sum('bobot');
        $bobotNormalisasi = ($bobotMentah / $globalTotalBobotMentah) * 100;
        $bobotCpmkLegend[$kodeCpmk] = number_format($bobotNormalisasi, 2, '.', '');

        $semuaNilaiCpmk = $seriesData[$kodeCpmk]['data'];
        $totalMahasiswa = count($semuaNilaiCpmk);
        $rataKeberhasilanCpmk[$kodeCpmk] =
            $totalMahasiswa > 0 ? round(array_sum($semuaNilaiCpmk) / $totalMahasiswa, 1) : 0;
    }

    $finalSeries = array_values($seriesData);
    $colorPalette = [
        '#3b82f6', // Blue-500
        '#8b5cf6', // Violet-500
        '#ef4444', // Red-500
        '#f59e0b', // Amber-500
        '#10b981', // Emerald-500
        '#ec4899', // Pink-500
        '#14b8a6', // Teal-500
        '#f97316', // Orange-500
        '#6366f1', // Indigo-500
        '#a855f7', // Purple-500
        '#d946ef', // Fuchsia-500
        '#84cc16', // Lime-500
        '#06b6d4', // Cyan-500
        '#e11d48', // Rose-600
        '#facc15', // Yellow-400
        '#64748b', // Slate-500
    ];
    $totalMahasiswa = count($daftarNim);
    $calculatedWidth = max($totalMahasiswa * 160, 800);
@endphp

{{-- Logo + nama universitas, fixed di pojok kanan-bawah, terulang tiap halaman --}}
<div class="fixed pdf-footer-logo">
    <div class="pdf-footer-logo-text">
        <span class="pdf-footer-prodi">
            @if ($jadwal ?? false)
                {{ $jadwal->kelas_rel->pr_rel->prodi_pr ?? null }}
            @elseif ($pr_name ?? false)
                Program Studi {{ $pr_name ?? null }}
            @elseif ($dp_name ?? false)
                {{ $dp_name ?? null }}
            @elseif ($fk_name ?? false)
                {{ $fk_name ?? null }}
            @endif
        </span>
        <span class="pdf-footer-univ">
            {{ $univ }}
        </span>

    </div>

    @if ($logoBase64 ?? null)
        <img src="{{ $logoBase64 }}">
    @else
        <img src="{{ asset('images/logo-unsri.webp') }}">
    @endif
</div>

@yield('content')
<div class="rps-pdf bg-white">
    @php
        $jumlahSeries = count($finalSeries);
        $barGroupWidth = $jumlahSeries * 20 + max($jumlahSeries - 1, 0) * 2;
    @endphp

    <div class="w-full">
        @foreach ($chunk_users as $chunk_user)
            <div class="page-container w-full" style="height: 10vh;">

                <div class="chart-header-row">
                    <div>
                        <h3 class="chart-header-title">
                            Distribusi Capaian Nilai per Mahasiswa {{ $angkatan }}
                        </h3>
                        <p class="chart-header-subtitle">Mata Kuliah {{ $rps->mk_rel->nama_mk }}
                            | Semester {{ $rps->mk_rel->semester }}
                            | {{ $rps->mk_rel->sks }} SKS
                            | {{ $rps->mk_rel->sks_text }}
                        </p>
                    </div>

                    @if ($jadwal)
                        <div class="text-right">
                            <span class="chart-header-kelas">
                                {{ $jadwal->kode ?? 'Kelas Tidak Diketahui' }} {{ $tahun_akademik }}
                            </span>
                            <span class="chart-header-meta">
                                Kode RPS: {{ $rps->kode ?? '---- -- ----' }}
                            </span>
                            <span class="chart-header-meta">
                                {{ $jadwal->kelas_rel->pr_rel->prodi ?? '-- -----' }}
                            </span>
                        </div>
                    @else
                        <div class="text-right">
                            <span class="chart-header-kelas">
                                Kode RPS: {{ $rps->kode ?? '---- -- ----' }}
                            </span>

                            @php
                                $segments = array_filter([$pr_name, $dp_name]);
                            @endphp

                            @if (!empty($segments))
                                <span class="chart-header-meta">
                                    {{ implode(' | ', $segments) }}
                                </span>
                            @endif

                            @if ($fk_name)
                                <span class="chart-header-meta">
                                    {{ $fk_name }}
                                </span>
                            @endif
                        </div>
                    @endif

                </div>

                <div class="chart-body">

                    {{-- PEMBUNGKUS GRAFIK --}}
                    <div class="chart-wrap">

                        @php
                            $count = count($daftarCpmk);
                            $marginClass = match (true) {
                                $count >= 13 => 'chart-margin-lg', // 13-18
                                $count >= 7 => 'chart-margin-md', // 7-12
                                default => 'chart-margin-none', // 1-6
                            };
                        @endphp

                        <div class="cpmk-legend-box">
                            <div class="cpmk-legend-grid">
                                @foreach ($daftarCpmk as $index => $kodeCpmk)
                                    {{-- Isi foreach Anda ... --}}
                                    <div class="cpmk-legend-item">
                                        <div class="cpmk-legend-swatch"
                                            style="background-color: {{ $colorPalette[$index % count($colorPalette)] }};">
                                        </div>
                                        <div class="cpmk-legend-text">
                                            <span class="cpmk-legend-kode">{{ $kodeCpmk }}</span>
                                            <span class="cpmk-legend-detail">Bobot:
                                                {{ $bobotCpmkLegend[$kodeCpmk] ?? 0 }}% | Tercapai:
                                                {{ $rataKeberhasilanCpmk[$kodeCpmk] ?? 0 }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Div bawah dengan margin dinamis --}}
                        <div class="chart-row {{ $marginClass }}">

                            {{-- Kolom sumbu-Y --}}
                            <div class="chart-yaxis">
                                @for ($i = 0; $i <= 100; $i += 10)
                                    <span class="chart-yaxis-label"
                                        style="bottom: {{ $i }}%; transform: translateY(50%);">
                                        {{ $i }}
                                    </span>
                                @endfor
                            </div>

                            {{-- Wrapper area chart — TANPA padding di sini --}}
                            <div class="chart-area-outer">

                                {{-- Border + padding sekarang menyatu di div ini, border tidak ikut geser --}}
                                <div class="chart-area-inner">

                                    {{-- Garis kisi per 10% --}}
                                    @for ($i = 10; $i <= 100; $i += 10)
                                        <div class="chart-gridline" style="bottom: {{ $i }}%;"></div>
                                    @endfor

                                    {{-- Garis Batas Kelulusan 70% --}}
                                    <div class="chart-threshold-wrap" style="bottom: 70%;">
                                        <div class="chart-threshold-line">
                                        </div>
                                        <span class="chart-threshold-label">
                                            Batas Kelulusan 70%
                                        </span>
                                    </div>

                                    {{-- Bar per mahasiswa --}}
                                    @foreach ($chunk_user as $user)
                                        @php
                                            $nim = $user->identity1;
                                            $idxGlobal = array_search($nim, $daftarNim);
                                        @endphp

                                        <div class="chart-bar-group">
                                            @foreach ($finalSeries as $idxSeries => $series)
                                                @php
                                                    $value = $series['data'][$idxGlobal] ?? 0;
                                                    $color = $colorPalette[$idxSeries % count($colorPalette)];
                                                @endphp
                                                <div class="chart-bar-col">
                                                    <span class="chart-bar-value"
                                                        style="color: {{ $color }};">
                                                        {{ $value }}
                                                    </span>
                                                    <div class="chart-bar"
                                                        style="height: {{ $value }}%; background-color: {{ $color }}; width: 20px; min-height: 2px;">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- LABEL NIM — offset = 28px (sumbu-Y) + 24px (pl-6), lebar kotak = $barGroupWidth (samakan dgn bar) --}}
                    <div class="chart-nim-row">
                        @foreach ($chunk_user as $user)
                            <div class="chart-nim-box" style="width: {{ $barGroupWidth }}px;">
                                <span class="chart-nim-text" style="width: 60px;">
                                    {{ $user->identity1 }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>