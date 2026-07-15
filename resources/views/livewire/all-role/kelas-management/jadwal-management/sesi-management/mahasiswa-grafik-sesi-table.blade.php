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
    // $index = 0;
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
                // $rasioBobotDiCpmk = $bobotCpmkArray[$originalIndex] ?? 0;
                // $skorMurniCpmk += $nilaiPertemuan * $rasioBobotDiCpmk;
                $rasioBobot = $pertemuan['bobot'] / 100;
                $skorMurniCpmk += $nilaiPertemuan * $rasioBobot;
            }
            $totalNilaiKontribusiCpmk = ($skorMurniCpmk / $bobotNormalisasiGlobalCpmk) * 100;
            $seriesData[$kodeCpmk]['data'][] = round($totalNilaiKontribusiCpmk, 1);
        }

        // if ($index === 0) {
        //     dump('Mapping:', $allMapping->toArray());
        //     dump('Nilai User:', $arrayNilai);
        //     dump('Bobot User:', $bobotCpmkArray);
        // }
        // $index++;
        // if ($nilaiPertemuan * $rasioBobotDiCpmk > 100) {
        //     dump('Anomali pada User: ' . $user->id);
        //     dump('Nilai: ' . $nilaiPertemuan);
        //     dump('Rasio Bobot: ' . $rasioBobotDiCpmk);
        // }
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

    // foreach ($groupsCpmk as $kodeCpmk => $pertemuans) {
    //     $bobotMentah = collect($pertemuans)->sum('bobot');
    //     $bobotNormalisasi = ($bobotMentah / $globalTotalBobotMentah) * 100;

    //     $bobotCpmkLegend[$kodeCpmk] = number_format($bobotNormalisasi, 2, '.', '');

    //     $semuaNilaiCpmk = $seriesData[$kodeCpmk]['data'];
    //     $totalMahasiswa = count($semuaNilaiCpmk);
    //     $totalSkorMahasiswa = array_sum($semuaNilaiCpmk);

    //     $rataKeberhasilanCpmk[$kodeCpmk] = $totalMahasiswa > 0 ? round($totalSkorMahasiswa / $totalMahasiswa, 1) : 0;

    //     // --- DUMP DIAGNOSTIK ---
    //     dump('Diagnostik untuk CPMK: ' . $kodeCpmk);
    //     dump([
    //         'Bobot Mentah CPMK' => $bobotMentah,
    //         'Global Total Bobot Mentah' => $globalTotalBobotMentah,
    //         'Bobot Normalisasi (Pembagi)' => $bobotNormalisasi,
    //         'Total Mahasiswa' => $totalMahasiswa,
    //         'Total Skor dari Semua Mahasiswa' => $totalSkorMahasiswa,
    //         'Rata-rata Keberhasilan (Hasil)' => $rataKeberhasilanCpmk[$kodeCpmk],
    //     ]);
    // }
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

