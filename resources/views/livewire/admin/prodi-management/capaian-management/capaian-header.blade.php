{{--
    ============================================================
    GRID INFORMASI UTAMA PRODI — Versi Donut Mini
    Pola sama dengan mahasiswa-stats-grid.
    ============================================================
--}}

@php
    $akred = $prodi_data['akreditas_pr'] ?? 'E';

    [$akredAccent, $akredRgb] = match ($akred) {
        'A'        => ['#06b6d4', [6,182,212]],
        'A-'       => ['#22c55e', [34,197,94]],
        'B+'       => ['#10b981', [16,185,129]],
        'B'        => ['#eab308', [234,179,8]],
        'B-'       => ['#f59e0b', [245,158,11]],
        'C+', 'C'  => ['#f97316', [249,115,22]],
        default    => ['#ef4444', [239,68,68]],
    };
    $akredSoftBg = 'rgba(' . implode(',', $akredRgb) . ',0.12)';

    $targetSks = (int) ($prodi_data['target_sks'] ?? 144);
    $cplValue  = (float) ($prodi_data['rekap_pr'] ?? 0);
    $ikRaw     = $prodi_data['index_pr'] ?? 0;
    $ikValue   = is_numeric($ikRaw) ? (float) $ikRaw : 0;
    $mkValue   = (int) ($prodi_data['count_mk'] ?? 0);
    $rpsValue  = (int) ($prodi_data['count_rps_aktif'] ?? 0);
@endphp

<div class="md:px-6 lg:px-8 xl:px-12 grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-4 gap-y-9 bg-[var(--main-pop-up-color)]/90 p-6 rounded-xl border table-border shadow-sm">

    {{-- 1. RATA-RATA CPL — donut ring /100 --}}
    @include('livewire.global.statistik.donut-mini-stats', [
        'icon'      => 'chart-bar',
        'title'     => 'Rata-rata CPL',
        'sub'       => 'Capaian Pembelajaran Lulusan',
        'value'     => $cplValue,
        'max'       => 100,
        'display'   => $cplValue . ' / 100',
        'accent'    => '#10b981',
        'softBg'    => 'rgba(16,185,129,0.12)',
        'textColor' => '#10b981',
        'size'      => 64,
        'pctSize'   => 'text-sm',
    ])

    {{-- 2. INDEKS KINERJA — donut ring /4.00 --}}
    @include('livewire.global.statistik.donut-mini-stats', [
        'icon'      => 'trophy',
        'title'     => 'Indeks Kinerja (IK)',
        'sub'       => 'Rata-rata Indeks Prestasi Prodi',
        'value'     => $ikValue,
        'max'       => 4,
        'display'   => (is_numeric($ikRaw) ? number_format($ikRaw, 2) : $ikRaw) . ' / 4.00',
        'accent'    => 'var(--focus-color)',
        'softBg'    => 'color-mix(in srgb, var(--focus-color) 14%, transparent)',
        'textColor' => 'var(--focus-color)',
        'size'      => 64,
        'pctSize'   => 'text-sm',
    ])

    {{-- 3. TARGET SKS PRODI — tanpa ring, angka di tengah --}}
    {{-- @include('livewire.global.statistik.donut-mini-stats', [
        'icon'      => 'rectangle-stack',
        'title'     => 'Target SKS Prodi',
        'sub'       => 'Beban Kredit Kurikulum',
        'value'     => 0,
        'max'       => 0,
        'display'   => $targetSks . ' SKS',
        'accent'    => '#7c3aed',
        'softBg'    => 'rgba(124,58,237,0.12)',
        'textColor' => '#7c3aed',
        'size'      => 64,
        'pctSize'   => 'text-sm',
    ]) --}}

    {{-- 4. MK / RPS AKTIF — tanpa ring, angka MK di tengah --}}
    @include('livewire.global.statistik.donut-mini-stats', [
        'icon'      => 'book-open',
        'title'     => 'Mata Kuliah / RPS Aktif',
        'sub'       => "Taerget $targetSks SKS",
        'value'     => 0,
        'max'       => 0,
        'display'   => $mkValue . ' MK / ' . $rpsValue . ' RPS',
        'accent'    => 'var(--focus-color)',
        'softBg'    => 'color-mix(in srgb, var(--focus-color) 14%, transparent)',
        'textColor' => 'var(--focus-color)',
        'size'      => 64,
        'pctSize'   => 'text-sm',
        'centerText'=> (string) $mkValue,
    ])

    {{-- 5. AKREDITASI — tanpa ring, huruf di tengah --}}
    @include('livewire.global.statistik.donut-mini-stats', [
        'icon'      => 'academic-cap',
        'title'     => 'Akreditasi Prodi',
        'sub'       => 'Status Mutu Akademik',
        'value'     => 0,
        'max'       => 0,
        'display'   => $akred,
        'accent'    => $akredAccent,
        'softBg'    => $akredSoftBg,
        'textColor' => $akredAccent,
        'size'      => 64,
        'pctSize'   => 'text-sm',
    ])

</div>