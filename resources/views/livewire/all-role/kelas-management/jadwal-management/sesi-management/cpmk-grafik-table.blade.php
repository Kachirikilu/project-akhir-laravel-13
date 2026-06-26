@php
    $allMapping = collect($this->mapping_pertemuan)->values();
    // Hitung bobot total untuk normalisasi global agar total semua CPMK = 100%
    $globalTotalBobotMentah = collect($groupsCpmk)->map(fn($p) => collect($p)->sum('bobot'))->sum() ?: 1;
    $daftarCpmk = collect($groupsCpmk)->keys()->toArray();

    // Siapkan data untuk grafik
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

            // Hitung bobot normalisasi untuk CPMK ini saja
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
            // Hitung kontribusi akhir: (skor murni / bobot normalisasi) * 100
            $totalNilaiKontribusiCpmk = ($skorMurniCpmk / $bobotNormalisasiGlobalCpmk) * 100;
            // Masukkan ke series data
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

<div
    class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">

    <div class="flex items-center justify-between border-b border-gray-100 dark:border-zinc-800 px-5 py-4">
        <div>
            <h3 class="text-sm font-bold text-gray-800 dark:text-zinc-100 tracking-wide">Distribusi Capaian Nilai per
                Mahasiswa</h3>
            <p class="text-[11px] text-gray-400 mt-0.5">Grafik perbandingan evaluasi CPMK antar mahasiswa</p>
        </div>

        <div
            class="flex items-center gap-4 bg-gray-50 dark:bg-zinc-950/50 px-4 py-2 rounded-lg border border-gray-100 dark:border-zinc-800">
            @foreach ($daftarCpmk as $index => $kodeCpmk)
                @php
                    $currentColor = $colorPalette[$index % count($colorPalette)];
                    $bobot = $bobotCpmkLegend[$kodeCpmk] ?? 0;
                @endphp
                <div class="flex items-center gap-1.5">
                    <div class="w-2.5 h-2.5 rounded-sm shrink-0" style="background-color: {{ $currentColor }};"></div>
                    <div class="flex flex-col">
                        <span class="text-[11px] font-bold text-gray-700 dark:text-gray-200 leading-none">
                            {{ $kodeCpmk }}
                        </span>
                        <span class="text-[9px] text-gray-500 font-medium leading-none mt-0.5">
                            {{ $bobot }}%
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="w-full overflow-x-auto p-4 scrollbar-large" wire:ignore>

        <div style="width: {{ $calculatedWidth }}px;" x-data="{
            chart: null,
            initChart() {
                if (this.chart) this.chart.destroy();
        
                let isDark = document.documentElement.classList.contains('dark');
                let options = {
                    series: {{ json_encode($finalSeries) }},
                    chart: {
                        type: 'bar',
                        height: 320,
                        toolbar: { show: false },
                        parentHeightOffset: 0,
                        fontFamily: 'Inter, sans-serif'
                    },
                    colors: {{ json_encode($colorPalette) }},
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '80%',
                            barHeight: '100%',
                            borderRadius: 3,
                            dataLabels: { position: 'top' }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '10px',
                            fontWeight: '700',
                            // Ubah agar kontras: Putih di mode gelap, Abu Tua di mode terang
                            colors: [isDark ? '#e4e4e7' : '#18181b']
                        }
                    },
                    xaxis: {
                        categories: {{ json_encode($daftarNim) }},
                        labels: {
                            style: {
                                colors: isDark ? '#a1a1aa' : '#3f3f46', // Lebih gelap agar terbaca di terang
                                fontSize: '11px',
                                fontWeight: 'bold'
                            }
                        },
                        axisBorder: { show: true, color: isDark ? '#3f3f46' : '#9ca3af' },
                        axisTicks: { show: true, color: isDark ? '#3f3f46' : '#9ca3af' }
                    },
                    yaxis: {
                        max: 100,
                        min: 0,
                        tickAmount: 5,
                        labels: {
                            style: {
                                colors: isDark ? '#a1a1aa' : '#3f3f46', // Sama, buat lebih tegas
                                fontSize: '10px'
                            }
                        },
                        axisBorder: { show: true, color: isDark ? '#3f3f46' : '#9ca3af' }
                    },
                    grid: {
                        show: true,
                        borderColor: isDark ? '#3f3f46' : '#e4e4e7', // Gunakan abu-abu sangat muda di mode terang
                        strokeDashArray: 0,
                        xaxis: { lines: { show: true } }
                    },
                    annotations: {
                        yaxis: [{
                            y: 70,
                            borderColor: '#ef4444',
                            strokeDashArray: 4,
                            label: {
                                borderColor: '#ef4444',
                                // Label ini akan selalu putih karena background merah, ini sudah oke
                                style: { color: '#fff', background: '#ef4444', fontSize: '9px', fontWeight: 700 },
                                text: 'Batas Kelulusan (70%)'
                            }
                        }]
                    }
                };
        
                this.chart = new ApexCharts($refs.canvas, options);
                this.chart.render();
            }
        }" x-init="$nextTick(() => initChart())"
            x-effect="
                $wire.$watch('finalSeries', () => initChart());
                $wire.$watch('daftarNim', () => initChart());
            ">
            <div x-ref="canvas"></div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