<div
    class="my-6 w-full bg-white dark:bg-zinc-900 rounded-xl border-x border-gray-200 dark:border-zinc-800 shadow-xs overflow-hidden">
    <div
        class="overflow-x-auto scrollbar-medium flex items-center justify-between border-b border-gray-100 dark:border-zinc-800 px-5 py-4">
        <div class="shrink-0">
            <h3 class="text-sm font-bold text-gray-800 dark:text-zinc-100 tracking-wide whitespace-nowrap">Distribusi
                Capaian Nilai per
                Mahasiswa</h3>
            <p class="text-[11px] text-gray-500 mt-0.5 whitespace-nowrap">Kode RPS: {{ $kelas->kode_rps ?? $rps->kode }}
            </p>
        </div>

        <div
            class="ml-16 flex items-center gap-8 bg-gray-50 dark:bg-zinc-950/50 px-4 py-2 rounded-lg border border-gray-100 dark:border-zinc-800 shrink-0">
            <button class="cursor-pointer" wire:click="refreshCapaiansList"
                x-on:click="
                        $el.querySelector('svg').animate(
                            [
                                { transform: 'rotate(0deg)' },
                                { transform: 'rotate(720deg)' }
                            ],
                            {
                                duration: 600,
                                easing: 'cubic-bezier(.22,1,.36,1)'
                            }
                        );
                    ">
                <flux:icon name="arrow-path"
                    class="w-4 h-4 text-[var(--contrast-third-text)] hover:text-[var(--focus-color)] transition-colors duration-200" />
            </button>
            @foreach ($daftarCpmk as $index => $kodeCpmk)
                @php
                    $currentColor = $colorPalette[$index % count($colorPalette)];
                    $bobot = $bobotCpmkLegend[$kodeCpmk] ?? 0;
                    $keberhasilan = $rataKeberhasilanCpmk[$kodeCpmk] ?? 0;
                @endphp

                <div class="flex items-center gap-3 shrink-0">
                    <div class="w-4 h-4 rounded-sm shrink-0" style="background-color: {{ $currentColor }};"></div>
                    <div class="flex flex-col">
                        <span
                            class="text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200 leading-none whitespace-nowrap">
                            {{ $kodeCpmk }}
                        </span>
                        <span
                            class="text-[9px] sm:text-xs text-gray-500 font-medium leading-none mt-1 whitespace-nowrap">
                            Bobot: {{ $bobot }}% <span class="mx-1">|</span> Tercapai: {{ $keberhasilan }}%
                        </span>
                    </div>
                </div>
            @endforeach
            <div class="flex-shrink-0">
                @include('livewire.global.table.export-button', [
                    'nameXString' => 'Export Grafik',
                    'xString' =>
                        'printPDFCpmkGrafik(' . ($jadwal_id_url ?? $rps_id_url) . ', ' . ($isRPS ?? false) . ')',
                    'icon' => 'arrow-down-tray',
                    'isFull' => 1,
                    'valuePx' => 'px-6',
                    'valuePy' => 'py-2.5',
                    'color' => 'rose',
                    'wireLoading' => 'printPDFCpmkGrafik()',
                ])
            </div>
        </div>
    </div>

    <div class="w-full min-h-90 overflow-x-auto p-4 scrollbar-large"
        wire:key="chart-{{ $search ?? '' }}-{{ $searchAngkatan ?? '' }}-{{ $searchMode ?? '' }}-{{ $perPage ?? '' }}-{{ $sortField ?? '' }}-{{ $sortDirection ?? '' }}-{{ $switchTable ?? '' }}-{{ $filterStatus ?? '' }}-{{ $filterAngkatan ?? '' }}-{{ $refreshTrigger ?? '' }}">

        <div style="width: {{ $calculatedWidth }}px;" x-data="{
            chart: null,
            initChart() {
                if (this.chart) this.chart.destroy();
        
                const isDark = () =>
                    document.documentElement.classList.contains('dark');
        
        
                let options = {
                    series: {{ json_encode($finalSeries) }},
                    chart: {
                        type: 'bar',
                        height: 360,
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
                            colors: ['#e4e4e7']
                            {{-- colors: [isDark() ? '#e4e4e7' : '#18181b'] --}}
                        }
                    },
                    xaxis: {
                        categories: {{ json_encode($daftarNim) }},
                        labels: {
                            style: {
                                colors: isDark() ? '#a1a1aa' : '#3f3f46',
                                fontSize: '11px',
                                fontWeight: 'bold'
                            }
                        },
                        axisBorder: {
                            show: true,
                            color: isDark() ? '#3f3f46' : '#9ca3af'
                        },
                        axisTicks: {
                            show: true,
                            color: isDark() ? '#3f3f46' : '#9ca3af'
                        }
                    },
                    yaxis: {
                        min: 0,
                        max: 100,
                        tickAmount: 5,
        
                        labels: {
                            style: {
                                colors: isDark() ? '#a1a1aa' : '#3f3f46',
                                fontSize: '10px'
                            }
                        },
        
                        axisBorder: {
                            show: true,
                            color: isDark() ? '#3f3f46' : '#9ca3af'
                        }
                    },
                    grid: {
                        show: true,
                        borderColor: isDark() ? '#3f3f46' : '#e4e4e7',
                        strokeDashArray: 0,
                        xaxis: { lines: { show: true } }
                    },
                    legend: {
                        labels: {
                            colors: '[var(--contrast-main-text)]'
                        }
                    },
                    annotations: {
                        yaxis: [{
                            y: 70,
                            borderColor: '#ef4444',
                            strokeDashArray: 4,
                            label: {
                                borderColor: '#ef4444',
                                style: { color: '#fff', background: '#ef4444', fontSize: '9px', fontWeight: 700 },
                                text: 'Batas Kelulusan (70%)'
                            }
                        }]
                    }
                };
        
                this.chart = new ApexCharts($refs.canvas, options);
                this.chart.render();
        
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.attributeName === 'class') {
                            this.chart.updateOptions({
                                xaxis: {
                                    categories: {{ json_encode($daftarNim) }},
                                    labels: {
                                        style: {
                                            colors: isDark() ? '#a1a1aa' : '#3f3f46',
                                            fontSize: '11px',
                                            fontWeight: 'bold'
                                        }
                                    },
                                    axisBorder: {
                                        show: true,
                                        color: isDark() ? '#3f3f46' : '#9ca3af'
                                    },
                                    axisTicks: {
                                        show: true,
                                        color: isDark() ? '#3f3f46' : '#9ca3af'
                                    }
                                },
        
                                yaxis: {
                                    min: 0,
                                    max: 100,
                                    tickAmount: 5,
        
                                    labels: {
                                        style: {
                                            colors: isDark() ? '#a1a1aa' : '#3f3f46',
                                            fontSize: '10px'
                                        }
                                    },
        
                                    axisBorder: {
                                        show: true,
                                        color: isDark() ? '#3f3f46' : '#9ca3af'
                                    }
                                },
        
                                grid: {
                                    borderColor: isDark() ? '#3f3f46' : '#e4e4e7'
                                }
                            }, false, false);
                        }
                    });
                });
        
                observer.observe(document.documentElement, { attributes: true });
            }
        }" x-init="(async () => {
            while (typeof window.ApexCharts === 'undefined') {
                await new Promise(resolve => setTimeout(resolve, 50));
            }
        
            initChart();
        })();"
            x-effect="
                $wire.$watch('finalSeries', () => initChart());
                $wire.$watch('daftarNim', () => initChart());
            ">
            <div x-ref="canvas"></div>
        </div>

    </div>
</div>
