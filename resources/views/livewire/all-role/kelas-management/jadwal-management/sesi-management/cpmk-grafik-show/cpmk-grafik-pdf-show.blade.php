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
    foreach ($groupsCpmk as $kodeCpmk => $pertemuans) {
        $bobotMentah = collect($pertemuans)->sum('bobot');
        $bobotNormalisasi = ($bobotMentah / $globalTotalBobotMentah) * 100;
        $bobotCpmkLegend[$kodeCpmk] = number_format($bobotNormalisasi, 2, '.', '');
    }

    $finalSeries = array_values($seriesData);
    $colorPalette = ['#3b82f6', '#8b5cf6', '#ef4444', '#f59e0b', '#10b981', '#ec4899', '#14b8a6'];
    $totalMahasiswa = count($daftarNim);
    $calculatedWidth = max($totalMahasiswa * 160, 800);

@endphp

<div class="rps-pdf bg-white">
@php
    $jumlahSeries = count($finalSeries);
    $barGroupWidth = ($jumlahSeries * 20) + (max($jumlahSeries - 1, 0) * 2);
@endphp

<div class="w-full">
    @foreach ($chunks as $chunk)
        <div class="page-container w-full" style="height: 10vh;">

            <div class="flex items-start justify-between border-b border-gray-100 dark:border-zinc-800 px-5 py-4 relative">
                <div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-zinc-100 tracking-wide">
                        Distribusi Capaian Nilai per Mahasiswa
                    </h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Grafik perbandingan evaluasi CPMK antar mahasiswa</p>
                </div>

                <div class="text-right">
                    <span class="block text-[11px] font-bold text-gray-800 dark:text-zinc-100">
                        {{ $namaKelas ?? 'Kelas Tidak Diketahui' }}
                    </span>
                    <span class="block text-[10px] text-gray-500 uppercase tracking-wider">
                        Semester {{ $semester ?? '-' }}
                    </span>
                </div>
            </div>

            <div class="pt-6">

                {{-- PEMBUNGKUS GRAFIK --}}
                <div class="relative" style="height: 320px; padding-top: 68px;">

                    {{-- Legend CPMK - pojok kanan atas --}}
                    <div class="absolute top-0 right-0 flex items-center gap-4 bg-gray-50 dark:bg-zinc-950/50 px-4 py-2 rounded-lg border border-gray-100 dark:border-zinc-800 z-30">
                        @foreach ($daftarCpmk as $index => $kodeCpmk)
                            @php
                                $currentColor = $colorPalette[$index % count($colorPalette)];
                                $bobot = $bobotCpmkLegend[$kodeCpmk] ?? 0;
                            @endphp
                            <div class="flex items-center gap-1.5">
                                <div class="w-2.5 h-2.5 rounded-sm shrink-0" style="background-color: {{ $currentColor }};"></div>
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-bold text-gray-700 dark:text-gray-200 leading-none">{{ $kodeCpmk }}</span>
                                    <span class="text-[9px] text-gray-500 font-medium leading-none mt-0.5">{{ $bobot }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex h-full">

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
                            <div class="relative flex flex-wrap items-end justify-start h-full border-b border-l border-gray-300 gap-8 pl-3 pr-2">

                                {{-- Garis kisi per 10% --}}
                                @for ($i = 10; $i <= 100; $i += 10)
                                    <div class="absolute left-0 w-full border-t border-gray-100 z-0" style="bottom: {{ $i }}%;"></div>
                                @endfor

                                {{-- Garis Batas Kelulusan 70% --}}
                                <div class="absolute left-0 w-full z-50" style="bottom: 70%;">
                                    <div class="w-full border-t-2 border-dashed" style="border-color: #ef4444;"></div>
                                    <span class="absolute right-0 -top-[14px] text-[9px] font-bold px-1.5 py-0.5 rounded bg-white"
                                        style="color: #ef4444; border: 1px solid #ef4444;">
                                        Batas Kelulusan 70%
                                    </span>
                                </div>

                                {{-- Bar per mahasiswa --}}
                                @foreach ($chunk as $user)
                                    @php
                                        $nim = $user->identity1;
                                        $idxGlobal = array_search($nim, $daftarNim);
                                    @endphp

                                    <div class="relative flex items-end gap-0.5 h-full justify-center shrink-0 z-20">
                                        @foreach ($finalSeries as $idxSeries => $series)
                                            @php
                                                $value = $series['data'][$idxGlobal] ?? 0;
                                                $color = $colorPalette[$idxSeries % count($colorPalette)];
                                            @endphp
                                            <div class="flex flex-col items-center justify-end h-full">
                                                <span class="text-[7px] font-bold mb-0.5 leading-none whitespace-nowrap"
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
                    @foreach ($chunk as $user)
                        <div class="flex justify-center shrink-0" style="width: {{ $barGroupWidth }}px;">
                            <span class="text-[8px] text-black block rotate-45 origin-top-left" style="width: 60px;">
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
