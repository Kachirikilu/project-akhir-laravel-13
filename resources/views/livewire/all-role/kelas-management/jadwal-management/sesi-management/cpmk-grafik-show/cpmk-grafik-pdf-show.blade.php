<style>
    @page {
        size: A4 landscape;
        margin: 1cm;
    }

    /* Reset dasar pengganti Tailwind Preflight (box-sizing border-box WAJIB
       karena banyak elemen pakai width/height tetap + padding/border sekaligus) */
    *,
    *::before,
    *::after {
        box-sizing: border-box;
        /* margin: 0;
        padding: 0; */
    }

    table {
        border-collapse: collapse;
        border-spacing: 0;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    p {
        font-size: inherit;
        font-weight: inherit;
        line-height: inherit;
    }

    img {
        display: block;
    }

    /* ===== Generic helpers ===== */
    .relative {
        position: relative;
    }

    .absolute {
        position: absolute;
    }

    .fixed {
        position: fixed;
    }

    .w-full {
        width: 100%;
    }

    .block {
        display: block;
    }

    .text-right {
        text-align: right;
    }

    .text-black {
        color: #000;
    }

    .bg-white {
        background-color: #fff;
    }

    .rounded {
        border-radius: 4px;
    }

    .rounded-sm {
        border-radius: 2px;
    }

    .rounded-lg {
        border-radius: 8px;
    }

    .rounded-t-sm {
        border-top-left-radius: 2px;
        border-top-right-radius: 2px;
    }

    .font-bold {
        font-weight: bold;
    }

    .font-semibold {
        font-weight: 600;
    }

    .font-medium {
        font-weight: 500;
    }

    .leading-none {
        line-height: 1;
    }

    .uppercase {
        text-transform: uppercase;
    }

    .whitespace-nowrap {
        white-space: nowrap;
    }

    .tracking-wide {
        letter-spacing: 0.025em;
    }

    .tracking-wider {
        letter-spacing: 0.05em;
    }

    .text-gray-400 {
        color: #9ca3af;
    }

    .text-gray-500 {
        color: #6b7280;
    }

    .text-gray-600 {
        color: #4b5563;
    }

    .text-gray-700 {
        color: #374151;
    }

    .text-gray-800 {
        color: #1f2937;
    }

    .z-30 {
        z-index: 30;
    }

    .z-50 {
        z-index: 50;
    }

    /* mt-* dipertahankan sebagai nama class (dipakai langsung dari $marginClass
       yang di-generate PHP: 'mt-20' / 'mt-10' / 'mt-0'), nilainya sesuai skala px Tailwind */
    .mt-20 {
        margin-top: 80px;
    }

    .mt-10 {
        margin-top: 40px;
    }

    .mt-0 {
        margin-top: 0;
    }

    /* ===== Footer logo (fixed, terulang tiap halaman) ===== */
    .pdf-footer-fixed {
        bottom: 32px;
        right: 32px;
        z-index: 999;
    }

    .pdf-footer-table td {
        vertical-align: middle;
    }

    .pdf-footer-text-cell {
        text-align: right;
        padding-right: 12px;
    }

    .pdf-footer-prodi {
        font-size: 8px;
        font-weight: 600;
        color: #6b7280;
        line-height: 1.4;
    }

    .pdf-footer-univ {
        font-size: 8px;
        font-weight: 600;
        color: #9ca3af;
        line-height: 1.4;
    }

    .pdf-footer-logo-cell img {
        height: 36px;
    }

    /* ===== Root wrapper ===== */
    .capaian-pdf {
        font-family: "Times New Roman", Times, serif;
    }

    /* .page-container {
        page-break-after: always;
    } */

    /* ===== Header ===== */
    .chart-header-wrap {
        border-bottom: 1px solid #f3f4f6;
    }

    .chart-header-table {
        width: 100%;
    }

    .chart-header-table td {
        vertical-align: top;
        padding: 16px 20px;
    }

    .chart-header-title {
        font-size: 14px;
    }

    .chart-header-subtitle {
        font-size: 11px;
    }

    .chart-header-kelas {
        font-size: 11px;
    }

    .chart-header-meta {
        font-size: 10px;
        margin-top: 2px;
    }

    .chart-body {
        padding-top: 24px;
    }

    /* Box pembungkus: rata kanan */
    .cpmk-legend-box {
        background-color: #f9fafb;
        padding: 0 24px;
        border: 1px solid #f3f4f6;
        text-align: right;
    }

    /* Item: berjejer kesamping */
    .cpmk-legend-item {
        display: inline-block;
        vertical-align: top;
        text-align: left;
        margin-left: 10px;
    }

    .cpmk-legend-table td {
        vertical-align: middle;
        padding: 0;
    }

    .cpmk-legend-swatch {
        width: 12px;
        height: 12px;
    }

    .cpmk-legend-swatch-cell {
        padding-right: 12px !important;
    }

    .cpmk-legend-kode {
        font-size: 9px;
    }

    .cpmk-legend-detail {
        font-size: 7px;
    }

    /* ===== Sumbu-Y + area chart (table, pengganti flex) ===== */
    .chart-outer-table {
        width: 100%;
    }

    .chart-yaxis-cell {
        vertical-align: bottom;
        width: 20px;
    }

    .chart-yaxis-label {
        right: 4px;
        font-size: 7px;
    }

    .chart-area-cell {
        vertical-align: bottom;
    }

    .chart-area-frame {
        border-bottom: 1px solid #d1d5db;
        border-left: 1px solid #d1d5db;
        padding: 0 8px 0 12px;
    }

    .chart-gridline {
        left: 0;
        width: 100%;
        border-top: 1px solid #f3f4f6;
        z-index: 0;
    }

    .chart-threshold-line {
        left: 0;
        width: 100%;
        border-top: 2px dashed #ef4444;
    }

    .chart-threshold-label {
        right: 0;
        top: -14px;
        font-size: 9px;
        padding: 2px 6px;
        background-color: #fff;
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    /* ===== Bar per mahasiswa (table, pengganti flex-wrap) ===== */
    .chart-bars-table {
        z-index: 20;
    }

    .chart-bar-group-cell {
        vertical-align: bottom;
        padding-right: 10px;
    }

    .chart-bar-inner-table td {
        vertical-align: bottom;
        text-align: center;
        padding: 0 1px;
    }

    .chart-bar-value {
        font-size: 7px;
        margin-bottom: 2px;
    }

    /* ===== Label NIM (table terpisah, tetap pakai $barGroupWidth) ===== */
    .chart-nim-table {
        margin-top: 8px;
    }

    .chart-nim-cell {
        text-align: center;
        vertical-align: top;
    }

    .chart-nim-text {
        display: inline-block;
        font-size: 8px;
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
<div class="fixed pdf-footer-fixed">
    <table class="pdf-footer-table">
        <tr>
            <td class="pdf-footer-text-cell">
                <div class="pdf-footer-prodi">
                    @if ($jadwal ?? false)
                        {{ $jadwal->kelas_rel->pr_rel->prodi_pr ?? null }}
                    @elseif ($pr_name ?? false)
                        Program Studi {{ $pr_name ?? null }}
                    @elseif ($dp_name ?? false)
                        {{ $dp_name ?? null }}
                    @elseif ($fk_name ?? false)
                        {{ $fk_name ?? null }}
                    @endif
                </div>
                <div class="pdf-footer-univ">
                    {{ $univ }}
                </div>
            </td>
            <td class="pdf-footer-logo-cell">
                @if ($logoBase64 ?? null)
                    <img src="{{ $logoBase64 }}">
                @else
                    <img src="{{ asset('images/logo-unsri.webp') }}">
                @endif
            </td>
        </tr>
    </table>
</div>

{{-- @yield('content') --}}
<div class="capaian-pdf bg-white">
    @php
        $jumlahSeries = count($finalSeries);
        $barGroupWidth = $jumlahSeries * 30 + max($jumlahSeries - 1, 0) * 2;
    @endphp

    <div class="w-full">
        @foreach ($chunk_users as $chunk_user)
            <div class="page-container w-full"
                style="{{ $loop->last ? 'page-break-after: auto;' : 'page-break-after: always;' }} break-inside: avoid;">

                <div class="chart-header-wrap">
                    <table class="chart-header-table">
                        <tr>
                            <td>
                                <h3 class="font-bold text-gray-800 tracking-wide chart-header-title">
                                    Distribusi Capaian Nilai per Mahasiswa {{ $angkatan }}
                                </h3>
                                <p class="text-gray-600 chart-header-subtitle">Mata Kuliah {{ $rps->mk_rel->nama_mk }}
                                    | Semester {{ $rps->mk_rel->semester }}
                                    | {{ $rps->mk_rel->sks }} SKS
                                    | {{ $rps->mk_rel->sks_text }}
                                </p>
                            </td>

                            @if ($jadwal)
                                <td class="text-right">
                                    <span class="block font-bold text-gray-800 chart-header-kelas">
                                        {{ $jadwal->kode ?? 'Kelas Tidak Diketahui' }} {{ $tahun_akademik }}
                                    </span>
                                    <span class="block text-gray-500 uppercase tracking-wider chart-header-meta">
                                        Kode RPS: {{ $rps->kode ?? '---- -- ----' }}
                                    </span>
                                    <span class="block text-gray-500 uppercase tracking-wider chart-header-meta">
                                        {{ $jadwal->kelas_rel->pr_rel->prodi ?? '-- -----' }}
                                    </span>
                                </td>
                            @else
                                <td class="text-right">
                                    <span class="block font-bold text-gray-800 chart-header-kelas">
                                        Kode RPS: {{ $rps->kode ?? '---- -- ----' }}
                                    </span>

                                    @php
                                        $segments = array_filter([$pr_name, $dp_name]);
                                    @endphp

                                    @if (!empty($segments))
                                        <span class="block text-gray-500 uppercase tracking-wider chart-header-meta">
                                            {{ implode(' | ', $segments) }}
                                        </span>
                                    @endif

                                    @if ($fk_name)
                                        <span class="block text-gray-500 uppercase tracking-wider chart-header-meta">
                                            {{ $fk_name }}
                                        </span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    </table>
                </div>

                <div class="chart-body">

                    {{-- PEMBUNGKUS GRAFIK --}}
                    <div class="relative" style="height: 360px; padding-top: 68px;">

                        {{-- Hapus left: 0 dan pastikan posisi merapat ke kanan --}}
                        <div class="absolute z-30 cpmk-legend-box" style="top:0; right:0;">
                            @foreach ($daftarCpmk as $index => $kodeCpmk)
                                <div class="cpmk-legend-item">
                                    <table class="cpmk-legend-table">
                                        <tr>
                                            <td class="cpmk-legend-swatch-cell">
                                                <div class="rounded-sm cpmk-legend-swatch"
                                                    style="background-color: {{ $colorPalette[$index % count($colorPalette)] }};">
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="font-bold text-gray-700 leading-none cpmk-legend-kode">{{ $kodeCpmk }}</span>
                                                <div class="text-gray-500 font-medium leading-none cpmk-legend-detail"
                                                    style="white-space: nowrap; margin-bottom: 7px;">
                                                    Bobot: {{ $bobotCpmkLegend[$kodeCpmk] ?? 0 }}% | Tercapai:
                                                    {{ $rataKeberhasilanCpmk[$kodeCpmk] ?? 0 }}%
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            @endforeach

                        </div>

                        {{-- Div bawah dengan margin dinamis --}}
                        <table class="chart-outer-table" style="transform: translateY(64px);">
                            <tr>
                                {{-- Kolom sumbu-Y --}}
                                <td class="chart-yaxis-cell" style="vertical-align: bottom;">
                                    <div class="relative" style="width: 20px; height: 252px;">
                                        @for ($i = 0; $i <= 100; $i += 10)
                                            <span class="absolute text-gray-400"
                                                style="bottom: {{ $i }}%; transform: translateY(50%); right: 5px; font-size: 10px;">
                                                {{ $i }}
                                            </span>
                                        @endfor
                                    </div>
                                </td>
                                {{-- Wrapper area chart — TANPA padding di sini --}}
                                <td class="chart-area-cell">
                                    {{-- Border + padding sekarang menyatu di div ini, border tidak ikut geser --}}
                                    <div class="relative chart-area-frame" style="height: 252px;">

                                        {{-- Garis kisi per 10% --}}
                                        @for ($i = 10; $i <= 100; $i += 10)
                                            <div class="absolute chart-gridline" style="bottom: {{ $i }}%;">
                                            </div>
                                        @endfor

                                        {{-- Garis Batas Kelulusan 70% --}}
                                        <div class="absolute z-50" style="left:0; width:100%; bottom: 70%;">
                                            <div class="chart-threshold-line" style="border-color: #ef4444;">
                                            </div>
                                            <span class="absolute font-bold rounded bg-white chart-threshold-label"
                                                style="color: #ef4444; border: 1px solid #ef4444;">
                                                Batas Kelulusan 70%
                                            </span>
                                        </div>

                                        {{-- Bar per mahasiswa --}}
                                        <table class="chart-bars-table">
                                            <tr>
                                                @foreach ($chunk_user as $user)
                                                    @php
                                                        $nim = $user->identity1;
                                                        $idxGlobal = array_search($nim, $daftarNim);
                                                    @endphp

                                                    <td class="chart-bar-group-cell">
                                                        <table class="chart-bar-inner-table">
                                                            {{-- Baris 1: Grafik Batang --}}
                                                            <tr>
                                                                @foreach ($finalSeries as $idxSeries => $series)
                                                                    @php
                                                                        $value = $series['data'][$idxGlobal] ?? 0;
                                                                        $color =
                                                                            $colorPalette[
                                                                                $idxSeries % count($colorPalette)
                                                                            ];
                                                                    @endphp
                                                                    <td
                                                                        style="height: 252px; vertical-align: bottom; position: relative;">
                                                                        <span class="font-bold chart-bar-value"
                                                                            style="position: absolute; bottom: {{ $value }}%; left: 0; width: 100%; text-align: center; color: {{ $color }}; font-size: 8px;">
                                                                            {{ $value }}
                                                                        </span>
                                                                        <div class="rounded-t-sm"
                                                                            style="height: {{ $value }}%; background-color: {{ $color }}; width: 20px; min-height: 2px;">
                                                                        </div>
                                                                    </td>
                                                                @endforeach
                                                            </tr>

                                                            {{-- Baris 2: Label NIM (Tepat di bawah tiap grup batang) --}}
                                                            <tr>
                                                                <td colspan="{{ count($finalSeries) }}"
                                                                    style="text-align: center; vertical-align: top;">
                                                                    <div
                                                                        style="transform: translateY(12px); font-size: 10px; width: 60px; margin: 0 auto;">
                                                                        <span class="text-black chart-nim-text">
                                                                            {{ $user->identity1 }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>


                </div>
            </div>
        @endforeach
    </div>
</div>
