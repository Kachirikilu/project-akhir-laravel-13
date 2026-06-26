@php
    // 1. SIAPKAN DATA GLOBAL (MENGGABUNGKAN SEMUA MAHASISWA)
    $allMapping = collect($this->mapping_pertemuan)->values();
    $globalTotalBobotMentah = collect($groupsCpmk)->map(fn($p) => collect($p)->sum('bobot'))->sum() ?: 1;
    
    // 🌟 FIX: Ubah ke array murni menggunakan ->keys()->toArray() agar aman dari error tipe data
    $daftarCpmk = collect($groupsCpmk)->keys()->toArray();
    
    // Struktur data untuk ApexCharts Grouped Bar
    $seriesData = [];
    foreach ($daftarCpmk as $kode) {
        $seriesData[$kode] = [
            'name' => $kode,
            'data' => []
        ];
    }
    $daftarNim = [];

    // Loop semua user untuk mengisi data per-CPMK
    foreach($users as $user) {
        $daftarNim[] = $user->identity1; // Simpan NIM untuk label bawah
        
        $arrayNilai = is_array($user->mhs_nilai_array) ? $user->mhs_nilai_array : json_decode($user->mhs_nilai_array ?? '[]', true);
        $bobotCpmkArray = is_array($user->mhs_bobot_array) ? $user->mhs_bobot_array : json_decode($user->mhs_bobot_array ?? '[]', true);

        foreach ($groupsCpmk as $kodeCpmk => $pertemuans) {
            $skorMurniCpmk = 0;
            foreach ($pertemuans as $pertemuan) {
                $originalIndex = $allMapping->search(fn($item) => 
                    $item['kode_scpmk'] === $pertemuan['kode_scpmk'] && $item['kode_cpmk'] === $pertemuan['kode_cpmk']
                );
                $nilaiPertemuan = $arrayNilai[$originalIndex] ?? 0;
                $rasioBobotDiCpmk = $bobotCpmkArray[$originalIndex] ?? 0;
                $skorMurniCpmk += $nilaiPertemuan * $rasioBobotDiCpmk;
            }
            $seriesData[$kodeCpmk]['data'][] = round($skorMurniCpmk, 1);
        }
    }

    // Ubah asosiatif ke indeks array numerik agar dibaca ApexCharts
    $finalSeries = array_values($seriesData);
    $colorPalette = ['#3b82f6', '#8b5cf6', '#ef4444', '#f59e0b', '#10b981', '#ec4899', '#14b8a6'];
    
    // Hitung lebar responsif: makin banyak mahasiswa, canvas grafik makin melar ke kanan agar tidak gepeng
    $totalMahasiswa = count($daftarNim);
    $calculatedWidth = max($totalMahasiswa * 160, 800); // Minimal lebar 800px, bertambah 160px per mhs
@endphp

<div class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden">
    
    <div class="flex items-center justify-between border-b border-gray-100 dark:border-zinc-800 px-5 py-4">
        <div>
            <h3 class="text-sm font-bold text-gray-800 dark:text-zinc-100 tracking-wide">Distribusi Capaian Nilai per Mahasiswa</h3>
            <p class="text-[11px] text-gray-400 mt-0.5">Grafik perbandingan evaluasi CPMK antar mahasiswa</p>
        </div>
        
        <div class="flex items-center gap-3 bg-gray-50 dark:bg-zinc-950/50 px-3 py-1.5 rounded-lg border border-gray-100 dark:border-zinc-800">
            @foreach ($daftarCpmk as $index => $kodeCpmk)
                @php $currentColor = $colorPalette[$index % count($colorPalette)]; @endphp
                <div class="flex items-center gap-1.5">
                    <div class="w-2.5 h-2.5 rounded-sm shrink-0" style="background-color: {{ $currentColor }};"></div>
                    <span class="text-[11px] font-bold text-gray-600 dark:text-gray-300">{{ $kodeCpmk }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="w-full overflow-x-auto p-4 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-zinc-700" wire:ignore>
        
        <div style="width: {{ $calculatedWidth }}px;" 
             x-data="{
                init() {
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
                                columnWidth: '80%', // Bikin 3 CPMK sedempet mungkin
                                barHeight: '100%',
                                borderRadius: 3,
                                dataLabels: { position: 'top' }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function (val) { return val; },
                            offsetY: -18,
                            style: { fontSize: '10px', fontWeight: '700', colors: [isDark ? '#e4e4e7' : '#374151'] }
                        },
                        stroke: {
                            show: true,
                            width: 1, // Border tipis pemisah antar balok CPMK
                            colors: [isDark ? '#18181b' : '#ffffff']
                        },
                        legend: { show: false }, // Legenda sudah dipindah ke atas manual
                        xaxis: {
                            categories: {{ json_encode($daftarNim) }}, // NIM di bawah kelompok bar
                            labels: { 
                                style: { colors: isDark ? '#a1a1aa' : '#4b5563', fontSize: '11px', fontWeight: 'bold' } 
                            },
                            axisBorder: { show: true, color: isDark ? '#3f3f46' : '#d1d5db' },
                            axisTicks: { show: true, color: isDark ? '#3f3f46' : '#d1d5db' }
                        },
                        yaxis: {
                            max: 100,
                            min: 0, // 🌟 HAPUS SATUAN MINUS (Kunci dasar mulai dari 0)
                            tickAmount: 5,
                            labels: { 
                                style: { colors: isDark ? '#a1a1aa' : '#6b7280', fontSize: '10px' } 
                            },
                            axisBorder: { show: true, color: isDark ? '#3f3f46' : '#d1d5db' }
                        },
                        grid: {
                            show: true,
                            borderColor: isDark ? '#3f3f46' : '#9ca3af', // 🌟 GARIS GRAFIK LEBIH DIPERTEGAS
                            strokeDashArray: 0, // Garis lurus tegas solid (bukan putus-putus)
                            xaxis: {
                                lines: { show: true } // Beri border tipis vertikal pemisah antar kelompok mahasiswa
                            },
                            padding: { top: 20, right: 10, bottom: 0, left: 10 }
                        },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light',
                            shared: true, // Arahkan kursor langsung memunculkan info semua CPMK mhs tersebut
                            intersect: false
                        },
                        annotations: {
                            yaxis: [{
                                y: 70,
                                borderColor: '#ef4444',
                                strokeDashArray: 4,
                                width: '100%',
                                label: {
                                    borderColor: '#ef4444',
                                    style: { color: '#fff', background: '#ef4444', fontSize: '9px', fontWeight: 700 },
                                    text: 'Batas Kelulusan (70%)'
                                }
                            }]
                        }
                    };

                    let chart = new ApexCharts($refs.canvas, options);
                    chart.render();
                }
             }">
            <div x-ref="canvas"></div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
