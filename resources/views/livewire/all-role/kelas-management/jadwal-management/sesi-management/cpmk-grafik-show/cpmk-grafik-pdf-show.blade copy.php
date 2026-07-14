<style>
    @page {
        size: A4 landscape;
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
<div class="fixed bottom-[0.5cm] right-[0.5cm] z-[999] flex items-center gap-5">
    <div class="flex flex-col items-end leading-none gap-1">
        <span class="text-[8px] font-semibold text-gray-500">
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
        <span class="text-[8px] font-semibold text-gray-400">
            {{ $univ }}
        </span>

    </div>

    @if ($logoBase64 ?? null)
        <img src="{{ $logoBase64 }}" class="h-9 object-contain">
    @else
        <img src="{{ asset('images/logo-unsri.webp') }}" class="h-9 object-contain">
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

                <div
                    class="flex items-start justify-between border-b border-gray-100 dark:border-zinc-800 px-5 py-4 relative">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 dark:text-zinc-100 tracking-wide">
                            Distribusi Capaian Nilai per Mahasiswa {{ $angkatan }}
                        </h3>
                        <p class="text-[11px] text-gray-600 mt-0.5">Mata Kuliah {{ $rps->mk_rel->nama_mk }}
                            | Semester {{ $rps->mk_rel->semester }}
                            | {{ $rps->mk_rel->sks }} SKS
                            | {{ $rps->mk_rel->sks_text }}
                        </p>
                    </div>

                    @if ($jadwal)
                        <div class="text-right">
                            <span class="block text-[11px] font-bold text-gray-800 dark:text-zinc-100">
                                {{ $jadwal->kode ?? 'Kelas Tidak Diketahui' }} {{ $tahun_akademik }}
                            </span>
                            <span class="block text-[10px] text-gray-500 uppercase tracking-wider">
                                Kode RPS: {{ $rps->kode ?? '---- -- ----' }}
                            </span>
                            <span class="block text-[10px] text-gray-500 uppercase tracking-wider">
                                {{ $jadwal->kelas_rel->pr_rel->prodi ?? '-- -----' }}
                            </span>
                        </div>
                    @else
                        <div class="text-right">
                            <span class="block text-[11px] font-bold text-gray-800 dark:text-zinc-100">
                                Kode RPS: {{ $rps->kode ?? '---- -- ----' }}
                            </span>

                            @php
                                $segments = array_filter([$pr_name, $dp_name]);
                            @endphp

                            @if (!empty($segments))
                                <span class="block text-[10px] text-gray-500 uppercase tracking-wider">
                                    {{ implode(' | ', $segments) }}
                                </span>
                            @endif

                            @if ($fk_name)
                                <span class="block text-[10px] text-gray-500 uppercase tracking-wider">
                                    {{ $fk_name }}
                                </span>
                            @endif
                        </div>
                    @endif

                </div>

                <div class="pt-6">

                    {{-- PEMBUNGKUS GRAFIK --}}
                    <div class="relative" style="height: 320px; padding-top: 68px;">

                        @php
                            $count = count($daftarCpmk);
                            $marginClass = match (true) {
                                $count >= 13 => 'mt-20', // 13-18
                                $count >= 7 => 'mt-10', // 7-12
                                default => 'mt-0', // 1-6
                            };
                        @endphp

                        <div
                            class="absolute top-0 right-0 bg-gray-50 dark:bg-zinc-950/50 px-4 py-3 rounded-lg border border-gray-100 dark:border-zinc-800 z-30">
                            <div class="grid grid-cols-6 gap-x-6 gap-y-4">
                                @foreach ($daftarCpmk as $index => $kodeCpmk)
                                    {{-- Isi foreach Anda ... --}}
                                    <div class="flex items-center gap-1.5 min-w-[120px]">
                                        <div class="w-3 h-3 rounded-sm shrink-0"
                                            style="background-color: {{ $colorPalette[$index % count($colorPalette)] }};">
                                        </div>
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[9px] font-bold text-gray-700 dark:text-gray-200 leading-none">{{ $kodeCpmk }}</span>
                                            <span
                                                class="text-[7px] text-gray-500 font-medium leading-none mt-0.5">Bobot:
                                                {{ $bobotCpmkLegend[$kodeCpmk] ?? 0 }}% | Tercapai:
                                                {{ $rataKeberhasilanCpmk[$kodeCpmk] ?? 0 }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Div bawah dengan margin dinamis --}}
                        <div class="flex h-full {{ $marginClass }}">

                            {{-- Kolom sumbu-Y --}}
                            <div class="relative shrink-0" style="width: 20px; height: 100%;">
                                @for ($i = 0; $i <= 100; $i += 10)
                                    <span class="absolute right-1 text-[7px] text-gray-400 leading-none"
                                        style="bottom: {{ $i }}%; transform: translateY(50%);">
                                        {{ $i }}
                                    </span>
                                @endfor
                            </div>

                            {{-- Wrapper area chart — TANPA padding di sini --}}
                            <div class="relative flex-1" style="height: 100%;">

                                {{-- Border + padding sekarang menyatu di div ini, border tidak ikut geser --}}
                                <div
                                    class="relative flex flex-wrap items-end justify-start h-full border-b border-l border-gray-300 gap-8 pl-3 pr-2">

                                    {{-- Garis kisi per 10% --}}
                                    @for ($i = 10; $i <= 100; $i += 10)
                                        <div class="absolute left-0 w-full border-t border-gray-100 z-0"
                                            style="bottom: {{ $i }}%;"></div>
                                    @endfor

                                    {{-- Garis Batas Kelulusan 70% --}}
                                    <div class="absolute left-0 w-full z-50" style="bottom: 70%;">
                                        <div class="w-full border-t-2 border-dashed" style="border-color: #ef4444;">
                                        </div>
                                        <span
                                            class="absolute right-0 -top-[14px] text-[9px] font-bold px-1.5 py-0.5 rounded bg-white"
                                            style="color: #ef4444; border: 1px solid #ef4444;">
                                            Batas Kelulusan 70%
                                        </span>
                                    </div>

                                    {{-- Bar per mahasiswa --}}
                                    @foreach ($chunk_user as $user)
                                        @php
                                            $nim = $user->identity1;
                                            $idxGlobal = array_search($nim, $daftarNim);
                                        @endphp

                                        <div
                                            class="relative flex items-end gap-0.5 h-full justify-center shrink-0 z-20">
                                            @foreach ($finalSeries as $idxSeries => $series)
                                                @php
                                                    $value = $series['data'][$idxGlobal] ?? 0;
                                                    $color = $colorPalette[$idxSeries % count($colorPalette)];
                                                @endphp
                                                <div class="flex flex-col items-center justify-end h-full">
                                                    <span
                                                        class="text-[7px] font-bold mb-0.5 leading-none whitespace-nowrap"
                                                        style="color: {{ $color }};">
                                                        {{ $value }}
                                                    </span>
                                                    <div class="rounded-t-sm"
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
                    <div class="flex flex-wrap justify-start gap-8 mt-2" style="padding-left: 44px;">
                        @foreach ($chunk_user as $user)
                            <div class="flex justify-center shrink-0" style="width: {{ $barGroupWidth }}px;">
                                <span class="text-[8px] text-black block rotate-45 origin-top-left"
                                    style="width: 60px;">
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
